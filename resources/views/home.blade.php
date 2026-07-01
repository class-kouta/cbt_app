@extends('layouts.app')

@section('title', config('app.name'))
@section('page-title', 'ワーク')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="space-y-0">
    <x-work-section
        title="日々のセルフケア"
        description="気分の確認や、リラックス・つながりを整える"
    >
        <x-feature-card href="/condition-checks" title="コンディションチェック">
            <x-slot name="icon">
                <x-icon name="chart-bar" class="w-full h-full" />
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/self-compassion-journals" title="セルフコンパッション日記">
            <x-slot name="icon">
                <x-icon name="heart" class="w-full h-full" />
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/mindfulness" title="マインドフルネス瞑想">
            <x-slot name="icon">
                <x-icon name="sparkles" class="w-full h-full" />
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/copings" title="コーピングリスト">
            <x-slot name="icon">
                <x-icon name="sun" class="w-full h-full" />
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/support-networks" title="サポートネットワーク">
            <x-slot name="icon">
                <x-icon name="user-group" class="w-full h-full" />
            </x-slot>
        </x-feature-card>
    </x-work-section>

    <x-work-section
        title="認知行動療法（CBT）"
        description="思考や行動のパターンを見直す"
    >
        <x-feature-card href="/stressor-and-responses" title="ストレッサーとストレス反応">
            <x-slot name="icon">
                <x-icon name="bolt" class="w-full h-full" />
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/columns" title="認知再構成法(コラム法)">
            <x-slot name="icon">
                <x-icon name="table-cells" class="w-full h-full" />
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/problem-solvings/list" title="問題解決法">
            <x-slot name="icon">
                <x-icon name="light-bulb" class="w-full h-full" />
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/exposures/list" title="エクスポージャー療法">
            <x-slot name="icon">
                <x-icon name="arrow-trending-up" class="w-full h-full" />
            </x-slot>
        </x-feature-card>
    </x-work-section>

    <x-work-section
        title="スキーマ療法"
        description="自分の心のしくみを深く理解する"
    >
        <x-feature-card href="/schema-therapy/chronology" title="スキーマ年表">
            <x-slot name="icon">
                <x-icon name="queue-list" class="w-full h-full" />
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/early-maladaptive-schemas" title="早期不適応的スキーマ">
            <x-slot name="icon">
                <x-icon name="puzzle-piece" class="w-full h-full" />
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/schema-therapy/mode-work/dialogue" title="スキーマモードの対話ワーク">
            <x-slot name="icon">
                <x-icon name="chat-bubble-left-right" class="w-full h-full" />
            </x-slot>
        </x-feature-card>
    </x-work-section>

    <x-work-section
        title="記録・その他"
        description="自由に記録したり、ストレスのパターンを整理する"
        :is-last="true"
    >
        <x-feature-card href="/stress-person-encyclopedias" title="ストレス人物図鑑">
            <x-slot name="icon">
                <x-icon name="book-open" class="w-full h-full" />
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/simple-notepads/list" title="メモ帳">
            <x-slot name="icon">
                <x-icon name="document-text" class="w-full h-full" />
            </x-slot>
        </x-feature-card>
    </x-work-section>
</div>
@endsection
