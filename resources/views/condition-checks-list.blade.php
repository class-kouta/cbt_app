@php
use App\Enums\ConditionCheckRating;

$ratingLabels = ConditionCheckRating::labelsByField();
$ratingBadgeClasses = ConditionCheckRating::badgeClassesByValue();
$shortLabels = [
    'mood' => '気分',
    'fatigue' => '疲労',
    'anxiety' => '不安',
    'sleepiness' => '眠気',
    'physical_condition' => '体調',
];
@endphp

@extends('layouts.app')

@section('title', 'コンディションチェック')
@section('page-title', 'コンディションチェック')

@section('content')
<div x-data="conditionCheckListApp()" x-init="init()" x-cloak>
    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-4">
        <p class="text-emerald-800 text-sm">
            現在のコンディションを記録しましょう
        </p>
    </div>

    <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-2" x-show="!loading && total > 0">
            合計: <span x-text="total" class="font-bold"></span> 件
        </div>

        <template x-for="item in items" :key="item.id">
            <a :href="'/condition-checks/' + item.id + '/edit'" class="block">
                <div class="bg-white rounded-lg shadow-md p-4 transition-all hover:shadow-lg hover:bg-emerald-50 cursor-pointer">
                    <div class="font-semibold text-gray-900 mb-3" x-text="formatDate(item.created_at)"></div>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        @foreach ($shortLabels as $field => $shortLabel)
                            <div class="rounded-lg border border-gray-100 bg-gray-50 px-2 py-2 text-center">
                                <div class="text-[10px] sm:text-xs text-gray-500 mb-1">{{ $shortLabel }}</div>
                                <span
                                    class="inline-block text-[10px] sm:text-xs font-medium px-2 py-1 rounded-full leading-tight"
                                    :class="getRatingClass(item.{{ $field }})"
                                    x-text="getRatingLabel('{{ $field }}', item.{{ $field }})"
                                ></span>
                            </div>
                        @endforeach
                    </div>

                    <p
                        x-show="item.memo"
                        class="mt-3 text-sm text-gray-600 line-clamp-1"
                        x-text="item.memo"
                    ></p>
                </div>
            </a>
        </template>

        <div x-show="loading" class="text-center py-16">
            <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 mt-2">読み込み中...</p>
        </div>

        <div x-show="!loading && items.length === 0" class="text-center py-12 text-gray-500">
            <div class="mb-4 flex justify-center text-gray-300">
                <x-icon name="clipboard-document" class="w-12 h-12" />
            </div>
            <p>まだ記録がありません</p>
            <a href="/condition-checks/create" class="text-teal-600 hover:text-teal-800 text-sm mt-4 inline-block">
                コンディションを記録してみましょう →
            </a>
        </div>

        <x-pagination
            theme-color-from="emerald-500"
            theme-color-to="teal-500"
            theme-border-color="emerald-500"
        />
    </div>

    <a
        href="/condition-checks/create"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-emerald-600 hover:to-teal-600 transition-all"
        title="新しい記録を作成"
    >
        ＋
    </a>
</div>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
function conditionCheckListApp() {
    const ratingLabels = @json($ratingLabels);
    const ratingBadgeClasses = @json($ratingBadgeClasses);

    return {
        items: [],
        loading: true,
        ratingLabels,
        ratingBadgeClasses,
        currentPage: 1,
        perPage: 30,
        total: 0,
        lastPage: 1,
        from: 0,
        to: 0,

        async init() {
            await this.loadItems();
        },

        async loadItems() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                params.append('page', this.currentPage);
                params.append('per_page', this.perPage);

                const res = await apiFetch('/api/condition-checks?' + params.toString());
                const result = await res.json();

                this.items = result.data || [];
                this.total = result.total || 0;
                this.currentPage = result.current_page || 1;
                this.lastPage = result.last_page || 1;
                this.from = result.from || 0;
                this.to = result.to || 0;
                this.perPage = result.per_page || 30;
            } finally {
                this.loading = false;
            }
        },

        async goToPage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage) return;
            this.currentPage = page;
            await this.loadItems();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        get visiblePages() {
            return calculateVisiblePages(this.currentPage, this.lastPage);
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            const h = String(date.getHours()).padStart(2, '0');
            const min = String(date.getMinutes()).padStart(2, '0');
            return `${y}年${m}月${d}日 ${h}時${min}分`;
        },

        getRatingLabel(field, value) {
            const labels = this.ratingLabels[field] || [];
            return labels[value - 1] || '-';
        },

        getRatingClass(value) {
            return this.ratingBadgeClasses[value] || 'bg-gray-100 text-gray-800';
        },
    };
}
</script>
@endsection
