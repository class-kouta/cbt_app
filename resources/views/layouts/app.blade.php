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

        async function parseApiErrorMessage(response, fallback = '保存に失敗しました') {
            try {
                const data = await response.json();
                if (data.errors) {
                    const messages = Object.values(data.errors).flat();
                    if (messages.length > 0) {
                        return messages.join('\n');
                    }
                }
                if (data.message) {
                    return data.message;
                }
            } catch (error) {}

            return fallback;
        }

        async function fetchExposureOptions() {
            const response = await apiFetch('/api/exposures/options');
            if (!response.ok) {
                return [];
            }

            const data = await response.json();
            return data.data || [];
        }

        async function fetchProblemSolvingOptions() {
            const response = await apiFetch('/api/problem-solvings/options');
            if (!response.ok) {
                return [];
            }

            const data = await response.json();
            return data.data || [];
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

        const SIMPLE_NOTEPAD_TAG_COLORS = [
            { bg: 'bg-rose-100', text: 'text-rose-700', border: 'border-rose-300', selectedBg: 'bg-rose-500', selectedBorder: 'border-rose-500', hover: 'hover:border-rose-400 hover:bg-rose-50', iconBg: 'bg-rose-100', iconText: 'text-rose-600' },
            { bg: 'bg-amber-100', text: 'text-amber-700', border: 'border-amber-300', selectedBg: 'bg-amber-500', selectedBorder: 'border-amber-500', hover: 'hover:border-amber-400 hover:bg-amber-50', iconBg: 'bg-amber-100', iconText: 'text-amber-600' },
            { bg: 'bg-emerald-100', text: 'text-emerald-700', border: 'border-emerald-300', selectedBg: 'bg-emerald-500', selectedBorder: 'border-emerald-500', hover: 'hover:border-emerald-400 hover:bg-emerald-50', iconBg: 'bg-emerald-100', iconText: 'text-emerald-600' },
            { bg: 'bg-sky-100', text: 'text-sky-700', border: 'border-sky-300', selectedBg: 'bg-sky-500', selectedBorder: 'border-sky-500', hover: 'hover:border-sky-400 hover:bg-sky-50', iconBg: 'bg-sky-100', iconText: 'text-sky-600' },
            { bg: 'bg-violet-100', text: 'text-violet-700', border: 'border-violet-300', selectedBg: 'bg-violet-500', selectedBorder: 'border-violet-500', hover: 'hover:border-violet-400 hover:bg-violet-50', iconBg: 'bg-violet-100', iconText: 'text-violet-600' },
            { bg: 'bg-pink-100', text: 'text-pink-700', border: 'border-pink-300', selectedBg: 'bg-pink-500', selectedBorder: 'border-pink-500', hover: 'hover:border-pink-400 hover:bg-pink-50', iconBg: 'bg-pink-100', iconText: 'text-pink-600' },
            { bg: 'bg-teal-100', text: 'text-teal-700', border: 'border-teal-300', selectedBg: 'bg-teal-500', selectedBorder: 'border-teal-500', hover: 'hover:border-teal-400 hover:bg-teal-50', iconBg: 'bg-teal-100', iconText: 'text-teal-600' },
            { bg: 'bg-orange-100', text: 'text-orange-700', border: 'border-orange-300', selectedBg: 'bg-orange-500', selectedBorder: 'border-orange-500', hover: 'hover:border-orange-400 hover:bg-orange-50', iconBg: 'bg-orange-100', iconText: 'text-orange-600' },
            { bg: 'bg-indigo-100', text: 'text-indigo-700', border: 'border-indigo-300', selectedBg: 'bg-indigo-500', selectedBorder: 'border-indigo-500', hover: 'hover:border-indigo-400 hover:bg-indigo-50', iconBg: 'bg-indigo-100', iconText: 'text-indigo-600' },
            { bg: 'bg-lime-100', text: 'text-lime-700', border: 'border-lime-300', selectedBg: 'bg-lime-500', selectedBorder: 'border-lime-500', hover: 'hover:border-lime-400 hover:bg-lime-50', iconBg: 'bg-lime-100', iconText: 'text-lime-600' },
        ];

        function getSimpleNotepadTagColor(tagId) {
            const index = Math.abs(Number(tagId) || 0) % SIMPLE_NOTEPAD_TAG_COLORS.length;
            return SIMPLE_NOTEPAD_TAG_COLORS[index];
        }
    </script>
</head>
<body class="@yield('body-class', 'bg-gray-100') min-h-screen flex flex-col">
    <!-- Fixed Header -->
    <nav class="header-bg text-gray-700 shadow-md fixed top-0 left-0 right-0 z-50" x-data="{ menuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-14">
                <!-- Left side - My Page link -->
                <div class="flex items-center w-10">
                    @if(request()->routeIs('mypage'))
                        <span
                            class="p-2 text-gray-400 cursor-default"
                            aria-current="page"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                />
                            </svg>
                            <span class="sr-only">マイページ（現在のページ）</span>
                        </span>
                    @else
                        <a
                            href="{{ route('mypage') }}"
                            class="p-2 rounded-md text-gray-700 hover:bg-white/30 transition-colors focus:outline-none focus:ring-2 focus:ring-white/50"
                            aria-label="マイページへ"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
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
                    class="fixed top-0 right-0 h-full w-80 menu-bg shadow-2xl z-50 flex flex-col"
                >
                    <!-- Menu header with close button -->
                    <div class="flex items-center justify-end h-14 px-3 flex-shrink-0 border-b border-gray-500/20">
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

                    <x-nav-drawer />
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
