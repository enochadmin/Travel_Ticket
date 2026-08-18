<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        if ($user->hasRole('reception')) {
            return redirect()->route('reception.dashboard');
        }

        if ($user->hasRole('admin')) {
            $data['totalUsers'] = User::count();
            $data['totalProjects'] = Project::count();
            $data['activeProjects'] = Project::where('status', 'active')->count();
            $data['unassignedUsers'] = User::whereNull('project_id')->count();

            // 1. Users by Role (Role -> users count)
            $usersByRole = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('roles.name', DB::raw('count(model_has_roles.model_id) as total'))
                ->where('model_has_roles.model_type', User::class)
                ->groupBy('roles.name')
                ->get();
            $data['roleChartLabels'] = $usersByRole->pluck('name')->map(fn($n) => ucfirst(str_replace('-', ' ', $n)))->toArray();
            $data['roleChartData'] = $usersByRole->pluck('total')->toArray();

            // 2. Projects by Discipline
            $projectsByDiscipline = Project::select('discipline', DB::raw('count(*) as total'))
                ->whereNotNull('discipline')
                ->groupBy('discipline')
                ->get();
            $data['disciplineChartLabels'] = $projectsByDiscipline->pluck('discipline')->toArray();
            $data['disciplineChartData'] = $projectsByDiscipline->pluck('total')->toArray();

            // 3. Projects by Status
            $projectsByStatus = Project::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();
            $data['statusChartLabels'] = $projectsByStatus->pluck('status')->map(fn($s) => ucfirst($s))->toArray();
            $data['statusChartData'] = $projectsByStatus->pluck('total')->toArray();

            // 4. Users per Project
            $usersPerProject = User::with('project')
                ->select('project_id', DB::raw('count(*) as total'))
                ->whereNotNull('project_id')
                ->groupBy('project_id')
                ->get()
                ->sortByDesc('total'); // Sort naturally by volume

            $data['projectUsersChartLabels'] = $usersPerProject->map(fn($u) => optional($u->project)->name ?? 'Unknown')->values()->toArray();
            $data['projectUsersChartData'] = $usersPerProject->pluck('total')->values()->toArray();

            // Feed: Latest Users (or Unassigned Users if needed)
            $data['latestUsers'] = User::with(['roles', 'project'])->latest()->take(5)->get();

        } elseif ($user->hasRole('ceo')) {
            $data['totalProjects'] = Project::count();
            $data['totalRequests'] = TravelRequest::count();
            $data['approved'] = TravelRequest::where('status', 'approved')->count();
            $data['rejected'] = TravelRequest::where('status', 'rejected')->count();

            // Fetch projects grouped by travel requests (eager load required relations)
            $projects = Project::with([
                'travelRequests' => function ($query) {
                    $query->latest();
                },
                'travelRequests.user',
                'travelRequests.pm',
                'travelRequests.hod',
            ])->get();

            $data['projects'] = $projects;

            // Generate Chart Data: Count travel requests per project, sorted to show most out of least
            $sortedProjects = $projects->sortByDesc(function ($project) {
                return $project->travelRequests->count();
            });

            // Ensure the collections are re-indexed (values) so json_encode produces JS arrays
            $data['ceoChartLabels'] = $sortedProjects->values()->pluck('name')->toArray();
            $data['ceoChartData'] = $sortedProjects->values()->map(function ($project) {
                return $project->travelRequests->count();
            })->toArray();

            // Overall status counts for CEO pie chart
            $data['pendingPm'] = TravelRequest::where('status', 'pending_pm')->count();
            $data['pendingCommercial'] = TravelRequest::where('status', 'pending_commercial')->count();
            $data['ceoStatusChartLabels'] = ['Approved', 'Rejected', 'Pending (PM)', 'Pending (Commercial)'];
            $data['ceoStatusChartData'] = [
                $data['approved'],
                $data['rejected'],
                $data['pendingPm'],
                $data['pendingCommercial'],
            ];

        } elseif ($user->hasRole('commercial-director')) {
            $data['approved'] = TravelRequest::where('status', 'approved')->count();
            $data['rejected'] = TravelRequest::where('status', 'rejected')->count();
            $data['pending'] = TravelRequest::whereIn('status', [
                'pending_pm',
                'pending_commercial',
                'pending_hod',
                'pending_ceo',
            ])->count();
            $data['totalRequests'] = TravelRequest::count();
            $data['dashboardTitle'] = 'Organization Overview';
            $data['dashboardSubtitle'] = 'Summary of all travel requests across the organization.';
            $data['summaryChartId'] = 'commercialSummaryChart';

            // Add trend data (current month vs last month)
            $now = now();
            $currentMonth = TravelRequest::whereBetween('travel_date', [
                $now->clone()->startOfMonth(),
                $now->clone()->endOfMonth()
            ])->count();
            
            $lastMonth = TravelRequest::whereBetween('travel_date', [
                $now->clone()->subMonth()->startOfMonth(),
                $now->clone()->subMonth()->endOfMonth()
            ])->count();

            $data['currentMonthRequests'] = $currentMonth;
            $data['lastMonthRequests'] = $lastMonth;
            $data['monthTrendPercent'] = $lastMonth > 0 
                ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 2)
                : ($currentMonth > 0 ? 100 : 0);
            $data['trendChartLabels'] = [
                $now->clone()->subMonth()->format('M Y'),
                $now->format('M Y'),
            ];
            $data['trendChartData'] = [$lastMonth, $currentMonth];

            // Top travelers — count only fully processed trips: approved tickets
            // that were processed/archived by Reception.
            $data['topTravelers'] = TravelRequest::selectRaw('user_id, COUNT(*) as trip_count')
                ->where('status', 'approved')
                ->whereNotNull('archived_at')
                ->groupBy('user_id')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(5)
                ->with('user')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'user_name' => optional($item->user)->name ?? 'Unknown',
                        'trip_count' => $item->trip_count,
                    ];
                });

            // Top destinations
            $data['topDestinations'] = TravelRequest::selectRaw('destination, COUNT(*) as count')
                ->whereNotNull('destination')
                ->where('destination', '!=', '')
                ->groupBy('destination')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(10)
                ->get();

            $data['destinationChartLabels'] = $data['topDestinations']->pluck('destination')->toArray();
            $data['destinationChartData'] = $data['topDestinations']->pluck('count')->toArray();

        } elseif ($user->hasRole('head-office-director')) {
            // Kept for backward compat, though commercial-director is replacing it
            $data['pendingHod'] = TravelRequest::where('status', 'pending_hod')->count();
            $data['approved'] = TravelRequest::where('status', 'approved')->count();
            $data['rejected'] = TravelRequest::where('status', 'rejected')->count();
            $data['hodQueue'] = TravelRequest::with(['user', 'project'])
                ->where('status', 'pending_hod')
                ->latest()->take(10)->get();

        } elseif ($user->hasRole('project-manager')) {
            $pmProjectId = $user->approverProjectId();
            $data['pmProject'] = $pmProjectId
                ? Project::find($pmProjectId)
                : null;

            $projectQuery = fn () => TravelRequest::where('project_id', $pmProjectId ?? -1);

            $data['approved'] = $projectQuery()->where('status', 'approved')->count();
            $data['rejected'] = $projectQuery()->where('status', 'rejected')->count();
            $data['pending'] = $projectQuery()->whereIn('status', [
                'pending_pm',
                'pending_commercial',
                'pending_hod',
                'pending_ceo',
            ])->count();
            $data['totalRequests'] = $projectQuery()->count();
            $data['dashboardTitle'] = $data['pmProject']?->name ?? 'My Project';
            $data['dashboardSubtitle'] = 'Summary of travel requests for your project team.';
            $data['summaryChartId'] = 'pmSummaryChart';

        } else {
            // Regular user / requester
            $data['myTotal'] = TravelRequest::where('user_id', $user->id)->count();
            $data['myPending'] = TravelRequest::where('user_id', $user->id)
                ->whereIn('status', ['pending_pm', 'pending_commercial', 'pending_hod', 'pending_ceo'])
                ->count();
            $data['myApproved'] = TravelRequest::where('user_id', $user->id)->where('status', 'approved')->count();
            $data['myRejected'] = TravelRequest::where('user_id', $user->id)->where('status', 'rejected')->count();
            $data['myRequests'] = TravelRequest::with('project')
                ->where('user_id', $user->id)
                ->latest()->take(8)->get();
        }

        return view('dashboard', $data);
    }
}
