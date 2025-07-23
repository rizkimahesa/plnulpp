<nav class="fixed top-0 left-0 w-full z-50 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- Logo PLN --}}
            <div class="flex items-center space-x-2">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('img/logo-pln.png') }}" alt="Logo PLN" class="logo-img" />
                </a>
            </div>

            {{-- Menu Tengah --}}
<div class="hidden sm:flex sm:items-center sm:justify-center flex-1 space-x-6">
    <a href="{{ route('dashboard') }}" class="text-white hover:underline underline-offset-4 decoration-2 decoration-indigo-500 px-3 py-2 text-sm font-medium">
        Dashboard
    </a>

    {{-- Harmet --}}
    <a href="{{ route('harmet.index') }}" class="text-white hover:underline underline-offset-4 decoration-2 decoration-indigo-500 px-3 py-2 text-sm font-medium">
        Harmet
    </a>

    {{-- Billing --}}
    <a href="{{ route('billing.index') }}" class="text-white hover:underline underline-offset-4 decoration-2 decoration-indigo-500 px-3 py-2 text-sm font-medium">
        Billing
    </a>

    @auth
    <div x-data="{ openData: false }" class="relative">
        <button @click="openData = !openData"
            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 hover:text-white focus:outline-none transition">
            <span>Data P2TL</span>
            <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.204l3.71-3.973a.75.75 0 111.08 1.04l-4.25 4.55a.75.75 0 01-1.08 0l-4.25-4.55a.75.75 0 01.02-1.06z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    
    {{-- User --}}
    <a href="{{ route('user.index') }}" class="text-white hover:underline underline-offset-4 decoration-2 decoration-indigo-500 px-3 py-2 text-sm font-medium">
        User
    </a>

        <div x-show="openData" @click.away="openData = false"
            class="absolute z-50 mt-2 w-40 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5"
            x-cloak>
            <div class="py-1">
                <x-dropdown-link :href="route('data.p2tl')">P2TL</x-dropdown-link>
                <x-dropdown-link :href="route('data.realisasi')">Realisasi</x-dropdown-link>
                    </div>
                 </div>
            </div>
            @endauth
        </div>

            {{-- User --}}
            <div class="flex items-center space-x-4">
                @auth
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 dark:text-gray-400 bg-transparent hover:text-white">
                                <div>{{ Auth::user()->name }}</div>
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            {{-- Menu Edit Profile --}}
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Edit Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                @endauth
            </div>

        </div>
    </div>
</nav>
