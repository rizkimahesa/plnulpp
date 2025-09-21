<x-guest-layout>

    <!-- Status Session -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <!-- Form Login -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-lg font-semibold" />
            <x-text-input id="email" class="block mt-1 w-full text-base" type="email"
                name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-lg font-semibold" />
            <x-text-input id="password" class="block mt-1 w-full text-base" type="password"
                name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-6">
            {{-- Ganti dari password.request ke tampilan register --}}
            <a class="underline text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ url('/register') }}">
                {{ __('Belum punya akun? Daftar') }}
            </a>

            <x-primary-button class="ms-4 px-6 py-3 text-base">
                {{ __('Login') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
