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
    <script>
        /**
         * ページネーションで表示するページ番号の配列を計算する
         * @param {number} currentPage - 現在のページ
         * @param {number} lastPage - 最後のページ
         * @param {number} maxVisible - 表示するページ数（デフォルト5）
         * @returns {number[]} 表示するページ番号の配列
         */
        function calculateVisiblePages(currentPage, lastPage, maxVisible = 5) {
            const pages = [];
            
            // 表示開始ページを計算
            const offset = Math.floor(maxVisible / 2);
            let start = Math.max(1, currentPage - offset);

            // 表示範囲が最終ページを超える場合は開始ページを調整
            if (start + maxVisible - 1 > lastPage) {
                start = Math.max(1, lastPage - maxVisible + 1);
            }
            
            // ページ番号を追加（最大maxVisible個）
            for (let i = start; i <= Math.min(start + maxVisible - 1, lastPage); i++) {
                pages.push(i);
            }
            
            return pages;
        }
    </script>
</head>
<body class="@yield('body-class', 'bg-gray-100') min-h-screen flex flex-col">
    <!-- Fixed Header -->
    <nav class="header-bg text-gray-700 shadow-md fixed top-0 left-0 right-0 z-50" x-data="{ menuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-14">
                <!-- Left side - empty for balance -->
                <div class="flex items-center w-10">
                </div>

                <!-- Center - Page title -->
                <div class="flex-1 text-center">
                    <span class="font-medium text-gray-700">@yield('page-title', '')</span>
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
                    class="fixed top-0 right-0 h-full w-72 menu-bg shadow-2xl z-50 flex flex-col"
                >
                    <!-- Menu header with close button -->
                    <div class="flex items-center justify-between h-14 px-4 flex-shrink-0">
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

                    <!-- Menu items (scrollable) -->
                    <nav class="py-2 flex-1 overflow-y-auto" x-data="{ stressorOpen: true, columnOpen: true, writingOpen: true, problemOpen: true, supportOpen: true, notepadOpen: true, schemaOpen: true }">
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

                        <!-- ストレッサーとストレス反応（多段） -->
                        <div class="border-b border-gray-500/30">
                            <button
                                @click="stressorOpen = !stressorOpen"
                                class="flex items-center justify-between w-full px-6 py-3 text-gray-700"
                            >
                                <span class="font-medium text-lg">ストレッサーとストレス反応</span>
                                <svg
                                    class="w-5 h-5 transition-transform duration-200"
                                    :class="stressorOpen ? 'rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="stressorOpen" x-collapse class="">
                                @if(request()->is('stressor-and-responses'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">新規作成</span>
                                    </span>
                                @else
                                    <a href="/stressor-and-responses" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">新規作成</span>
                                    </a>
                                @endif
                                @if(request()->is('stressor-and-responses/list'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">一覧</span>
                                    </span>
                                @else
                                    <a href="/stressor-and-responses/list" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">一覧</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- 認知再構成法(コラム法)（多段） -->
                        <div class="border-b border-gray-500/30">
                            <button
                                @click="columnOpen = !columnOpen"
                                class="flex items-center justify-between w-full px-6 py-3 text-gray-700"
                            >
                                <span class="font-medium text-lg">認知再構成法(コラム法)</span>
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
                                @if(request()->is('columns/adaptive-thoughts'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">適応的思考</span>
                                    </span>
                                @else
                                    <a href="/columns/adaptive-thoughts" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">適応的思考</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- 問題解決法（多段） -->
                        <div class="border-b border-gray-500/30">
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
                                        <span class="text-base">問題解決法シート一覧</span>
                                    </span>
                                @else
                                    <a href="/problem-solvings/list" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">問題解決法シート一覧</span>
                                    </a>
                                @endif
                                @if(request()->is('problem-solvings/plans'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">計画一覧</span>
                                    </span>
                                @else
                                    <a href="/problem-solvings/plans" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">計画一覧</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- サポートネットワーク -->
                        <div class="border-b border-gray-500/30">
                            @if(request()->is('support-networks'))
                                <span class="flex items-center gap-4 px-6 py-3 text-gray-400 cursor-default">
                                    <span class="font-medium text-lg">サポートネットワーク</span>
                                </span>
                            @else
                                <a href="/support-networks" class="flex items-center gap-4 px-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                    <span class="font-medium text-lg">サポートネットワーク</span>
                                </a>
                            @endif
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

                        <!-- マインドフルネス瞑想 -->
                        <div class="border-b border-gray-500/30">
                            @if(request()->is('mindfulness'))
                                <span class="flex items-center gap-4 px-6 py-3 text-gray-400 cursor-default">
                                    <span class="font-medium text-lg">マインドフルネス瞑想</span>
                                </span>
                            @else
                                <a href="/mindfulness" class="flex items-center gap-4 px-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                    <span class="font-medium text-lg">マインドフルネス瞑想</span>
                                </a>
                            @endif
                        </div>

                        <!-- スキーマ療法（多段） -->
                        <div class="border-b border-gray-500/30">
                            <button
                                @click="schemaOpen = !schemaOpen"
                                class="flex items-center justify-between w-full px-6 py-3 text-gray-700"
                            >
                                <span class="font-medium text-lg">スキーマ療法</span>
                                <svg
                                    class="w-5 h-5 transition-transform duration-200"
                                    :class="schemaOpen ? 'rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="schemaOpen" x-collapse class="">
                                @php
                                $schemaLinks = [
                                    ['label' => 'トップ', 'patterns' => 'schema-therapy', 'exclude' => 'schema-therapy/*', 'href' => '/schema-therapy'],
                                    ['label' => '安全なイメージと安全な何か', 'patterns' => 'schema-therapy/safe-image', 'href' => '/schema-therapy/safe-image'],
                                    ['label' => '年表', 'patterns' => 'schema-therapy/chronology*', 'href' => '/schema-therapy/chronology'],
                                    ['label' => '早期不適応的スキーマ', 'patterns' => 'early-maladaptive-schemas', 'exclude' => 'early-maladaptive-schemas/*', 'href' => '/early-maladaptive-schemas'],
                                    ['label' => 'モードマップ', 'patterns' => 'schema-therapy/mode-map', 'href' => '/schema-therapy/mode-map'],
                                    ['label' => 'セルフモニタリング', 'patterns' => 'schema-therapy/self-monitoring*', 'href' => '/schema-therapy/self-monitoring'],
                                    ['label' => 'ハッピースキーマと行動計画', 'patterns' => 'schema-therapy/happy-schema-action-plan', 'href' => '/schema-therapy/happy-schema-action-plan'],
                                    ['label' => 'モードワーク', 'patterns' => 'schema-therapy/mode-work', 'href' => '/schema-therapy/mode-work'],
                                    ['label' => 'スキーマカウント', 'patterns' => 'early-maladaptive-schemas/count', 'href' => '/early-maladaptive-schemas/count'],
                                ];
                                @endphp
                                @foreach ($schemaLinks as $link)
                                    @php
                                        $isActive = request()->is($link['patterns']);
                                        if (isset($link['exclude'])) {
                                            $isActive = $isActive && !request()->is($link['exclude']);
                                        }
                                    @endphp
                                    @if ($isActive)
                                        <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                            <span class="text-base">{{ $link['label'] }}</span>
                                        </span>
                                    @else
                                        <a href="{{ $link['href'] }}" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                            <span class="text-base">{{ $link['label'] }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- メモ帳（多段） -->
                        <div class="border-b border-gray-500/30">
                            <button
                                @click="notepadOpen = !notepadOpen"
                                class="flex items-center justify-between w-full px-6 py-3 text-gray-700"
                            >
                                <span class="font-medium text-lg">メモ帳</span>
                                <svg
                                    class="w-5 h-5 transition-transform duration-200"
                                    :class="notepadOpen ? 'rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="notepadOpen" x-collapse class="">
                                @if(request()->is('simple-notepads'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">新規作成</span>
                                    </span>
                                @else
                                    <a href="/simple-notepads" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">新規作成</span>
                                    </a>
                                @endif
                                @if(request()->is('simple-notepads/list'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">一覧</span>
                                    </span>
                                @else
                                    <a href="/simple-notepads/list" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">一覧</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- 会員メニュー -->
                        <div class="border-b border-gray-500/30" x-data="authMenu()" x-init="init()">
                            <template x-if="isLoggedIn">
                                <div>
                                    <div class="flex items-center gap-3 px-6 py-3 text-gray-700">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="font-medium text-lg truncate" x-text="memberName"></span>
                                    </div>
                                    <button
                                        @click="logout()"
                                        class="flex items-center gap-3 w-full pl-10 pr-6 py-3 text-red-600 hover:bg-white/40 transition-colors"
                                    >
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span class="text-base">ログアウト</span>
                                    </button>
                                </div>
                            </template>
                            <template x-if="!isLoggedIn">
                                <div>
                                    <a href="/login" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        <span class="font-medium text-lg">ログイン</span>
                                    </a>
                                    <a href="/register" class="flex items-center gap-3 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">会員登録</span>
                                    </a>
                                </div>
                            </template>
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
                <p>&copy; {{ date('Y') }} ココケア</p>
            </div>
        </div>
    </footer>

    <!-- 認証メニュー共通関数 -->
    <script>
    function authMenu() {
        return {
            isLoggedIn: false,
            memberName: '',

            async init() {
                try {
                    const res = await fetch('/api/auth/me', {
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json' },
                    });
                    if (res.ok) {
                        const data = await res.json();
                        this.isLoggedIn = true;
                        this.memberName = data.member.name;
                    }
                } catch (e) {
                    // 未認証の場合は何もしない
                }
            },

            async logout() {
                try {
                    await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
                    await fetch('/api/auth/logout', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': decodeURIComponent(
                                document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || ''
                            ),
                        },
                    });
                } catch (e) {
                    // API失敗してもリダイレクトする
                }
                window.location.href = '/login';
            },
        };
    }
    </script>

    <!-- 共通CSVエクスポート関数 -->
    <script>
    /**
     * CSVエクスポート共通関数
     * iOS/Android両対応（Web Share API + Blob URL方式）
     * 
     * @param {string} apiUrl - CSVエクスポートAPIのURL
     * @param {object} params - 検索パラメータ（keyword, tag_ids等）
     * @param {string} defaultFilename - デフォルトのファイル名
     * @param {string} shareTitle - Web Share APIで使用するタイトル
     * @returns {Promise<void>}
     */
    async function exportCsvFromApi(apiUrl, params = {}, defaultFilename = 'export.csv', shareTitle = 'CSVエクスポート') {
        // クエリパラメータを構築
        const searchParams = new URLSearchParams();
        if (params.keyword) {
            searchParams.append('keyword', params.keyword);
        }
        if (params.tagIds && params.tagIds.length > 0) {
            params.tagIds.forEach(id => {
                searchParams.append('tag_ids[]', id);
            });
        }
        
        const url = apiUrl + (searchParams.toString() ? '?' + searchParams.toString() : '');
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error('CSV export failed');
        }
        
        const blob = await response.blob();
        const filename = response.headers.get('Content-Disposition')?.match(/filename="(.+)"/)?.[1] || defaultFilename;
        
        // iOS/Android対応のダウンロード処理
        if (navigator.share && navigator.canShare) {
            try {
                const file = new File([blob], filename, { type: 'text/csv' });
                if (navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        files: [file],
                        title: shareTitle,
                    });
                    return;
                }
            } catch (shareError) {
                // Web Share APIが使えない場合は従来の方法にフォールバック
            }
        }
        
        // 従来のダウンロード方式
        const downloadUrl = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(downloadUrl);
    }
    </script>
</body>
</html>
