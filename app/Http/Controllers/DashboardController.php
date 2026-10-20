<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * The only status the Commercial Director acts on. Tickets waiting on the PM, HOD
     * or CEO belong to someone else's queue and are deliberately excluded here.
     */
    private const PENDING_STATUS = 'pending_commercial';

    /** A ticket older than this many days is critical; at or above the second value is a warning. */
    private const AGE_CRITICAL_AFTER = 15;

    private const AGE_WATCH_AFTER = 10;

    public function index(Request $request)
    {
        $user = Auth::user();
        $data = [];

        if ($user->hasRole('reception')) {
            return redirect()->route('reception.dashboard');
        }

        // The pending-approval panel is rendered for every holder of the role, even
        // when another role's branch drives the rest of the dashboard (e.g. admin +
        // commercial-director), so the data is always initialised.
        $data = array_merge(
            $data,
            $user->hasRole('commercial-director')
                ? $this->pendingSnapshot($request)
                : $this->emptyPendingSnapshot()
        );

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
            $data['pending'] = TravelRequest::where('status', self::PENDING_STATUS)->count();
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

        } elseif ($user->hasAnyRole(['project-manager', 'head-office-manager'])) {
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
            // Regular user / requester — a small personal travel report
            $mine = fn () => TravelRequest::where('user_id', $user->id);

            $data['myTotal'] = $mine()->count();
            $data['myPending'] = $mine()
                ->whereIn('status', ['pending_pm', 'pending_commercial', 'pending_hod', 'pending_ceo'])
                ->count();
            $data['myApproved'] = $mine()->where('status', 'approved')->count();
            $data['myRejected'] = $mine()->where('status', 'rejected')->count();

            // Only approved tickets count as travel, so the report reflects trips actually taken.
            $approved = TravelRequest::with('project')
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereNotNull('travel_date')
                ->get(['id', 'user_id', 'project_id', 'destination', 'travel_date', 'status']);

            $data['travelTrend'] = $this->monthlyTrend($approved);
            $data['travelTrendTotal'] = (int) $data['travelTrend']->sum('count');

            $data['topDestinations'] = $approved
                ->groupBy(fn ($ticket) => trim((string) $ticket->destination) ?: 'Unknown')
                ->map(fn ($rows, $name) => ['name' => $name, 'count' => $rows->count()])
                ->sortByDesc('count')
                ->take(5)
                ->values();

            $data['projectShare'] = $approved
                ->groupBy(fn ($ticket) => $ticket->project?->name ?? 'No project')
                ->map(fn ($rows, $name) => ['name' => $name, 'count' => $rows->count()])
                ->sortByDesc('count')
                ->values();

            $today = now()->startOfDay();
            $tripDate = fn ($ticket) => Carbon::parse($ticket->travel_date)->startOfDay();

            $data['latestPastTrip'] = $approved
                ->filter(fn ($ticket) => $tripDate($ticket)->lte($today))
                ->sortByDesc(fn ($ticket) => $tripDate($ticket)->timestamp)
                ->first();

            $data['nextUpcomingTrip'] = $approved
                ->filter(fn ($ticket) => $tripDate($ticket)->gte($today))
                ->sortBy(fn ($ticket) => $tripDate($ticket)->timestamp)
                ->first();
        }

        return view('dashboard', $data);
    }

    /**
     * Commercial-review tickets grouped per project, optionally narrowed down by
     * project and travel/request date. Only tickets waiting for the Commercial
     * Director are counted, and everything is resolved from the dashboard query
     * string so the panel survives a refresh or a shared link.
     *
     * @return array<string, mixed>
     */
    private function pendingSnapshot(Request $request): array
    {
        $from = $this->dateQuery($request, 'pending_from');
        $to = $this->dateQuery($request, 'pending_to');

        $projectId = (int) $request->query('pending_project');
        $projectId = $projectId > 0 ? $projectId : null;

        $dateColumn = $request->query('pending_date_type') === 'created_at' ? 'created_at' : 'travel_date';

        $query = TravelRequest::query()
            ->where('status', self::PENDING_STATUS)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($from, fn ($q) => $q->whereDate($dateColumn, '>=', $from))
            ->when($to, fn ($q) => $q->whereDate($dateColumn, '<=', $to));

        $total = (clone $query)->count();

        // Age = days since the ticket was requested. It is the point of this panel,
        // so the buckets and the per-project ages are computed from the actual rows.
        $tickets = (clone $query)->get(['id', 'project_id', 'created_at', 'travel_date']);
        $today = now()->startOfDay()->timestamp;

        $ageInDays = static function ($createdAt) use ($today): int {
            if (! $createdAt) {
                return 0;
            }

            return max(0, (int) floor(($today - $createdAt->copy()->startOfDay()->timestamp) / 86400));
        };

        $projectNames = Project::query()
            ->whereIn('id', $tickets->pluck('project_id')->filter()->unique()->all())
            ->pluck('name', 'id');

        $byProject = $tickets
            ->groupBy(fn ($ticket) => (int) ($ticket->project_id ?? 0))
            ->map(function ($rows, $projectId) use ($projectNames, $ageInDays) {
                $ages = $rows->map(fn ($ticket) => $ageInDays($ticket->created_at))->all();
                $nextTravel = $rows->pluck('travel_date')->filter()->min();
                $oldestRequest = $rows->pluck('created_at')->filter()->min();

                return [
                    'id' => $projectId ?: null,
                    'name' => $projectId
                        ? (string) ($projectNames[$projectId] ?? 'Unknown project')
                        : 'No project assigned',
                    'total' => count($ages),
                    'oldest_days' => $ages ? max($ages) : null,
                    'avg_days' => $ages ? (int) round(array_sum($ages) / count($ages)) : null,
                    'buckets' => self::ageBuckets($ages),
                    'oldest_requested_at' => $oldestRequest ? Carbon::parse($oldestRequest) : null,
                    'next_travel' => $nextTravel ? Carbon::parse($nextTravel) : null,
                ];
            })
            ->sortBy([
                fn ($a, $b) => ($b['oldest_days'] ?? 0) <=> ($a['oldest_days'] ?? 0),
                fn ($a, $b) => $b['total'] <=> $a['total'],
            ])
            ->values();

        $allAges = $tickets->map(fn ($ticket) => $ageInDays($ticket->created_at))->all();

        $filtersActive = $from !== null
            || $to !== null
            || $projectId !== null
            || $dateColumn === 'created_at';

        return [
            'pendingFrom' => $from,
            'pendingTo' => $to,
            'pendingProject' => $projectId,
            'pendingDateType' => $dateColumn,
            'pendingTotal' => $total,
            'pendingGrandTotal' => TravelRequest::where('status', self::PENDING_STATUS)->count(),
            'pendingAging' => self::ageBuckets($allAges) + [
                'oldest_days' => $allAges ? max($allAges) : null,
                'avg_days' => $allAges ? (int) round(array_sum($allAges) / count($allAges)) : null,
            ],
            'pendingAgeThresholds' => ['critical' => self::AGE_CRITICAL_AFTER, 'watch' => self::AGE_WATCH_AFTER],
            'pendingProjects' => $byProject,
            'pendingFiltersActive' => $filtersActive,
            'pendingViewOpen' => $request->boolean('pending_view') || $filtersActive,
            'pendingProjectOptions' => Project::orderBy('name')->get(['id', 'name']),
        ];
    }

    /**
     * Approved trips per month for the trailing 12 months (oldest month first),
     * so a requester can see their own travel rhythm.
     *
     * @param  \Illuminate\Support\Collection<int, TravelRequest>  $tickets
     * @return \Illuminate\Support\Collection<int, array{label: string, period: string, count: int}>
     */
    private function monthlyTrend($tickets)
    {
        $start = now()->startOfMonth()->subMonths(11);

        return collect(range(0, 11))->map(function (int $offset) use ($start, $tickets) {
            $month = $start->copy()->addMonths($offset);
            $key = $month->format('Y-m');

            $count = $tickets->filter(
                fn ($ticket) => Carbon::parse($ticket->travel_date)->format('Y-m') === $key
            )->count();

            return [
                'label' => $month->format('M'),
                'period' => $month->format('M Y'),
                'count' => $count,
            ];
        });
    }

    /**
     * Split a list of ticket ages into the colour bands used on the dashboard:
     * under 10 days is healthy, 10–15 days needs a nudge, over 15 days is critical.
     *
     * @param  array<int, int>  $ages
     * @return array{critical: int, watch: int, fresh: int}
     */
    private static function ageBuckets(array $ages): array
    {
        $buckets = ['critical' => 0, 'watch' => 0, 'fresh' => 0];

        foreach ($ages as $age) {
            if ($age > self::AGE_CRITICAL_AFTER) {
                $buckets['critical']++;
            } elseif ($age >= self::AGE_WATCH_AFTER) {
                $buckets['watch']++;
            } else {
                $buckets['fresh']++;
            }
        }

        return $buckets;
    }

    /**
     * Shape-compatible defaults for dashboards that do not render the pending panel.
     *
     * @return array<string, mixed>
     */
    private function emptyPendingSnapshot(): array
    {
        return [
            'pendingFrom' => null,
            'pendingTo' => null,
            'pendingProject' => null,
            'pendingDateType' => 'travel_date',
            'pendingTotal' => 0,
            'pendingGrandTotal' => 0,
            'pendingAging' => ['critical' => 0, 'watch' => 0, 'fresh' => 0, 'oldest_days' => null, 'avg_days' => null],
            'pendingAgeThresholds' => ['critical' => self::AGE_CRITICAL_AFTER, 'watch' => self::AGE_WATCH_AFTER],
            'pendingProjects' => collect(),
            'pendingOldestDays' => null,
            'pendingFiltersActive' => false,
            'pendingViewOpen' => false,
            'pendingProjectOptions' => collect(),
        ];
    }

    /** Read a YYYY-MM-DD query value, ignoring anything malformed. */
    private function dateQuery(Request $request, string $key): ?string
    {
        $value = trim((string) $request->query($key, ''));

        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
