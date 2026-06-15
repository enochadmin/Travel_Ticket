<?php

namespace App\Http\Controllers;

use App\Exports\Reports\MostRequestedProjectsExport;
use App\Exports\Reports\MostTraveledCitiesExport;
use App\Exports\Reports\TravelRequestsReportExport;
use App\Models\City;
use App\Models\Project;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->reportQuery($request)->with(['user', 'project']);

        $statsBase = clone $query;
        $stats = $this->buildStatusStats($statsBase);

        $travelRequests = $query->latest()->paginate(25)->withQueryString();
        $projects = $this->projectsForFilters();
        $headOfficeProjects = Project::headOffice()->orderBy('name')->get();

        $chartData = ['labels' => [], 'data' => []];
        $chartSource = (clone $query)->get()->groupBy(fn ($item) => optional($item->project)->name ?? 'Unknown');
        foreach ($chartSource->sortByDesc(fn ($items) => $items->count()) as $project => $items) {
            $chartData['labels'][] = $project;
            $chartData['data'][] = $items->count();
        }

        $statusChartData = $this->simpleStatusChart($stats);
        $filters = $this->reportFilters($request);
        $scopeLabel = $this->scopeLabel();

        return view('reports.index', compact(
            'travelRequests',
            'projects',
            'headOfficeProjects',
            'chartData',
            'statusChartData',
            'stats',
            'filters',
            'scopeLabel'
        ));
    }

    public function mostTraveledCities(Request $request)
    {
        $query = $this->reportQuery($request);
        $cityField = $this->normalizeCityField($request->get('city_field', 'destination'));
        $cityStats = $this->buildCityStats($query, $cityField);

        $totalRequests = (clone $query)->count();
        $topCity = $cityStats->first();
        $chartData = [
            'labels' => $cityStats->take(10)->pluck('city')->values()->all(),
            'data' => $cityStats->take(10)->pluck('request_count')->values()->all(),
        ];

        $projects = $this->projectsForFilters();
        $filters = array_merge($this->reportFilters($request), ['city_field' => $cityField]);
        $scopeLabel = $this->scopeLabel();

        return view('reports.most_traveled_cities', compact(
            'cityStats',
            'chartData',
            'projects',
            'filters',
            'totalRequests',
            'topCity',
            'cityField',
            'scopeLabel'
        ));
    }

    public function mostRequestedProjects(Request $request)
    {
        $query = $this->reportQuery($request);

        $projectStats = $this->buildProjectStats($query);
        $totalRequests = (clone $query)->count();
        $topProject = $projectStats->first();

        $chartData = [
            'labels' => $projectStats->take(10)->pluck('project_name')->values()->all(),
            'data' => $projectStats->take(10)->pluck('request_count')->values()->all(),
        ];

        $projects = $this->projectsForFilters();
        $filters = $this->reportFilters($request);
        $scopeLabel = $this->scopeLabel();

        return view('reports.most_requested_projects', compact(
            'projectStats',
            'chartData',
            'projects',
            'filters',
            'totalRequests',
            'topProject',
            'scopeLabel'
        ));
    }

    public function exportTravelRequests(Request $request): BinaryFileResponse
    {
        $rows = $this->reportQuery($request)->with(['user', 'project', 'pm'])->latest()->get();

        return Excel::download(
            new TravelRequestsReportExport($rows),
            'travel-requests-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportMostTraveledCities(Request $request): BinaryFileResponse
    {
        $cityField = $this->normalizeCityField($request->get('city_field', 'destination'));
        $cityStats = $this->buildCityStats($this->reportQuery($request), $cityField);

        return Excel::download(
            new MostTraveledCitiesExport($cityStats, $cityField),
            'most-traveled-cities-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportMostRequestedProjects(Request $request): BinaryFileResponse
    {
        $projectStats = $this->buildProjectStats($this->reportQuery($request));

        return Excel::download(
            new MostRequestedProjectsExport($projectStats),
            'most-requested-projects-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    private function reportQuery(Request $request): Builder
    {
        return $this->applyRoleScope(
            $this->applyReportFilters(TravelRequest::query(), $request)
        );
    }

    private function applyRoleScope(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user->hasRole('project-manager')) {
            $projectId = $user->approverProjectId();
            $query->where('project_id', $projectId ?? -1);
        }

        return $query;
    }

    private function applyReportFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->boolean('head_office_only')) {
            $query->forHeadOffice();
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', 'like', '%' . $request->purpose . '%');
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('travel_date', [$request->start_date, $request->end_date]);
        }

        return $query;
    }

    private function reportFilters(Request $request): array
    {
        return $request->only([
            'project_id',
            'status',
            'destination',
            'purpose',
            'start_date',
            'end_date',
            'head_office_only',
        ]);
    }

    private function buildStatusStats(Builder $query): array
    {
        $base = clone $query;

        return [
            'total' => (clone $base)->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'rejected' => (clone $base)->where('status', 'rejected')->count(),
            'pending' => (clone $base)->whereIn('status', [
                'pending_pm',
                'pending_commercial',
                'pending_hod',
                'pending_ceo',
            ])->count(),
            'pending_pm' => (clone $base)->where('status', 'pending_pm')->count(),
            'pending_commercial' => (clone $base)->whereIn('status', ['pending_commercial', 'pending_hod'])->count(),
            'pending_ceo' => (clone $base)->where('status', 'pending_ceo')->count(),
        ];
    }

    private function simpleStatusChart(array $stats): array
    {
        return [
            'labels' => ['Approved', 'Pending', 'Rejected'],
            'data' => [$stats['approved'], $stats['pending'], $stats['rejected']],
        ];
    }

    private function normalizeCityField(?string $cityField): string
    {
        if (! in_array($cityField, ['destination', 'origin', 'all'], true)) {
            return 'destination';
        }

        return $cityField;
    }

    private function buildCityStats(Builder $query, string $cityField): Collection
    {
        $cityStats = $this->aggregateCityStats(clone $query, $cityField);
        $regions = City::whereIn('name', $cityStats->pluck('city'))->pluck('region', 'name');

        return $cityStats->map(function ($row, $index) use ($regions) {
            $row->region = $regions[$row->city] ?? null;
            $row->rank = $index + 1;

            return $row;
        })->values();
    }

    private function buildProjectStats(Builder $query): Collection
    {
        $rows = (clone $query)
            ->reorder()
            ->selectRaw('project_id, COUNT(*) as request_count, COALESCE(SUM(passenger_count), 0) as passenger_count')
            ->groupBy('project_id')
            ->orderByDesc('request_count')
            ->get();

        $projects = Project::whereIn('id', $rows->pluck('project_id'))->get()->keyBy('id');

        return $rows->map(function ($row, $index) use ($projects) {
            $project = $projects->get($row->project_id);

            return (object) [
                'rank' => $index + 1,
                'project_id' => $row->project_id,
                'project_name' => $project?->name ?? 'Unknown',
                'project_code' => $project?->project_code,
                'region' => $project?->region,
                'is_head_office' => $project?->isHeadOffice() ?? false,
                'request_count' => (int) $row->request_count,
                'passenger_count' => (int) $row->passenger_count,
                'approved_count' => 0,
                'pending_count' => 0,
                'rejected_count' => 0,
            ];
        })->map(function ($row) use ($query) {
            $projectQuery = (clone $query)->where('project_id', $row->project_id);
            $row->approved_count = (clone $projectQuery)->where('status', 'approved')->count();
            $row->rejected_count = (clone $projectQuery)->where('status', 'rejected')->count();
            $row->pending_count = (clone $projectQuery)->whereIn('status', [
                'pending_pm',
                'pending_commercial',
                'pending_hod',
                'pending_ceo',
            ])->count();

            return $row;
        })->take(25)->values();
    }

    private function aggregateCityStats(Builder $query, string $cityField): Collection
    {
        if ($cityField === 'all') {
            $destinations = $this->cityCountsForField(clone $query, 'destination');
            $origins = $this->cityCountsForField(clone $query, 'origin');

            return $destinations->concat($origins)
                ->groupBy('city')
                ->map(function (Collection $rows, string $city) {
                    return (object) [
                        'city' => $city,
                        'request_count' => $rows->sum('request_count'),
                        'passenger_count' => $rows->sum('passenger_count'),
                    ];
                })
                ->sortByDesc('request_count')
                ->values()
                ->take(25);
        }

        return $this->cityCountsForField(clone $query, $cityField)->take(25);
    }

    private function cityCountsForField(Builder $query, string $field): Collection
    {
        return $query
            ->reorder()
            ->selectRaw("{$field} as city, COUNT(*) as request_count, COALESCE(SUM(passenger_count), 0) as passenger_count")
            ->whereNotNull($field)
            ->where($field, '!=', '')
            ->groupBy($field)
            ->orderByDesc('request_count')
            ->get();
    }

    private function projectsForFilters(): Collection
    {
        $user = Auth::user();

        if ($user->hasRole('project-manager')) {
            $projectId = $user->approverProjectId();

            return $projectId
                ? Project::where('id', $projectId)->orderBy('name')->get()
                : collect();
        }

        return Project::orderBy('name')->get();
    }

    private function scopeLabel(): ?string
    {
        $user = Auth::user();

        if ($user->hasRole('project-manager')) {
            $project = $user->managedProject ?? ($user->project_id ? Project::find($user->project_id) : null);

            return $project ? "Showing data for {$project->name}" : null;
        }

        return null;
    }
}
