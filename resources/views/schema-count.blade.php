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
                        <p class="text-sm text-gray-500">ストレッサー記録数</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="totalRecords + '件'"></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🎯</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">スキーマ選択総数</p>
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
                        <div class="flex items-center gap-3">
                            <div class="w-32 sm:w-40 text-sm font-medium text-gray-700 truncate" x-text="schema.name"></div>
                            <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-red-400 rounded-full transition-all duration-500"
                                    :style="'width: ' + getBarWidth(schema.count) + '%'"
                                ></div>
                            </div>
                            <div class="w-12 text-right text-sm font-bold text-gray-800" x-text="schema.count + '回'"></div>
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
                        <div class="flex items-center gap-3">
                            <div class="w-32 sm:w-40 text-sm font-medium text-gray-700 truncate" x-text="schema.name"></div>
                            <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-orange-400 rounded-full transition-all duration-500"
                                    :style="'width: ' + getBarWidth(schema.count) + '%'"
                                ></div>
                            </div>
                            <div class="w-12 text-right text-sm font-bold text-gray-800" x-text="schema.count + '回'"></div>
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
                        <div class="flex items-center gap-3">
                            <div class="w-32 sm:w-40 text-sm font-medium text-gray-700 truncate" x-text="schema.name"></div>
                            <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-yellow-400 rounded-full transition-all duration-500"
                                    :style="'width: ' + getBarWidth(schema.count) + '%'"
                                ></div>
                            </div>
                            <div class="w-12 text-right text-sm font-bold text-gray-800" x-text="schema.count + '回'"></div>
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
                        <div class="flex items-center gap-3">
                            <div class="w-32 sm:w-40 text-sm font-medium text-gray-700 truncate" x-text="schema.name"></div>
                            <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-green-400 rounded-full transition-all duration-500"
                                    :style="'width: ' + getBarWidth(schema.count) + '%'"
                                ></div>
                            </div>
                            <div class="w-12 text-right text-sm font-bold text-gray-800" x-text="schema.count + '回'"></div>
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
                        <div class="flex items-center gap-3">
                            <div class="w-32 sm:w-40 text-sm font-medium text-gray-700 truncate" x-text="schema.name"></div>
                            <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-blue-400 rounded-full transition-all duration-500"
                                    :style="'width: ' + getBarWidth(schema.count) + '%'"
                                ></div>
                            </div>
                            <div class="w-12 text-right text-sm font-bold text-gray-800" x-text="schema.count + '回'"></div>
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
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full font-bold text-white"
                                :class="{
                                    'bg-yellow-400': index === 0,
                                    'bg-gray-400': index === 1,
                                    'bg-orange-400': index === 2,
                                    'bg-gray-300': index > 2
                                }"
                                x-text="index + 1"
                            ></div>
                            <div class="flex-1 text-sm font-medium text-gray-700" x-text="schema.name"></div>
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

        getTopSchemas() {
            const allSchemas = [
                ...this.schemaDefinitions.domain1,
                ...this.schemaDefinitions.domain2,
                ...this.schemaDefinitions.domain3,
                ...this.schemaDefinitions.domain4,
                ...this.schemaDefinitions.domain5
            ];

            return allSchemas
                .map(schema => ({
                    ...schema,
                    count: this.schemaCounts[schema.key] || 0
                }))
                .filter(schema => schema.count > 0)
                .sort((a, b) => b.count - a.count)
                .slice(0, 5);
        }
    };
}
</script>
@endsection
