@php
use App\Enums\ConditionCheckRating;

$ratingFields = ConditionCheckRating::fieldLabels();
$ratingLabels = ConditionCheckRating::labelsByField();
$shortLabels = [
    'mood' => '気分',
    'fatigue' => '疲労',
    'anxiety' => '不安',
    'sleepiness' => '眠気',
    'physical_condition' => '体調',
];
@endphp

@extends('layouts.app')

@section('title', 'コンディションチェック - 記録一覧')
@section('page-title', 'コンディションチェック')

@section('content')
<div x-data="conditionCheckListApp()" x-init="init()" x-cloak>
    <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-2">
            合計: <span x-text="items.length" class="font-bold"></span> 件
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

        <div x-show="!loading && items.length === 0" class="text-center py-12 text-gray-500">
            <div class="mb-4 flex justify-center text-gray-300">
                <x-icon name="clipboard-document" class="w-12 h-12" />
            </div>
            <p>まだ記録がありません</p>
            <a href="/condition-checks/create" class="text-teal-600 hover:text-teal-800 text-sm mt-4 inline-block">
                コンディションを記録してみましょう →
            </a>
        </div>
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

    return {
        items: [],
        loading: true,
        ratingLabels,

        async init() {
            await this.loadItems();
        },

        async loadItems() {
            this.loading = true;
            try {
                const res = await apiFetch('/api/condition-checks');
                this.items = await res.json();
            } finally {
                this.loading = false;
            }
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
            const num = parseInt(value, 10);
            if (num <= 2) return 'bg-green-100 text-green-800';
            if (num === 3) return 'bg-yellow-100 text-yellow-800';
            return 'bg-orange-100 text-orange-800';
        },
    };
}
</script>
@endsection
