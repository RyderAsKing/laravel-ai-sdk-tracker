<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Larai Tracker | Logs</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <p class="text-xs text-slate-500">Logs</p>
                </div>
            </div>
            <nav class="flex items-center gap-2">
                <a href="{{ route('larai.dashboard') }}" class="border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">Dashboard</a>
                <a href="{{ route('larai.logs') }}" class="border border-slate-300 bg-slate-900 px-3 py-2 text-sm text-white">Logs</a>
                <a href="{{ route('larai.settings') }}" class="border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">Settings</a>
                <a href="{{ route('larai.auth.logout') }}" class="border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">Logout</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <section class="border border-slate-200 bg-white p-4">
            <form action="{{ route('larai.logs') }}" method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-4">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search model, provider, or id"
                    class="border border-slate-300 bg-white px-3 py-2 text-sm"
                >

                <select name="provider" onchange="this.form.submit()" class="border border-slate-300 bg-white px-3 py-2 text-sm">
                    <option value="all">All providers</option>
                    @foreach($providers as $p)
                        <option value="{{ $p }}" {{ request('provider') == $p ? 'selected' : '' }}>{{ strtoupper($p) }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-2">
                    <button type="submit" class="border border-slate-300 bg-slate-900 px-3 py-2 text-sm text-white">Filter</button>
                    <a href="{{ route('larai.logs') }}" class="border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">Reset</a>
                </div>

                <div class="flex items-center justify-start gap-2 md:justify-end">
                    <a href="{{ route('larai.export', ['format' => 'json']) }}" class="border border-slate-300 bg-white px-2 py-1 text-xs hover:bg-slate-100">JSON</a>
                    <a href="{{ route('larai.export', ['format' => 'csv']) }}" class="border border-slate-300 bg-white px-2 py-1 text-xs hover:bg-slate-100">CSV</a>
                    <a href="{{ route('larai.export', ['format' => 'txt']) }}" class="border border-slate-300 bg-white px-2 py-1 text-xs hover:bg-slate-100">TXT</a>
                </div>
            </form>
        </section>

        <section class="border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">ID</a>
                            </th>
                            <th class="px-4 py-3">Provider</th>
                            <th class="px-4 py-3">Model</th>
                            <th class="px-4 py-3">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'prompt_tokens', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Prompt</a>
                            </th>
                            <th class="px-4 py-3">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'completion_tokens', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Completion</a>
                            </th>
                            <th class="px-4 py-3">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_tokens', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Total</a>
                            </th>
                            <th class="px-4 py-3">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'cost_usd', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Cost</a>
                            </th>
                            <th class="px-4 py-3">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Created</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">#{{ $log->id }}</td>
                                <td class="px-4 py-3 text-xs uppercase">{{ $log->provider }}</td>
                                <td class="px-4 py-3 text-xs">{{ $log->model }}</td>
                                <td class="px-4 py-3 text-xs tabular-nums">{{ number_format($log->prompt_tokens) }}</td>
                                <td class="px-4 py-3 text-xs tabular-nums">{{ number_format($log->completion_tokens) }}</td>
                                <td class="px-4 py-3 text-xs tabular-nums">{{ number_format($log->total_tokens) }}</td>
                                <td class="px-4 py-3 text-xs tabular-nums">{{ $currency_symbol }}{{ number_format($log->cost_usd, 6) }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">No logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="flex items-center justify-between border-t border-slate-200 px-4 py-3">
                    <p class="text-xs text-slate-500">Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }}</p>

                    <div class="flex items-center gap-2">
                        @if ($logs->onFirstPage())
                            <span class="border border-slate-200 bg-slate-100 px-3 py-1 text-xs text-slate-400">Previous</span>
                        @else
                            <a href="{{ $logs->previousPageUrl() }}" class="border border-slate-300 bg-white px-3 py-1 text-xs hover:bg-slate-100">Previous</a>
                        @endif

                        @if ($logs->hasMorePages())
                            <a href="{{ $logs->nextPageUrl() }}" class="border border-slate-300 bg-white px-3 py-1 text-xs hover:bg-slate-100">Next</a>
                        @else
                            <span class="border border-slate-200 bg-slate-100 px-3 py-1 text-xs text-slate-400">Next</span>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </main>
</body>
</html>
