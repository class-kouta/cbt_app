<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CBTアプリ')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
        .header-bg {
            background-color: rgba(167, 212, 182, 0.85);
        }
        .footer-bg {
            background-color: rgba(167, 212, 182, 0.85);
        }
        .menu-bg {
            background-color: rgba(167, 212, 182, 0.95);
        }
    </style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Fixed Header -->
    <nav class="header-bg text-gray-700 shadow-md fixed top-0 left-0 right-0 z-50" x-data="{ menuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <!-- Left side - empty or can add title -->
                <div class="flex items-center">
                    <span class="text-lg font-semibold">セルフケア</span>
                </div>

                <!-- Right side - Hamburger menu -->
                <div class="relative">
                    <button
                        @click="menuOpen = !menuOpen"
                        class="p-2 rounded-md hover:bg-white/30 transition-colors focus:outline-none focus:ring-2 focus:ring-white/50"
                        aria-label="メニューを開く"
                    >
                        <!-- Hamburger icon -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                x-show="!menuOpen"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                x-show="menuOpen"
                                x-cloak
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>

                    <!-- Dropdown menu -->
                    <div
                        x-show="menuOpen"
                        x-cloak
                        @click.away="menuOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="absolute right-0 mt-2 w-56 menu-bg rounded-lg shadow-lg ring-1 ring-black/5 overflow-hidden"
                    >
                        <div class="py-2">
                            <a href="/" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                <span class="text-xl">🏠</span>
                                <span class="font-medium">トップ</span>
                            </a>
                            <a href="/copings" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                <span class="text-xl">🌈</span>
                                <span class="font-medium">コーピングリスト</span>
                            </a>
                            <a href="/columns" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                <span class="text-xl">📝</span>
                                <span class="font-medium">コラム法</span>
                            </a>
                            <a href="/writing-disclosures" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                <span class="text-xl">✍️</span>
                                <span class="font-medium">筆記開示</span>
                            </a>
                            <a href="/problem-solvings" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                <span class="text-xl">💡</span>
                                <span class="font-medium">問題解決法</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Spacer for fixed header -->
    <div class="h-14"></div>

    <!-- Main content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex-grow w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-bg text-gray-700 shadow-inner mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="text-center text-sm">
                <p>&copy; {{ date('Y') }} セルフケアアプリ</p>
            </div>
        </div>
    </footer>
</body>
</html>
