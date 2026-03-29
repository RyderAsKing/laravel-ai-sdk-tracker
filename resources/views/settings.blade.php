<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Larai Tracker | Settings</title>
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
                    <p class="text-xs text-slate-500">Settings</p>
                </div>
            </div>
            <nav class="flex items-center gap-2">
                <a href="{{ route('larai.dashboard') }}" class="border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">Dashboard</a>
                <a href="{{ route('larai.logs') }}" class="border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">Logs</a>
                <a href="{{ route('larai.settings') }}" class="border border-slate-300 bg-slate-900 px-3 py-2 text-sm text-white">Settings</a>
                <a href="{{ route('larai.auth.logout') }}" class="border border-slate-300 bg-white px-3 py-2 text-sm hover:bg-slate-100">Logout</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        @if(session('password_success'))
            <div class="border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('password_success') }}</div>
        @endif

        @if(session('password_error'))
            <div class="border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('password_error') }}</div>
        @endif

        <form action="{{ route('larai.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <article class="space-y-6 xl:col-span-1">
                    <div class="border border-slate-200 bg-white p-4">
                        <h2 class="mb-4 text-sm font-semibold">General</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Currency code</label>
                                <input type="text" name="currency[code]" value="{{ $currency['code'] }}" class="w-full border border-slate-300 bg-white px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Currency symbol</label>
                                <input type="text" name="currency[symbol]" value="{{ $currency['symbol'] }}" class="w-full border border-slate-300 bg-white px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="border border-slate-200 bg-white p-4">
                        <h2 class="mb-4 text-sm font-semibold">Budget</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Monthly limit</label>
                                <input type="number" step="0.01" name="budget[amount]" value="{{ $budget->amount }}" class="w-full border border-slate-300 bg-white px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Alert threshold (%)</label>
                                <input type="number" name="budget[threshold]" value="{{ $budget->alert_threshold }}" class="w-full border border-slate-300 bg-white px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Recipient email</label>
                                <input type="email" name="budget[email]" value="{{ $budget->recipient_email }}" class="w-full border border-slate-300 bg-white px-3 py-2 text-sm">
                            </div>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="budget[active]" {{ $budget->is_active ? 'checked' : '' }} class="h-4 w-4 border border-slate-300">
                                Enable budget monitoring
                            </label>
                        </div>
                    </div>

                    <div class="border border-slate-200 bg-white p-4">
                        <h2 class="mb-4 text-sm font-semibold">Security</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Current password</label>
                                <input type="password" name="security[current_password]" class="w-full border border-slate-300 bg-white px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">New password</label>
                                <input type="password" name="security[new_password]" class="w-full border border-slate-300 bg-white px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Confirm new password</label>
                                <input type="password" name="security[new_password_confirmation]" class="w-full border border-slate-300 bg-white px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="border border-slate-300 bg-slate-900 px-4 py-2 text-sm text-white">Save settings</button>
                        <button type="submit" formaction="{{ route('larai.sync-prices') }}" class="border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-100">Sync prices</button>
                    </div>
                </article>

                <article class="xl:col-span-2">
                    <section class="border border-slate-200 bg-white">
                        <div class="border-b border-slate-200 px-4 py-3">
                            <h2 class="text-sm font-semibold">Model Pricing</h2>
                            <p class="text-xs text-slate-500">Cost per 1 million tokens</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">Provider</th>
                                        <th class="px-4 py-3">Model</th>
                                        <th class="px-4 py-3">Input</th>
                                        <th class="px-4 py-3">Output</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @forelse($customPrices as $price)
                                        <tr>
                                            <td class="px-4 py-3 text-xs uppercase">{{ $price->provider }}</td>
                                            <td class="px-4 py-3 text-xs">{{ $price->model }}</td>
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.0001" name="prices[{{ $price->id }}][input]" value="{{ (float) $price->input_price_per_1m }}" class="w-28 border border-slate-300 bg-white px-2 py-1 text-xs">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.0001" name="prices[{{ $price->id }}][output]" value="{{ (float) $price->output_price_per_1m }}" class="w-28 border border-slate-300 bg-white px-2 py-1 text-xs">
                                            </td>
                                            <td class="px-4 py-3 text-xs">
                                                {{ $price->is_custom ? 'manual' : 'synced' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">No model pricing found. Run sync to load defaults.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </article>
            </section>
        </form>
    </main>
</body>
</html>
