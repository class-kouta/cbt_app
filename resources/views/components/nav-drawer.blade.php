@php
    use App\Support\Navigation;

    $openState = Navigation::initialOpenState();
@endphp

<nav
    class="flex flex-col flex-1 min-h-0"
    x-data="{
        sections: @js($openState['sections']),
        items: @js($openState['items']),
        toggleSection(id) {
            this.sections[id] = !this.sections[id];
        },
        toggleItem(id) {
            this.items[id] = !this.items[id];
        },
    }"
>
    <div class="flex-1 overflow-y-auto px-2 py-3 space-y-1">
        {{-- マイページ --}}
        <div class="pb-2 mb-2 border-b border-gray-500/20">
            @foreach (config('navigation.top') as $link)
                @continue($link['icon'] === 'home')
                @php($isActive = Navigation::isActive($link['active']))
                @if ($isActive)
                    <span class="flex items-center gap-3 mx-1 px-3 py-2.5 rounded-lg text-gray-400 cursor-default">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="font-medium">{{ $link['label'] }}</span>
                    </span>
                @else
                    <a
                        href="{{ $link['href'] }}"
                        @click="menuOpen = false"
                        class="flex items-center gap-3 mx-1 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-white/50 transition-colors"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="font-medium">{{ $link['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>

        {{-- ワークトップと配下カテゴリ --}}
        <div class="space-y-1">
            @foreach (config('navigation.top') as $link)
                @continue($link['icon'] !== 'home')
                @php($isActive = Navigation::isActive($link['active']))
                @if ($isActive)
                    <span class="flex items-center gap-3 mx-1 px-3 py-2.5 rounded-lg text-gray-400 cursor-default">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-medium">{{ $link['label'] }}</span>
                    </span>
                @else
                    <a
                        href="{{ $link['href'] }}"
                        @click="menuOpen = false"
                        class="flex items-center gap-3 mx-1 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-white/50 transition-colors"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-medium">{{ $link['label'] }}</span>
                    </a>
                @endif
            @endforeach

            {{-- セクション（ワークトップ配下） --}}
            <div class="ml-4 pl-3 border-l border-gray-500/25 space-y-0.5">
                @foreach (config('navigation.sections') as $section)
                    <div class="rounded-lg overflow-hidden">
                        <button
                            type="button"
                            @click="toggleSection('{{ $section['id'] }}')"
                            class="flex items-center justify-between w-full px-3 py-2.5 text-left text-gray-700 hover:bg-white/40 transition-colors rounded-lg"
                        >
                            <span class="text-sm font-medium text-gray-700">{{ $section['title'] }}</span>
                            <svg
                                class="w-4 h-4 flex-shrink-0 text-gray-500 transition-transform duration-200"
                                :class="sections['{{ $section['id'] }}'] ? 'rotate-180' : ''"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="sections['{{ $section['id'] }}']" x-collapse class="pb-1">
                            @foreach ($section['items'] as $item)
                                @php($itemActive = Navigation::itemIsActive($item))

                                @if (isset($item['children']))
                                    <div class="mx-1">
                                        <button
                                            type="button"
                                            @click="toggleItem('{{ $item['id'] }}')"
                                            class="flex items-center justify-between w-full px-3 py-2 text-left rounded-md transition-colors {{ $itemActive ? 'text-gray-400' : 'text-gray-700 hover:bg-white/30' }}"
                                        >
                                            <span class="text-sm font-medium leading-snug">{{ $item['label'] }}</span>
                                            <svg
                                                class="w-4 h-4 flex-shrink-0 ml-2 transition-transform duration-200 {{ $itemActive ? 'text-gray-400' : 'text-gray-500' }}"
                                                :class="items['{{ $item['id'] }}'] ? 'rotate-180' : ''"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <div x-show="items['{{ $item['id'] }}']" x-collapse class="pb-1">
                                            @foreach ($item['children'] as $child)
                                                @php($childActive = Navigation::isActive($child['active']))
                                                @if ($childActive)
                                                    <span class="block pl-6 pr-3 py-1.5 text-sm text-gray-400 cursor-default">
                                                        {{ $child['label'] }}
                                                    </span>
                                                @else
                                                    <a
                                                        href="{{ $child['href'] }}"
                                                        @click="menuOpen = false"
                                                        class="block pl-6 pr-3 py-1.5 text-sm text-gray-600 hover:bg-white/40 hover:text-gray-800 rounded-md transition-colors"
                                                    >
                                                        {{ $child['label'] }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    @if ($itemActive)
                                        <span class="block mx-1 px-3 py-2 text-sm font-medium text-gray-400 cursor-default rounded-md">
                                            {{ $item['label'] }}
                                        </span>
                                    @else
                                        <a
                                            href="{{ $item['href'] }}"
                                            @click="menuOpen = false"
                                            class="block mx-1 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-white/40 rounded-md transition-colors"
                                        >
                                            {{ $item['label'] }}
                                        </a>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 会員メニュー --}}
    <div class="flex-shrink-0 border-t border-gray-500/20 px-2 py-2" x-data="authMenu()" x-init="init()">
        <template x-if="isLoggedIn">
            <button
                type="button"
                @click="logout()"
                class="flex items-center gap-3 w-full mx-1 px-3 py-2.5 text-red-600 hover:bg-white/40 rounded-lg transition-colors"
            >
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="font-medium">ログアウト</span>
            </button>
        </template>
        <template x-if="!isLoggedIn">
            <div>
                <a href="/login" @click="menuOpen = false" class="flex items-center gap-3 mx-1 px-3 py-2.5 text-gray-700 hover:bg-white/40 rounded-lg transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span class="font-medium">ログイン</span>
                </a>
                <a href="/register" @click="menuOpen = false" class="block mx-1 pl-11 pr-3 py-2 text-sm text-gray-600 hover:bg-white/40 rounded-lg transition-colors">
                    会員登録
                </a>
            </div>
        </template>
    </div>
</nav>
