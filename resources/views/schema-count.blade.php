@extends('layouts.app')

@section('title', 'スキーマカウント')
@section('page-title', 'スキーマカウント')

@section('content')
<div
    x-data="schemaCountApp()"
    x-init="init()"
    class="max-w-2xl mx-auto pb-8"
>
    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16">
        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-teal-500 border-t-transparent"></div>
        <p class="mt-4 text-gray-500">読み込み中...</p>
    </div>

    <!-- メインコンテンツ -->
    <div x-show="!loading" x-cloak>
        <!-- 説明カード -->
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-5 mb-6 border border-indigo-100">
            <h2 class="text-lg font-bold text-gray-800 mb-3">📊 スキーマカウントとは？</h2>
            <div class="text-sm text-gray-600 space-y-2">
                <p>
                    「ストレッサーとストレス反応」で記録した際に選択した<strong class="text-indigo-600">早期不適応スキーマ</strong>の出現回数を集計しています。
                </p>
                <p>
                    カウントが多いスキーマは、あなたがストレス場面で<strong class="text-indigo-600">繰り返し活性化されやすいパターン</strong>を示しています。
                    自分の傾向を知ることで、セルフケアに役立ててみましょう💪
                </p>
            </div>
        </div>

        <!-- 総記録数 -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">📝</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">ストレッサー</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="totalRecords + '件'"></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🎯</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">スキーマ</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="totalSchemaSelections + '回'"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- データがない場合 -->
        <div x-show="totalSchemaSelections === 0" class="bg-white rounded-xl shadow-md p-8 text-center">
            <p class="text-5xl mb-4">🌱</p>
            <p class="text-gray-600 text-lg mb-2">まだスキーマが選択されていません</p>
            <p class="text-gray-500 text-sm mb-4">
                「ストレッサーとストレス反応」でストレス場面を記録し、<br>
                刺激されたスキーマを選択してみましょう
            </p>
            <a href="/stressor-and-responses" class="inline-block px-6 py-3 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition-colors font-medium">
                記録を始める →
            </a>
        </div>

        <!-- スキーマカウント一覧（領域ごと） -->
        <div x-show="totalSchemaSelections > 0" class="space-y-4">
            <!-- 第1領域：切断と拒絶 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-red-500 text-white px-5 py-3">
                    <h3 class="font-bold">第1領域：切断と拒絶</h3>
                </div>
                <div class="p-4 space-y-3">
                    <template x-for="schema in getDomainSchemas('domain1')" :key="schema.key">
                        <div class="relative" x-data="{ showTooltip: false }">
                            <button
                                type="button"
                                class="w-full flex items-center gap-3 cursor-pointer hover:bg-gray-50 rounded-lg p-1 -m-1 transition-colors text-left"
                                @click="showTooltip = !showTooltip"
                            >
                                <div class="w-32 sm:w-40 text-sm font-medium text-gray-700 truncate flex items-center gap-1">
                                    <span x-text="schema.name"></span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-red-400 rounded-full transition-all duration-500"
                                        :style="'width: ' + getBarWidth(schema.count) + '%'"
                                    ></div>
                                </div>
                                <div class="w-12 text-right text-sm font-bold text-gray-800" x-text="schema.count + '回'"></div>
                            </button>
                            <!-- ツールチップ（上に表示） -->
                            <div
                                x-show="showTooltip"
                                @click.outside="showTooltip = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="absolute z-50 left-0 bottom-full mb-2 w-80 p-4 bg-white rounded-lg shadow-lg border border-gray-200"
                            >
                                <div class="text-sm text-gray-600 space-y-2">
                                    <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="getSchemaDetail(schema.key, 'belief')"></span></p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong><span x-text="getSchemaDetail(schema.key, 'behavior')"></span></p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong><span x-text="getSchemaDetail(schema.key, 'background')"></span></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 第2領域：自律性と機能の障害 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-orange-500 text-white px-5 py-3">
                    <h3 class="font-bold">第2領域：自律性と機能の障害</h3>
                </div>
                <div class="p-4 space-y-3">
                    <template x-for="schema in getDomainSchemas('domain2')" :key="schema.key">
                        <div class="relative" x-data="{ showTooltip: false }">
                            <button
                                type="button"
                                class="w-full flex items-center gap-3 cursor-pointer hover:bg-gray-50 rounded-lg p-1 -m-1 transition-colors text-left"
                                @click="showTooltip = !showTooltip"
                            >
                                <div class="w-32 sm:w-40 text-sm font-medium text-gray-700 truncate flex items-center gap-1">
                                    <span x-text="schema.name"></span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-orange-400 rounded-full transition-all duration-500"
                                        :style="'width: ' + getBarWidth(schema.count) + '%'"
                                    ></div>
                                </div>
                                <div class="w-12 text-right text-sm font-bold text-gray-800" x-text="schema.count + '回'"></div>
                            </button>
                            <!-- ツールチップ（上に表示） -->
                            <div
                                x-show="showTooltip"
                                @click.outside="showTooltip = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="absolute z-50 left-0 bottom-full mb-2 w-80 p-4 bg-white rounded-lg shadow-lg border border-gray-200"
                            >
                                <div class="text-sm text-gray-600 space-y-2">
                                    <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="getSchemaDetail(schema.key, 'belief')"></span></p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong><span x-text="getSchemaDetail(schema.key, 'behavior')"></span></p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong><span x-text="getSchemaDetail(schema.key, 'background')"></span></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 第3領域：制限の欠如 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-yellow-500 text-white px-5 py-3">
                    <h3 class="font-bold">第3領域：制限の欠如</h3>
                </div>
                <div class="p-4 space-y-3">
                    <template x-for="schema in getDomainSchemas('domain3')" :key="schema.key">
                        <div class="relative" x-data="{ showTooltip: false }">
                            <button
                                type="button"
                                class="w-full flex items-center gap-3 cursor-pointer hover:bg-gray-50 rounded-lg p-1 -m-1 transition-colors text-left"
                                @click="showTooltip = !showTooltip"
                            >
                                <div class="w-32 sm:w-40 text-sm font-medium text-gray-700 truncate flex items-center gap-1">
                                    <span x-text="schema.name"></span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-yellow-400 rounded-full transition-all duration-500"
                                        :style="'width: ' + getBarWidth(schema.count) + '%'"
                                    ></div>
                                </div>
                                <div class="w-12 text-right text-sm font-bold text-gray-800" x-text="schema.count + '回'"></div>
                            </button>
                            <!-- ツールチップ（上に表示） -->
                            <div
                                x-show="showTooltip"
                                @click.outside="showTooltip = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="absolute z-50 left-0 bottom-full mb-2 w-80 p-4 bg-white rounded-lg shadow-lg border border-gray-200"
                            >
                                <div class="text-sm text-gray-600 space-y-2">
                                    <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="getSchemaDetail(schema.key, 'belief')"></span></p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong><span x-text="getSchemaDetail(schema.key, 'behavior')"></span></p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong><span x-text="getSchemaDetail(schema.key, 'background')"></span></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 第4領域：他者への追従 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-green-500 text-white px-5 py-3">
                    <h3 class="font-bold">第4領域：他者への追従</h3>
                </div>
                <div class="p-4 space-y-3">
                    <template x-for="schema in getDomainSchemas('domain4')" :key="schema.key">
                        <div class="relative" x-data="{ showTooltip: false }">
                            <button
                                type="button"
                                class="w-full flex items-center gap-3 cursor-pointer hover:bg-gray-50 rounded-lg p-1 -m-1 transition-colors text-left"
                                @click="showTooltip = !showTooltip"
                            >
                                <div class="w-32 sm:w-40 text-sm font-medium text-gray-700 truncate flex items-center gap-1">
                                    <span x-text="schema.name"></span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-green-400 rounded-full transition-all duration-500"
                                        :style="'width: ' + getBarWidth(schema.count) + '%'"
                                    ></div>
                                </div>
                                <div class="w-12 text-right text-sm font-bold text-gray-800" x-text="schema.count + '回'"></div>
                            </button>
                            <!-- ツールチップ（上に表示） -->
                            <div
                                x-show="showTooltip"
                                @click.outside="showTooltip = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="absolute z-50 left-0 bottom-full mb-2 w-80 p-4 bg-white rounded-lg shadow-lg border border-gray-200"
                            >
                                <div class="text-sm text-gray-600 space-y-2">
                                    <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="getSchemaDetail(schema.key, 'belief')"></span></p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong><span x-text="getSchemaDetail(schema.key, 'behavior')"></span></p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong><span x-text="getSchemaDetail(schema.key, 'background')"></span></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 第5領域：過剰警戒と抑制 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-blue-500 text-white px-5 py-3">
                    <h3 class="font-bold">第5領域：過剰警戒と抑制</h3>
                </div>
                <div class="p-4 space-y-3">
                    <template x-for="schema in getDomainSchemas('domain5')" :key="schema.key">
                        <div class="relative" x-data="{ showTooltip: false }">
                            <button
                                type="button"
                                class="w-full flex items-center gap-3 cursor-pointer hover:bg-gray-50 rounded-lg p-1 -m-1 transition-colors text-left"
                                @click="showTooltip = !showTooltip"
                            >
                                <div class="w-32 sm:w-40 text-sm font-medium text-gray-700 truncate flex items-center gap-1">
                                    <span x-text="schema.name"></span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-blue-400 rounded-full transition-all duration-500"
                                        :style="'width: ' + getBarWidth(schema.count) + '%'"
                                    ></div>
                                </div>
                                <div class="w-12 text-right text-sm font-bold text-gray-800" x-text="schema.count + '回'"></div>
                            </button>
                            <!-- ツールチップ（上に表示） -->
                            <div
                                x-show="showTooltip"
                                @click.outside="showTooltip = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="absolute z-50 left-0 bottom-full mb-2 w-80 p-4 bg-white rounded-lg shadow-lg border border-gray-200"
                            >
                                <div class="text-sm text-gray-600 space-y-2">
                                    <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="getSchemaDetail(schema.key, 'belief')"></span></p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong><span x-text="getSchemaDetail(schema.key, 'behavior')"></span></p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong><span x-text="getSchemaDetail(schema.key, 'background')"></span></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- TOP5ランキング -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden" x-show="getTopSchemas().length > 0">
                <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white px-5 py-3">
                    <h3 class="font-bold">🏆 TOP5 よく活性化されるスキーマ</h3>
                </div>
                <div class="p-4 space-y-3">
                    <template x-for="(schema, index) in getTopSchemas()" :key="schema.key">
                        <div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-b-0">
                            <div class="w-6 text-lg font-bold text-gray-600" x-text="index + 1"></div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800" x-text="schema.name"></div>
                                <div
                                    class="text-xs px-2 py-0.5 rounded-full inline-block mt-1"
                                    :class="getDomainBadgeClass(schema.domain)"
                                    x-text="getDomainName(schema.domain)"
                                ></div>
                            </div>
                            <div class="text-lg font-bold text-indigo-600" x-text="schema.count + '回'"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function schemaCountApp() {
    return {
        loading: true,
        records: [],
        schemaCounts: {},
        totalRecords: 0,
        totalSchemaSelections: 0,
        maxCount: 0,

        // スキーマ定義
        schemaDefinitions: {
            domain1: [
                { key: 'abandonment', name: '見捨てられ／不安定' },
                { key: 'mistrust_abuse', name: '不信／虐待' },
                { key: 'emotional_deprivation', name: '情緒的剥奪' },
                { key: 'defectiveness_shame', name: '欠陥／恥' },
                { key: 'social_isolation', name: '社会的孤立／疎外' }
            ],
            domain2: [
                { key: 'dependence_incompetence', name: '依存／無能' },
                { key: 'vulnerability_to_harm', name: '損害や疾病に対する脆弱性' },
                { key: 'enmeshment', name: '巻き込まれ／未発達な自己' },
                { key: 'failure', name: '失敗' }
            ],
            domain3: [
                { key: 'entitlement_grandiosity', name: '権利要求／尊大さ' },
                { key: 'insufficient_self_control', name: '自制と自律の欠如' }
            ],
            domain4: [
                { key: 'subjugation', name: '服従' },
                { key: 'self_sacrifice', name: '自己犠牲' },
                { key: 'approval_seeking', name: '承認欲求／評価の追求' }
            ],
            domain5: [
                { key: 'negativity_pessimism', name: '否定／悲観' },
                { key: 'emotional_inhibition', name: '感情抑制' },
                { key: 'unrelenting_standards', name: '厳密な基準／過度の批判' },
                { key: 'punitiveness', name: '罰への懲罰的志向' }
            ]
        },

        // スキーマ詳細情報
        schemaDetails: {
            'abandonment': {
                belief: '「大切な人は自分を置いて去っていく。愛する人は死ぬか、他の人を選んで自分を捨てる」という予感に常に支配されています。',
                behavior: 'パートナーの連絡が遅れるだけでパニックになり、相手を激しく責めたり逆に必死にしがみついたりします。一方で、別れの痛みを恐れるあまり、最初から深い関係を避け、孤独の中に引きこもることもあります。',
                background: '幼少期に親との死別や離婚を経験した、あるいは親の機嫌によって可愛がられたり無視されたりする「気まぐれな養育」を受けたことが影響しています。'
            },
            'mistrust_abuse': {
                belief: '「人は私を傷つけ、利用し、騙す存在だ。油断すると攻撃される」という強い警戒心を持っており、世界を敵対的な場所だと感じています。',
                behavior: '他者の親切に「裏がある」と疑い、心を開けません。自分が被害者にならないよう先制攻撃で他者を威圧したり、逆に自分を粗末に扱う相手をあえて選んで「やはり人は信じられない」と確信を深めるループに陥りがちです。',
                background: '幼少期に家族から身体的・心理的な虐待を受けたり、信頼していた人にひどく裏切られたりした過酷な体験が根底にあります。'
            },
            'emotional_deprivation': {
                belief: '「自分の話を聴き、理解し、守ってくれる人は誰もいない。自分は生涯、心理的に満たされることはない」という慢性的で静かな欠乏感です。',
                behavior: '自分のニーズを伝えることを諦めているため、何も言わずに不機嫌になったり、原因不明の体調不良（心身症）として苦しさを訴えたりします。他者と関わっていても、心のどこかで「どうせ誰もわかってくれない」と冷めています。',
                background: '親が感情的に冷淡だった、または親自身に余裕がなく、子供の感情的な欲求（甘えや共感）に関心を示さない環境で育った場合に見られます。'
            },
            'defectiveness_shame': {
                belief: '「自分は本質的にダメな人間で、愛される価値がない。本当の自分を知られたら、きっと誰もが呆れて離れていく」という自分自身への強い嫌悪感です。',
                behavior: '批判や拒絶に極めて敏感で、少しの注意でも全人格を否定されたように落ち込みます。この「恥」を隠すために、完璧な自分を演じて武装したり、逆に自虐的な態度をとって他人に自分を攻撃させるような行動をとることがあります。',
                background: '親から否定的なレッテルを貼られたり、欠点ばかりを指摘されたりして、ありのままの自分を尊重された経験が乏しいことが原因です。'
            },
            'social_isolation': {
                belief: '「自分は他の人とは根本的に異なっていて、どこにも居場所がない。自分はどのグループにも馴染めない異邦人だ」という感覚です。',
                behavior: '職場や友人グループの中にいても、常に一歩引いた「部外者」として振る舞います。能力があっても集団を避け、フリーランスや一匹狼的な生き方を選びがちです。交流の場では、自分だけが浮いているような強い違和感に苦しみます。',
                background: '幼少期に家族が周囲から浮いていた、自分の外見や家柄が周囲と違っていた、あるいは学校でいじめを受け「みんなとは違う」と強く意識せざるを得なかった経験が関係しています。'
            },
            'dependence_incompetence': {
                belief: '「自分一人では日々の生活や重大な決定をこなせない。誰か頼れる人がいないと、自分は生きていけない子供のような存在だ」という無力感です。',
                behavior: '進路決定から今日の献立まで、誰かに決めてもらわないと不安で動けません。依存先がいなくなると激しいパニックに陥り、すぐに新しい「保護者役」を探そうとします。自分で新しいことに挑戦するのを極端に避けます。',
                background: '親が過保護で子供の代わりに何でもやってしまった、あるいは逆に親が子供の判断を常に否定し、自信を奪い続けた場合に形成されます。'
            },
            'vulnerability_to_harm': {
                belief: '「いつか必ず、恐ろしい災難（不治の病、強盗、飛行機事故、破産など）が自分を襲う。世界は危険に満ちていて、自分は防ぐことができない」という過剰な恐怖です。',
                behavior: '些細な体の異変で重病を疑って病院を巡ったり、ニュースを見て過度に不安になり外出を控えたりします。常に最悪の事態を想定して予防策に奔走するため、リラックスして人生を楽しむことができません。',
                background: '親自身が極度の心配性で、「世の中は危ないところだ」「油断すると大変なことになる」という恐怖のメッセージを繰り返し伝えられたことが影響しています。'
            },
            'enmeshment': {
                belief: '「自分は重要な誰か（親など）と一体であり、その人なしでは生きられない。自分だけのアイデンティティや人生を持つ資格がない」という未分化な状態です。',
                behavior: '成人しても親の期待や意見に逆らえず、進路、結婚、キャリアなどが親の意向で決まりがちです。「自分は何がしたいのか」がわからず、自立しようとすると罪悪感や空虚感に襲われます。',
                background: '親が子離れできず、子供を自分の一部のように扱い、子供が個人としての境界を発達させることを妨げた場合に生まれます。'
            },
            'failure': {
                belief: '「自分は他者と比べて根本的に能力が劣っており、何をやっても失敗する運命だ。成功は自分には縁がない」という信念です。',
                behavior: '失敗を恐れるあまり挑戦を避け、能力を十分に発揮できません。うまくいきそうになると無意識に自分で足を引っ張り、「やっぱり自分はダメだ」と確信する結果を招きます（自己成就予言）。',
                background: '親から「お前は出来が悪い」と言われ続けた、あるいは兄弟・同級生と常に比較されて「劣等生」の烙印を押された経験が関係しています。'
            },
            'entitlement_grandiosity': {
                belief: '「自分は特別な存在であり、一般のルールや制限は自分には適用されない。他者より優遇されて当然だ」という特権意識です。',
                behavior: '順番待ちに耐えられない、批判されると激怒する、他者の気持ちや権利を軽視します。自分の成功や地位を誇示し、周囲に称賛や服従を求めます。裏では脆い自己像を隠していることも多いです。',
                background: '幼少期に過度に甘やかされ「君は特別」と賞賛され続けた、あるいは逆に愛情不足を補うために空想上の「特別さ」にしがみついた場合があります。'
            },
            'insufficient_self_control': {
                belief: '「欲求を我慢したり、長期的な目標のために努力するのは苦痛すぎる。今の気分がすべてだ」という衝動性です。',
                behavior: '衝動買い、過食、依存症、仕事や勉強の先延ばしなど、自制が必要な場面で失敗しがちです。感情を爆発させて後悔する、目標を達成できない自分にさらに落ち込むという悪循環に陥りやすいです。',
                background: '親がルールや限度を設けなかった（放任）、あるいは親自身が自己制御に問題を抱えており、モデルとなる自己規律を学ぶ機会がなかった場合に見られます。'
            },
            'subjugation': {
                belief: '「自分のニーズや感情を表に出すと、相手から見捨てられるか、罰を受ける。安全でいるためには相手に従わねばならない」という恐怖に基づく服従です。',
                behavior: '理不尽な要求にも「ノー」と言えず、自分の意見を抑え込みます。蓄積した怒りが爆発するか、心身の不調として表れることもあります。支配的なパートナーや上司のもとに繰り返し身を置きがちです。',
                background: '親が非常に支配的で、子供の意見を許さず、従わないと怒りや拒絶で罰した場合に形成されやすいです。'
            },
            'self_sacrifice': {
                belief: '「自分よりも他者のニーズを優先しなければ、自分は価値のない人間だ。他者の苦しみを放置するのは罪だ」という過度の責任感です。',
                behavior: '頼まれてもいないのに他人の世話を焼き、自分の心身を削ります。一見、服従スキーマに似ていますが、動機は「恐怖」ではなく「相手への共感や罪悪感」です。長期的には疲れ果て、助けている相手に対して恨みを感じることもあります。',
                background: '親が弱く、子供が早くから「親の面倒を見る役」を担わされた場合、あるいは他者を助けた時だけ褒められ認められた場合に育まれます。'
            },
            'approval_seeking': {
                belief: '「他者からの称賛や認知を得ることが、自分の価値を証明する唯一の方法だ。本当の自分には価値がない」という信念です。',
                behavior: '人の評価を過度に気にし、地位・外見・成功などを通じて認められようと努力します。「いいね」の数や昇進など外からの評価で一喜一憂し、内面の充足感を得ることが難しいです。',
                background: '親が条件付きの愛情を与え、成績や外見など「成果」を出した時だけ子供を認めた環境が関係しています。ありのままの子供は無視されたか、低く評価されました。'
            },
            'negativity_pessimism': {
                belief: '「人生は結局うまくいかない。良いことがあっても、どうせ悪いことが起きる。心配と警戒を怠れば大変なことになる」という悲観です。',
                behavior: '物事の悪い面ばかりに目が行き、良いニュースを素直に喜べません。「最悪のケースを想定しておけば傷つかない」と、無意識に自分や他者の期待を下げます。周囲から「ネガティブな人」と敬遠されることも。',
                background: '親が慢性的に悲観的だった、家庭内に解決しない問題（貧困、病気など）が常に存在した、あるいは幼少期に不幸な出来事が連続した経験が影響します。'
            },
            'emotional_inhibition': {
                belief: '「感情を表に出すのは恥ずかしい、危険だ、あるいは相手に迷惑だ。自分の本当の気持ちは抑えるべきだ」という信念です。',
                behavior: '怒り、悲しみ、喜びなどの感情表現を極端に抑え、常に冷静・理性的であろうとします。親密な関係でも心を開けず、「何を考えているかわからない」と言われがちです。身体症状や突然の感情爆発として抑圧が表れることも。',
                background: '親が感情表現を「弱さ」や「恥」として非難した、あるいは家庭内で感情を出すと危険（暴力など）だった場合に形成されます。'
            },
            'unrelenting_standards': {
                belief: '「自分は常に最高の結果を出さねばならない。普通の成果では不十分だ。少しでも落ち度があれば自分は失格だ」という過度の完璧主義です。',
                behavior: '仕事や勉強に過剰な時間を費やし、趣味や人間関係を犠牲にします。他者にも高い基準を押し付けてしまうことがあります。完璧を達成しても満足は一瞬で、次の目標に追い立てられます。',
                background: '親が非常に高い期待を持ち、子供が優秀な結果を出した時だけ愛情や承認を与えた環境が典型的です。「ベストでなければ愛されない」と学習しました。'
            },
            'punitiveness': {
                belief: '「過ちを犯した者（自分を含む）は厳しく罰せられるべきだ。許しや同情は甘やかしだ」という厳格で容赦のない姿勢です。',
                behavior: '自分のミスに対して自己嫌悪や自傷的行動に走る、他者の失敗を許せず怒りを爆発させる、復讐心が強いといった形で現れます。「罰を与えないと人は学ばない」と信じています。',
                background: '親が非常に厳しく、子供の失敗に過酷な罰を与えた場合に形成されます。許しや思いやりを示すモデルがなく、「厳罰こそ正義」と内面化しました。'
            }
        },

        async init() {
            await this.loadData();
        },

        async loadData() {
            try {
                const res = await fetch('/api/stressor-and-responses');
                if (res.ok) {
                    this.records = await res.json();
                    this.totalRecords = this.records.length;
                    this.calculateCounts();
                }
            } catch (error) {
                console.error('データの取得に失敗しました:', error);
            } finally {
                this.loading = false;
            }
        },

        calculateCounts() {
            // 全スキーマの初期カウントを0に
            const allSchemas = [
                ...this.schemaDefinitions.domain1,
                ...this.schemaDefinitions.domain2,
                ...this.schemaDefinitions.domain3,
                ...this.schemaDefinitions.domain4,
                ...this.schemaDefinitions.domain5
            ];

            allSchemas.forEach(schema => {
                this.schemaCounts[schema.key] = 0;
            });

            // 各レコードのスキーマをカウント
            this.totalSchemaSelections = 0;
            this.records.forEach(record => {
                if (record.stimulated_schemas && Array.isArray(record.stimulated_schemas)) {
                    record.stimulated_schemas.forEach(schemaKey => {
                        if (this.schemaCounts.hasOwnProperty(schemaKey)) {
                            this.schemaCounts[schemaKey]++;
                            this.totalSchemaSelections++;
                        }
                    });
                }
            });

            // 最大値を計算
            this.maxCount = Math.max(...Object.values(this.schemaCounts), 1);
        },

        getDomainSchemas(domain) {
            return this.schemaDefinitions[domain].map(schema => ({
                ...schema,
                count: this.schemaCounts[schema.key] || 0
            }));
        },

        getBarWidth(count) {
            if (this.maxCount === 0) return 0;
            return (count / this.maxCount) * 100;
        },

        getSchemaDetail(key, type) {
            const detail = this.schemaDetails[key];
            return detail ? detail[type] : '';
        },

        getSchemaDomain(key) {
            for (const [domain, schemas] of Object.entries(this.schemaDefinitions)) {
                if (schemas.some(s => s.key === key)) {
                    return domain;
                }
            }
            return null;
        },

        getDomainName(domain) {
            const domainNames = {
                'domain1': '切断と拒絶',
                'domain2': '自律性と機能の障害',
                'domain3': '制限の欠如',
                'domain4': '他者への追従',
                'domain5': '過剰警戒と抑制'
            };
            return domainNames[domain] || '';
        },

        getDomainBadgeClass(domain) {
            const classes = {
                'domain1': 'bg-red-100 text-red-700',
                'domain2': 'bg-orange-100 text-orange-700',
                'domain3': 'bg-yellow-100 text-yellow-700',
                'domain4': 'bg-green-100 text-green-700',
                'domain5': 'bg-blue-100 text-blue-700'
            };
            return classes[domain] || 'bg-gray-100 text-gray-700';
        },

        getTopSchemas() {
            const allSchemas = [];
            for (const [domain, schemas] of Object.entries(this.schemaDefinitions)) {
                schemas.forEach(schema => {
                    allSchemas.push({
                        ...schema,
                        domain: domain,
                        count: this.schemaCounts[schema.key] || 0
                    });
                });
            }

            return allSchemas
                .filter(schema => schema.count > 0)
                .sort((a, b) => b.count - a.count)
                .slice(0, 5);
        }
    };
}
</script>
@endsection
