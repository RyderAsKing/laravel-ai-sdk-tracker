<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Larai Tracker | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <img src="https://doq9otz3zrcmp.cloudfront.net/blogs/1_1771417079_rJ7ATPHw.png" alt="Larai Tracker" class="h-8 w-8 border border-slate-300 object-cover">
                <div>
                    <p class="text-sm font-semibold">Larai Tracker</p>
                    <p class="text-xs text-slate-500">AI Usage Dashboard</p>
                </div>
            </div>

            <nav class="flex items-center gap-2">
                <a href="{{ route('larai.dashboard') }}" class="border border-slate-300 bg-slate-900 px-3 py-2 text-sm text-white">Dashboard</a>
                <a href="{{ route('larai.logs') }}" class="border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">Logs</a>
                <a href="{{ route('larai.settings') }}" class="border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">Settings</a>
                <a href="{{ route('larai.auth.logout') }}" class="border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">Logout</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Cost</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums">{{ $stats['currency_symbol'] }}{{ number_format($stats['total_cost'], 4) }}</p>
            </article>
            <article class="border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Today Cost</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums">{{ $stats['currency_symbol'] }}{{ number_format($stats['today_cost'], 4) }}</p>
            </article>
            <article class="border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Tokens</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums">{{ number_format($stats['total_tokens']) }}</p>
            </article>
            <article class="border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Active Models</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums">{{ $stats['costs_by_model']->count() }}</p>
            </article>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <article class="border border-slate-200 bg-white p-4 xl:col-span-2">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold">Cost Over Time</h2>
                        <p class="text-xs text-slate-500">Selected date range</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" onclick="setDateRange(7)" class="date-range-btn border border-slate-300 bg-white px-2 py-1 text-xs hover:bg-slate-100" data-days="7">7D</button>
                        <button type="button" onclick="setDateRange(14)" class="date-range-btn border border-slate-300 bg-white px-2 py-1 text-xs hover:bg-slate-100" data-days="14">14D</button>
                        <button type="button" onclick="setDateRange(30)" class="date-range-btn border border-slate-300 bg-white px-2 py-1 text-xs hover:bg-slate-100" data-days="30">30D</button>
                        <button type="button" onclick="setDateRange(90)" class="date-range-btn border border-slate-300 bg-white px-2 py-1 text-xs hover:bg-slate-100" data-days="90">90D</button>

                        <input id="startDateInput" type="date" value="{{ $stats['start_date'] }}" class="border border-slate-300 bg-white px-2 py-1 text-xs">
                        <input id="endDateInput" type="date" value="{{ $stats['end_date'] }}" class="border border-slate-300 bg-white px-2 py-1 text-xs">
                        <button type="button" onclick="applyDateRange()" class="border border-slate-300 bg-slate-900 px-3 py-1 text-xs text-white">Apply</button>
                    </div>
                </div>

                <div class="h-72">
                    <canvas id="costChart"></canvas>
                </div>
            </article>

            <article class="border border-slate-200 bg-white p-4">
                <h2 class="text-sm font-semibold">Cost by Model</h2>
                <p class="mb-4 text-xs text-slate-500">Current range breakdown</p>

                <div class="mx-auto h-56 max-w-56">
                    <canvas id="modelChart"></canvas>
                </div>

                <div id="modelListContainer" class="mt-4 space-y-2">
                    @foreach($stats['costs_by_model'] as $model)
                        <div class="flex items-center justify-between border border-slate-200 bg-slate-50 px-2 py-1 text-xs">
                            <span class="truncate font-mono text-slate-700">{{ $model->model }}</span>
                            <span class="tabular-nums">{{ $stats['currency_symbol'] }}{{ number_format($model->cost, 4) }}</span>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="border border-slate-200 bg-white">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <h2 class="text-sm font-semibold">Recent Logs</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('larai.export', 'json') }}" class="border border-slate-300 bg-white px-2 py-1 text-xs hover:bg-slate-100">JSON</a>
                    <a href="{{ route('larai.export', 'csv') }}" class="border border-slate-300 bg-white px-2 py-1 text-xs hover:bg-slate-100">CSV</a>
                    <a href="{{ route('larai.export', 'txt') }}" class="border border-slate-300 bg-white px-2 py-1 text-xs hover:bg-slate-100">TXT</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Provider</th>
                            <th class="px-4 py-3">Model</th>
                            <th class="px-4 py-3">Prompt</th>
                            <th class="px-4 py-3">Completion</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Cost</th>
                            <th class="px-4 py-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($stats['recent_logs'] as $log)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">#{{ $log->id }}</td>
                                <td class="px-4 py-3 text-xs uppercase">{{ $log->provider }}</td>
                                <td class="px-4 py-3 text-xs">{{ $log->model }}</td>
                                <td class="px-4 py-3 text-xs tabular-nums">{{ number_format($log->prompt_tokens) }}</td>
                                <td class="px-4 py-3 text-xs tabular-nums">{{ number_format($log->completion_tokens) }}</td>
                                <td class="px-4 py-3 text-xs tabular-nums">{{ number_format($log->total_tokens) }}</td>
                                <td class="px-4 py-3 text-xs tabular-nums">{{ $stats['currency_symbol'] }}{{ number_format($log->cost_usd, 6) }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">No logs yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                <a href="{{ route('larai.logs') }}" class="border border-slate-300 bg-white px-3 py-2 text-xs hover:bg-slate-100">Open full logs</a>
            </div>
        </section>
    </main>

    <script>
        const chartDataUrl = '{{ route("larai.chart-data") }}';
        const modelColors = ['#0f172a', '#334155', '#475569', '#64748b', '#94a3b8', '#cbd5e1'];

        let currentStartDate = '{{ $stats["start_date"] }}';
        let currentEndDate = '{{ $stats["end_date"] }}';

        const costChart = new Chart(document.getElementById('costChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($stats['costs_over_time']->pluck('date')) !!},
                datasets: [{
                    label: 'Cost',
                    data: {!! json_encode($stats['costs_over_time']->pluck('cost')) !!},
                    borderColor: '#0f172a',
                    borderWidth: 2,
                    pointRadius: 2,
                    fill: false,
                    tension: 0.2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: '#e2e8f0' }, ticks: { color: '#64748b', maxRotation: 0, autoSkip: true } },
                    y: { grid: { color: '#e2e8f0' }, ticks: { color: '#64748b' } },
                },
            },
        });

        const modelChart = new Chart(document.getElementById('modelChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($stats['costs_by_model']->pluck('model')) !!},
                datasets: [{
                    data: {!! json_encode($stats['costs_by_model']->pluck('cost')) !!},
                    backgroundColor: modelColors,
                    borderWidth: 1,
                    borderColor: '#ffffff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%',
            },
        });

        function setDateRange(days) {
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - (days - 1));

            const startStr = start.toISOString().split('T')[0];
            const endStr = end.toISOString().split('T')[0];

            document.getElementById('startDateInput').value = startStr;
            document.getElementById('endDateInput').value = endStr;

            applyDateRange();
            highlightActiveDaysButton(days);
        }

        function highlightActiveDaysButton(days) {
            document.querySelectorAll('.date-range-btn').forEach((btn) => {
                if (parseInt(btn.dataset.days, 10) === days) {
                    btn.classList.remove('bg-white', 'text-slate-900');
                    btn.classList.add('bg-slate-900', 'text-white');
                } else {
                    btn.classList.remove('bg-slate-900', 'text-white');
                    btn.classList.add('bg-white', 'text-slate-900');
                }
            });
        }

        async function applyDateRange() {
            currentStartDate = document.getElementById('startDateInput').value;
            currentEndDate = document.getElementById('endDateInput').value;

            if (!currentStartDate || !currentEndDate) {
                return;
            }

            const query = new URLSearchParams({
                start_date: currentStartDate,
                end_date: currentEndDate,
            });

            const response = await fetch(`${chartDataUrl}?${query.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            costChart.data.labels = data.costs_over_time.map((item) => item.date);
            costChart.data.datasets[0].data = data.costs_over_time.map((item) => item.cost);
            costChart.update();

            modelChart.data.labels = data.costs_by_model.map((item) => item.model);
            modelChart.data.datasets[0].data = data.costs_by_model.map((item) => item.cost);
            modelChart.data.datasets[0].backgroundColor = data.costs_by_model.map((_, index) => modelColors[index % modelColors.length]);
            modelChart.update();

            updateModelList(data.costs_by_model, data.currency_symbol);
        }

        function updateModelList(models, currencySymbol) {
            const container = document.getElementById('modelListContainer');

            if (!models.length) {
                container.innerHTML = '<p class="text-xs text-slate-500">No model usage in selected range.</p>';
                return;
            }

            container.innerHTML = models.map((model) => `
                <div class="flex items-center justify-between border border-slate-200 bg-slate-50 px-2 py-1 text-xs">
                    <span class="truncate font-mono text-slate-700">${model.model}</span>
                    <span class="tabular-nums">${currencySymbol}${parseFloat(model.cost).toFixed(4)}</span>
                </div>
            `).join('');
        }

        highlightActiveDaysButton(7);
    </script>
</body>
</html>
