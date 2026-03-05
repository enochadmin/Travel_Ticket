<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use App\Models\Project;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = TravelRequest::with(['user', 'project']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
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

        $travelRequests = $query->latest()->get();
        $projects = Project::all();

        $chartData = [
            'labels' => [],
            'data' => []
        ];

        $requestsPerProject = $travelRequests->groupBy('project.name')->map(fn($items) => $items->count());
        foreach ($requestsPerProject as $project => $count) {
            $chartData['labels'][] = $project ?? 'Unknown';
            $chartData['data'][] = $count;
        }

        return view('reports.index', compact('travelRequests', 'projects', 'chartData'));
    }
}
