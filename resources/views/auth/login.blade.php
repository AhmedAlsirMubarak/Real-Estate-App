<x-guest-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    @endphp

    <div class="rounded-2xl bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 p-5 sm:p-6 text-white shadow-2xl mb-5 relative overflow-hidden">
        <div class="absolute -top-10 -left-6 w-28 h-28 rounded-full bg-cyan-400/20 blur-2xl"></div>
        <div class="absolute -bottom-10 -right-6 w-28 h-28 rounded-full bg-fuchsia-400/20 blur-2xl"></div>
        <div class="relative">
            <p class="text-xs uppercase tracking-[0.25em] text-blue-100/80">{{ $tr('تسجيل الدخول الآمن', 'Secure Sign In') }}</p>
            <h1 class="mt-2 text-2xl font-black">{{ $tr('مرحبًا بعودتك', 'Welcome back') }}</h1>
            <p class="mt-1 text-sm text-blue-100/90">{{ $tr('ادخل بياناتك للوصول إلى لوحة التحكم المتقدمة.', 'Enter your credentials to access your advanced dashboard.') }}</p>
        </div>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700">{{ $tr('البريد الإلكتروني', 'Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="mt-1 block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="{{ $tr('name@example.com', 'name@example.com') }}">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-semibold text-slate-700">{{ $tr('كلمة المرور', 'Password') }}</label>
              
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="mt-1 block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500">
            <span>{{ $tr('تذكرني', 'Remember me') }}</span>
        </label>

        <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 font-semibold text-white shadow-lg shadow-blue-500/30 hover:from-blue-700 hover:to-indigo-700 transition">
            {{ $tr('تسجيل الدخول', 'Log in') }}
        </button>
    </form>
</x-guest-layout>
