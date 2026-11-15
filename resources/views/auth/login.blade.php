<x-guest-layout>
    <h1 class="font-['Cormorant_Garamond'] text-3xl font-semibold text-[#0F0D14]">Masuk</h1>
    <p class="mt-1 mb-6 text-sm text-gray-600">Gunakan akun Anda untuk melihat dan meminta akses video.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#C9A961] focus:ring-[#C9A961]">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#C9A961] focus:ring-[#C9A961]">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mt-4 flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded border-gray-300 text-[#C9A961] shadow-sm focus:ring-[#C9A961]">
                <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-gray-600 underline hover:text-gray-900 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9A961]">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit"
                class="mt-6 w-full rounded-md bg-[#0F0D14] px-4 py-2.5 text-sm font-semibold text-[#C9A961] hover:bg-[#1d1828] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9A961] focus-visible:ring-offset-2 focus-visible:ring-offset-[#FBF7EE]">
            Masuk
        </button>
    </form>

    @if (Route::has('register'))
        <p class="mt-6 text-center text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-[#8A6D2B] underline hover:text-[#6B541F]">Daftar</a>
        </p>
    @endif
</x-guest-layout>