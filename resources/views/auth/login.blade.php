<x-guest-layout>
<div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900">Welcome back</h2>
        <p class="text-sm text-slate-500 mt-1">Sign in to your account to continue</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-sm text-rose-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com"
                class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm">
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                class="w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm">
        </div>

        <!-- Remember me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-amber-600 hover:text-amber-700 font-medium" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full bg-amber-500 text-white py-2.5 rounded-lg font-semibold hover:bg-amber-600 transition-colors shadow-sm">
            Sign In
        </button>
    </form>

    <!-- Demo credentials -->
    <div class="mt-8 pt-6 border-t border-slate-200">
        <p class="text-xs text-slate-500 mb-3">Demo accounts (password: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-700">password</code>):</p>
        <div class="grid grid-cols-1 gap-2 text-xs">
            <button type="button" onclick="fillCreds('admin@amen.com')" class="text-left p-2 rounded-lg hover:bg-slate-100 border border-slate-200 transition-colors">
                <span class="font-medium text-slate-700">Admin:</span> <span class="text-slate-500">admin@amen.com</span>
            </button>
            <button type="button" onclick="fillCreds('supervisor@amen.com')" class="text-left p-2 rounded-lg hover:bg-slate-100 border border-slate-200 transition-colors">
                <span class="font-medium text-slate-700">Supervisor:</span> <span class="text-slate-500">supervisor@amen.com</span>
            </button>
            <button type="button" onclick="fillCreds('waiter@amen.com')" class="text-left p-2 rounded-lg hover:bg-slate-100 border border-slate-200 transition-colors">
                <span class="font-medium text-slate-700">Waiter:</span> <span class="text-slate-500">waiter@amen.com</span>
            </button>
            <button type="button" onclick="fillCreds('cooker@amen.com')" class="text-left p-2 rounded-lg hover:bg-slate-100 border border-slate-200 transition-colors">
                <span class="font-medium text-slate-700">Cooker:</span> <span class="text-slate-500">cooker@amen.com</span>
            </button>
            <button type="button" onclick="fillCreds('cashier@amen.com')" class="text-left p-2 rounded-lg hover:bg-slate-100 border border-slate-200 transition-colors">
                <span class="font-medium text-slate-700">Cashier:</span> <span class="text-slate-500">cashier@amen.com</span>
            </button>
        </div>
    </div>

    <script>
    function fillCreds(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
        document.getElementById('email').focus();
    }
    </script>
</div>
</x-guest-layout>
