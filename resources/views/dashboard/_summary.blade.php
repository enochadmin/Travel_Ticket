<div class="mx-auto max-w-4xl space-y-6">

    <div class="overflow-hidden rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm">
        <div class="px-6 py-6 sm:px-8" style="background:linear-gradient(135deg,#312e81 0%,#4f46e5 55%,#6366f1 100%);">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">Dashboard</p>
            <h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">{{ $dashboardTitle }}</h2>
            <p class="mt-2 text-sm text-indigo-100 max-w-2xl">{{ $dashboardSubtitle }}</p>
            <p class="mt-3 text-sm text-indigo-200">
                {{ $totalRequests }} total request{{ $totalRequests === 1 ? '' : 's' }} ·
                Open <span class="font-semibold text-white">Reports</span> in the sidebar for detailed analysis and exports.
            </p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <a href="{{ route('travel-requests.index', ['status' => 'approved']) }}" 
           class="group rounded-2xl border border-green-200 bg-green-50 dark:bg-green-950/30 dark:border-green-900 p-6 hover:shadow-lg hover:border-green-300 transition-all duration-200 cursor-pointer">
            <p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-400 group-hover:text-green-800 dark:group-hover:text-green-300">Approved</p>
            <p class="mt-3 text-4xl font-bold text-green-800 dark:text-green-300">{{ $approved }}</p>
        </a>
        <a href="{{ route('travel-requests.index', ['status' => 'pending']) }}" 
           class="group rounded-2xl border border-yellow-200 bg-yellow-50 dark:bg-yellow-950/30 dark:border-yellow-900 p-6 hover:shadow-lg hover:border-yellow-300 transition-all duration-200 cursor-pointer">
            <p class="text-xs font-semibold uppercase tracking-wider text-yellow-700 dark:text-yellow-400 group-hover:text-yellow-800 dark:group-hover:text-yellow-300">Pending</p>
            <p class="mt-3 text-4xl font-bold text-yellow-800 dark:text-yellow-300">{{ $pending }}</p>
        </a>
        <a href="{{ route('travel-requests.index', ['status' => 'rejected']) }}" 
           class="group rounded-2xl border border-red-200 bg-red-50 dark:bg-red-950/30 dark:border-red-900 p-6 hover:shadow-lg hover:border-red-300 transition-all duration-200 cursor-pointer">
            <p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-400 group-hover:text-red-800 dark:group-hover:text-red-300">Rejected</p>
            <p class="mt-3 text-4xl font-bold text-red-800 dark:text-red-300">{{ $rejected }}</p>
        </a>
    </div>

    <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-800 dark:text-slate-100">Request Status Overview</h3>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 mb-4">Approved vs pending vs rejected</p>
        <div class="mx-auto max-w-md" style="height: 280px;">
            <canvas id="{{ $summaryChartId }}"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById(@json($summaryChartId));
        if (!ctx) return;

        const isDark = document.documentElement.classList.contains('dark');
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [{{ $approved }}, {{ $pending }}, {{ $rejected }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderColor: isDark ? '#0b1220' : '#ffffff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: isDark ? '#cbd5e1' : '#4b5563', font: { size: 12 } }
                    }
                }
            }
        });
    });
</script>
