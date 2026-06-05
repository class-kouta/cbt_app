<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CBTアプリ')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
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
        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        }

        function xsrfToken() {
            return decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '');
        }

        function normalizeHeaders(headers = {}) {
            const normalized = {};

            if (headers instanceof Headers) {
                for (const [key, value] of headers.entries()) {
                    normalized[key.toLowerCase()] = value;
                }

                return normalized;
            }

            for (const [key, value] of Object.entries(headers || {})) {
                normalized[key.toLowerCase()] = value;
            }

            return normalized;
        }

        async function apiFetch(input, init = {}) {
            const requestInit = { ...init };
            const refreshCsrfCookie = !!requestInit.refreshCsrfCookie;
            delete requestInit.refreshCsrfCookie;

            if (refreshCsrfCookie) {
                await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
            }

            const method = (requestInit.method || 'GET').toUpperCase();
            const headers = normalizeHeaders(requestInit.headers);

            requestInit.credentials = requestInit.credentials || 'same-origin';
            headers['accept'] = headers['accept'] || 'application/json';

            if (!['GET', 'HEAD', 'OPTIONS'].includes(method)) {
                if (refreshCsrfCookie) {
                    headers['x-xsrf-token'] = headers['x-xsrf-token'] || xsrfToken();
                } else {
                    headers['x-csrf-token'] = headers['x-csrf-token'] || csrfToken();
                }
            }

            requestInit.headers = headers;

            return fetch(input, requestInit);
        }

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
                <!-- Left side - Home link -->
                <div class="flex items-center w-10">
                    @if(request()->routeIs('home'))
                        <span
                            class="p-2 text-gray-400 cursor-default"
                            aria-current="page"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                />
                            </svg>
                            <span class="sr-only">トップ（現在のページ）</span>
                        </span>
                    @else
                        <a
                            href="{{ route('home') }}"
                            class="p-2 rounded-md text-gray-700 hover:bg-white/30 transition-colors focus:outline-none focus:ring-2 focus:ring-white/50"
                            aria-label="トップへ"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                />
                            </svg>
                        </a>
                    @endif
                </div>

                <!-- Center - Page title -->
                <div class="flex-1 text-center">
                    <span class="font-medium text-gray-700">@yield('page-title', '')</span>
                </div>

                <!-- Right side - Hamburger menu button (未ログインの認証画面では非表示) -->
                @if(!request()->routeIs('login', 'register'))
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
                @else
                <div class="w-10" aria-hidden="true"></div>
                @endif

                @if(!request()->routeIs('login', 'register'))
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
                    @php
                        $menuActive = [
                            'home' => request()->routeIs('home'),
                            'writing' => request()->is('writing-disclosures*'),
                            'stressor' => request()->is('stressor-and-responses*'),
                            'column' => request()->is('columns*'),
                            'problem' => request()->is('problem-solvings*'),
                            'support' => request()->is('support-networks*'),
                            'coping' => request()->is('copings*'),
                            'mindfulness' => request()->is('mindfulness*'),
                            'chronology' => request()->is('schema-therapy/chronology*'),
                            'ems' => request()->is('early-maladaptive-schemas*'),
                            'modeDialogue' => request()->is('schema-therapy/mode-work/dialogue*'),
                            'notepad' => request()->is('simple-notepads*'),
                        ];
                        $menuGroupBg = 'bg-white/50';
                    @endphp
                    <nav
                        class="py-2 flex-1 overflow-y-auto"
                        x-data="{
                            stressorOpen: {{ $menuActive['stressor'] ? 'true' : 'false' }},
                            columnOpen: {{ $menuActive['column'] ? 'true' : 'false' }},
                            writingOpen: {{ $menuActive['writing'] ? 'true' : 'false' }},
                            problemOpen: {{ $menuActive['problem'] ? 'true' : 'false' }},
                            notepadOpen: {{ $menuActive['notepad'] ? 'true' : 'false' }},
                            modeDialogueOpen: {{ $menuActive['modeDialogue'] ? 'true' : 'false' }}
                        }"
                    >
                        <!-- トップ -->
                        <div class="border-b border-gray-500/30 {{ $menuActive['home'] ? $menuGroupBg : '' }}">
                            @if($menuActive['home'])
                                <span class="flex justify-center px-6 py-3 text-gray-400 cursor-default">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                        />
                                    </svg>
                                    <span class="sr-only">トップ（現在のページ）</span>
                                </span>
                            @else
                                <a
                                    href="{{ route('home') }}"
                                    class="flex justify-center px-6 py-3 text-gray-700 hover:bg-white/40 transition-colors"
                                    aria-label="トップへ"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                        />
                                    </svg>
                                </a>
                            @endif
                        </div>

                        <!-- 筆記開示（多段） -->
                        <div class="border-b border-gray-500/30 {{ $menuActive['writing'] ? $menuGroupBg : '' }}">
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
                        <div class="border-b border-gray-500/30 {{ $menuActive['stressor'] ? $menuGroupBg : '' }}">
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
                        <div class="border-b border-gray-500/30 {{ $menuActive['column'] ? $menuGroupBg : '' }}">
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
                        <div class="border-b border-gray-500/30 {{ $menuActive['problem'] ? $menuGroupBg : '' }}">
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
                        <div class="border-b border-gray-500/30 {{ $menuActive['support'] ? $menuGroupBg : '' }}">
                            @if($menuActive['support'])
                                <span class="flex items-center justify-end gap-2 px-6 py-3 text-gray-400 cursor-default">
                                    <span class="font-medium text-lg">サポートネットワーク</span>
                                    <span class="text-lg leading-none" aria-hidden="true">&gt;</span>
                                </span>
                            @else
                                <a href="/support-networks" class="flex items-center justify-end gap-2 px-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                    <span class="font-medium text-lg">サポートネットワーク</span>
                                    <span class="text-lg leading-none text-gray-600" aria-hidden="true">&gt;</span>
                                </a>
                            @endif
                        </div>

                        <!-- コーピングリスト -->
                        <div class="border-b border-gray-500/30 {{ $menuActive['coping'] ? $menuGroupBg : '' }}">
                            @if($menuActive['coping'])
                                <span class="flex items-center justify-end gap-2 px-6 py-3 text-gray-400 cursor-default">
                                    <span class="font-medium text-lg">コーピングリスト</span>
                                    <span class="text-lg leading-none" aria-hidden="true">&gt;</span>
                                </span>
                            @else
                                <a href="/copings" class="flex items-center justify-end gap-2 px-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                    <span class="font-medium text-lg">コーピングリスト</span>
                                    <span class="text-lg leading-none text-gray-600" aria-hidden="true">&gt;</span>
                                </a>
                            @endif
                        </div>

                        <!-- マインドフルネス瞑想 -->
                        <div class="border-b border-gray-500/30 {{ $menuActive['mindfulness'] ? $menuGroupBg : '' }}">
                            @if($menuActive['mindfulness'])
                                <span class="flex items-center justify-end gap-2 px-6 py-3 text-gray-400 cursor-default">
                                    <span class="font-medium text-lg">マインドフルネス瞑想</span>
                                    <span class="text-lg leading-none" aria-hidden="true">&gt;</span>
                                </span>
                            @else
                                <a href="/mindfulness" class="flex items-center justify-end gap-2 px-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                    <span class="font-medium text-lg">マインドフルネス瞑想</span>
                                    <span class="text-lg leading-none text-gray-600" aria-hidden="true">&gt;</span>
                                </a>
                            @endif
                        </div>

                        @php
                        $schemaLinks = [
                            ['label' => 'スキーマ年表', 'activeKey' => 'chronology', 'href' => '/schema-therapy/chronology'],
                            ['label' => '早期不適応的スキーマ', 'activeKey' => 'ems', 'href' => '/early-maladaptive-schemas'],
                        ];
                        @endphp
                        @foreach ($schemaLinks as $link)
                            @php
                                $schemaLinkActive = $menuActive[$link['activeKey']];
                            @endphp
                            <div class="border-b border-gray-500/30 {{ $schemaLinkActive ? $menuGroupBg : '' }}">
                                @if($schemaLinkActive)
                                    <span class="flex items-center justify-end gap-2 px-6 py-3 text-gray-400 cursor-default">
                                        <span class="font-medium text-lg">{{ $link['label'] }}</span>
                                        <span class="text-lg leading-none" aria-hidden="true">&gt;</span>
                                    </span>
                                @else
                                    <a href="{{ $link['href'] }}" class="flex items-center justify-end gap-2 px-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="font-medium text-lg">{{ $link['label'] }}</span>
                                        <span class="text-lg leading-none text-gray-600" aria-hidden="true">&gt;</span>
                                    </a>
                                @endif
                            </div>
                        @endforeach

                        <!-- スキーマモードの対話ワーク（多段） -->
                        <div class="border-b border-gray-500/30 {{ $menuActive['modeDialogue'] ? $menuGroupBg : '' }}">
                            <button
                                @click="modeDialogueOpen = !modeDialogueOpen"
                                class="flex items-center justify-between w-full px-6 py-3 text-gray-700"
                            >
                                <span class="font-medium text-lg">スキーマモードの対話ワーク</span>
                                <svg
                                    class="w-5 h-5 transition-transform duration-200"
                                    :class="modeDialogueOpen ? 'rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="modeDialogueOpen" x-collapse class="">
                                @if(request()->is('schema-therapy/mode-work/dialogue/create'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">新規作成</span>
                                    </span>
                                @else
                                    <a href="/schema-therapy/mode-work/dialogue/create" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">新規作成</span>
                                    </a>
                                @endif
                                @if(request()->is('schema-therapy/mode-work/dialogue'))
                                    <span class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-400 cursor-default">
                                        <span class="text-base">一覧</span>
                                    </span>
                                @else
                                    <a href="/schema-therapy/mode-work/dialogue" class="flex items-center gap-4 pl-10 pr-6 py-3 text-gray-700 hover:bg-white/40 transition-colors">
                                        <span class="text-base">一覧</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- メモ帳（多段） -->
                        <div class="border-b border-gray-500/30 {{ $menuActive['notepad'] ? $menuGroupBg : '' }}">
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
                @endif
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
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
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
                    const res = await apiFetch('/api/auth/me');
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
                    await apiFetch('/api/auth/logout', {
                        method: 'POST',
                        refreshCsrfCookie: true,
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
        const response = await apiFetch(url, {
            headers: { 'Accept': 'text/csv' },
        });
        
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
