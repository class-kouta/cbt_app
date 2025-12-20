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
            background-color: rgba(167, 212, 182, 0.7);
        }
        .footer-bg {
            background-color: rgba(167, 212, 182, 0.7);
        }
        .menu-bg {
            background-color: rgba(167, 212, 182, 0.85);
        }
        .slide-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }
        .slide-menu.open {
            transform: translateX(0);
        }
        .overlay {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .overlay.open {
            opacity: 1;
        }
    </style>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="@yield('body-class', 'bg-gray-100') min-h-screen flex flex-col">
    <!-- Fixed Header -->
    <nav class="header-bg text-gray-700 shadow-md fixed top-0 left-0 right-0 z-50" x-data="{ menuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <!-- Left side - empty -->
                <div class="flex items-center">
                </div>

                <!-- Right side - Hamburger menu button -->
                <div>
                    <button
                        @click="menuOpen = !menuOpen"
                        class="p-2 rounded-md hover:bg-white/30 transition-colors focus:outline-none focus:ring-2 focus:ring-white/50"
                        aria-label="メニューを開く"
                    >
                        <!-- Hamburger icon -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Overlay -->
                <div
                    x-show="menuOpen"
                    x-cloak
                    @click="menuOpen = false"
                    class="fixed inset-0 bg-black/50 z-40"
                    :class="menuOpen ? 'overlay open' : 'overlay'"
                ></div>

                <!-- Slide-in menu from right -->
                <div
                    x-cloak
                    :class="menuOpen ? 'slide-menu open' : 'slide-menu'"
                    class="fixed top-0 right-0 h-full w-72 menu-bg shadow-2xl z-50"
                >
                    <!-- Menu header with close button -->
                    <div class="flex items-center justify-between h-14 px-4">
                        <span class="text-lg font-semibold text-gray-700"></span>
                        <button
                            @click="menuOpen = false"
                            class="p-2 rounded-md hover:bg-white/30 transition-colors focus:outline-none"
                            aria-label="メニューを閉じる"
                        >
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Menu items -->
                    <nav class="py-2" x-data="{ columnOpen: true, writingOpen: true, problemOpen: true }">
                        <!-- トップ -->
                        <div class="border-b border-gray-500/30">
                            @if(request()->is('/'))
                                <span class="flex items-center gap-4 px-6 py-3 text-gray-400 cursor-default">
                                    <span class="font-medium text-lg">トップ</span>
                                </span>
                            @else
                                <a href="/" class="flex items-center gap-4 px-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                    <span class="font-medium text-lg">トップ</span>
                                </a>
                            @endif
                        </div>

                        <!-- コラム法（多段） -->
                        <div class="border-b border-gray-500/30">
                            <button
                                @click="columnOpen = !columnOpen"
                                class="flex items-center justify-between w-full px-6 py-3 text-gray-700"
                            >
                                <span class="font-medium text-lg">コラム法</span>
                                <svg
                                    class="w-5 h-5 transition-transform duration-200"
                                    :class="columnOpen ? 'rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="columnOpen" x-collapse class="">
                                @if(request()->is('columns'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">新規作成</span>
                                    </span>
                                @else
                                    <a href="/columns" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">新規作成</span>
                                    </a>
                                @endif
                                @if(request()->is('columns/list'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">一覧</span>
                                    </span>
                                @else
                                    <a href="/columns/list" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">一覧</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- コーピングリスト -->
                        <div class="border-b border-gray-500/30">
                            @if(request()->is('copings'))
                                <span class="flex items-center gap-4 px-6 py-3 text-gray-400 cursor-default">
                                    <span class="font-medium text-lg">コーピングリスト</span>
                                </span>
                            @else
                                <a href="/copings" class="flex items-center gap-4 px-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                    <span class="font-medium text-lg">コーピングリスト</span>
                                </a>
                            @endif
                        </div>

                        <!-- 筆記開示（多段） -->
                        <div class="border-b border-gray-500/30">
                            <button
                                @click="writingOpen = !writingOpen"
                                class="flex items-center justify-between w-full px-6 py-3 text-gray-700"
                            >
                                <span class="font-medium text-lg">筆記開示</span>
                                <svg
                                    class="w-5 h-5 transition-transform duration-200"
                                    :class="writingOpen ? 'rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="writingOpen" x-collapse class="">
                                @if(request()->is('writing-disclosures'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">新規作成</span>
                                    </span>
                                @else
                                    <a href="/writing-disclosures" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">新規作成</span>
                                    </a>
                                @endif
                                @if(request()->is('writing-disclosures/list'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">一覧</span>
                                    </span>
                                @else
                                    <a href="/writing-disclosures/list" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">一覧</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- 問題解決法（多段） -->
                        <div>
                            <button
                                @click="problemOpen = !problemOpen"
                                class="flex items-center justify-between w-full px-6 py-3 text-gray-700"
                            >
                                <span class="font-medium text-lg">問題解決法</span>
                                <svg
                                    class="w-5 h-5 transition-transform duration-200"
                                    :class="problemOpen ? 'rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="problemOpen" x-collapse class="">
                                @if(request()->is('problem-solvings'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">新規作成</span>
                                    </span>
                                @else
                                    <a href="/problem-solvings" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">新規作成</span>
                                    </a>
                                @endif
                                @if(request()->is('problem-solvings/list'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">一覧</span>
                                    </span>
                                @else
                                    <a href="/problem-solvings/list" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">一覧</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </nav>
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
                <p>&copy; {{ date('Y') }} ココロの避難所</p>
            </div>
        </div>
    </footer>
</body>
</html>
