<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Larai Tracker | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
        }
    </style>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50 px-4 text-slate-900">
    <main class="w-full max-w-md border border-slate-200 bg-white p-6">
        <div class="mb-6 flex items-center gap-3">
            <img src="https://doq9otz3zrcmp.cloudfront.net/blogs/1_1771417079_rJ7ATPHw.png" alt="Larai Tracker" class="h-8 w-8 border border-slate-300 object-cover">
            <div>
                <h1 class="text-sm font-semibold">Larai Tracker</h1>
                <p class="text-xs text-slate-500">Authentication</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-4 border border-rose-300 bg-rose-50 px-3 py-2 text-sm text-rose-800">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('larai.auth.login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label class="mb-1 block text-xs text-slate-500">
                    @if($setupRequired ?? false)
                        New password
                    @else
                        Password
                    @endif
                </label>
                <input type="password" name="password" required autofocus class="w-full border border-slate-300 bg-white px-3 py-2 text-sm">
            </div>

            @if($setupRequired ?? false)
                <div>
                    <label class="mb-1 block text-xs text-slate-500">Confirm password</label>
                    <input type="password" name="password_confirmation" required class="w-full border border-slate-300 bg-white px-3 py-2 text-sm">
                </div>
            @endif

            <button type="submit" class="w-full border border-slate-300 bg-slate-900 px-4 py-2 text-sm text-white">
                @if($setupRequired ?? false)
                    Set password and continue
                @else
                    Login
                @endif
            </button>
        </form>
    </main>
</body>
</html>
