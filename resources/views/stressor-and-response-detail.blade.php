@extends('layouts.app')

@section('title', 'ストレッサーとストレス反応詳細')
@section('page-title', 'ストレッサーとストレス反応')

@section('content')
<div x-data="stressorDetailApp()" x-init="init()" x-cloak>
    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16">
        <svg class="animate-spin h-8 w-8 mx-auto text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- コンテンツ -->
    <div x-show="!loading && item" class="space-y-4">
        <!-- ヘッダー -->
        <div class="flex items-center justify-between mb-4">
            <a href="/stressor-and-responses/list" class="text-teal-600 hover:text-teal-800 flex items-center gap-1 transition-colors">
                ←
            </a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500" x-text="formatDate(item?.created_at)"></span>
                <!-- 編集ページへのリンク -->
                <a
                    :href="'/stressor-and-responses/' + itemId + '/edit'"
                    class="text-teal-600 hover:text-teal-800 transition-colors p-2 rounded hover:bg-teal-50"
                    title="編集する"
                >
                    ✏️
                </a>
                <button
                    @click="deleteItem()"
                    class="text-red-400 hover:text-red-600 transition-colors p-2 rounded hover:bg-red-50"
                    title="削除"
                >
                    🗑️
                </button>
            </div>
        </div>

        <!-- 詳細表示 -->
        <div class="space-y-4">
            <!-- ストレッサー -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-4">
                    ストレッサー
                </h3>
                <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" x-text="item?.stressor || '未入力'"></p>
            </div>

            <!-- ストレス反応セクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-4">
                    ストレス反応
                </h3>
                
                <div class="space-y-4">
                    <!-- 認知（自動思考） -->
                    <div class="bg-amber-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-amber-600 mb-2">
                            認知（自動思考）
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.cognition ? 'text-gray-400' : ''" x-text="item?.cognition || '未入力'"></p>
                    </div>

                    <!-- 気分・感情 -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-blue-600 mb-2">
                            気分・感情
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.mood ? 'text-gray-400' : ''" x-text="item?.mood || '未入力'"></p>
                    </div>

                    <!-- 身体反応 -->
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-green-600 mb-2">
                            身体反応
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.body_reaction ? 'text-gray-400' : ''" x-text="item?.body_reaction || '未入力'"></p>
                    </div>

                    <!-- 行動 -->
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-purple-600 mb-2">
                            行動
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.behavior ? 'text-gray-400' : ''" x-text="item?.behavior || '未入力'"></p>
                    </div>
                </div>
            </div>

            <!-- 刺激されたスキーマ -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-4">
                    刺激されたスキーマ
                </h3>
                <div x-show="item?.stimulated_schemas && item.stimulated_schemas.length > 0">
                    <div class="flex flex-wrap gap-2">
                        <template x-for="schemaKey in (item?.stimulated_schemas || [])" :key="schemaKey">
                            <div class="relative" x-data="{ showTooltip: false }">
                                <button
                                    type="button"
                                    @click="showTooltip = !showTooltip"
                                    @click.outside="showTooltip = false"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium hover:bg-indigo-200 transition-colors cursor-pointer"
                                >
                                    <span x-text="getSchemaName(schemaKey)"></span>
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                <!-- ツールチップ -->
                                <div
                                    x-show="showTooltip"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform scale-100"
                                    x-transition:leave-end="opacity-0 transform scale-95"
                                    class="absolute z-10 left-0 top-full mt-2 w-72 p-3 bg-white rounded-lg shadow-lg border border-gray-200"
                                >
                                    <p class="text-sm text-gray-600" x-text="getSchemaDescription(schemaKey)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div x-show="!item?.stimulated_schemas || item.stimulated_schemas.length === 0">
                    <p class="text-gray-400">未選択</p>
                </div>
            </div>
        </div>
    </div>

    <!-- エラー -->
    <div x-show="!loading && !item" class="text-center py-16 bg-white rounded-xl shadow-md">
        <p class="text-6xl mb-4">😢</p>
        <p class="text-gray-600 text-lg mb-2">データが見つかりません</p>
        <a href="/stressor-and-responses/list" class="inline-block mt-4 text-teal-600 hover:text-teal-800">
            ←
        </a>
    </div>
</div>

<script>
function stressorDetailApp() {
    return {
        item: null,
        loading: true,
        itemId: {{ $itemId }},

        async init() {
            await this.loadItem();
        },

        async loadItem() {
            try {
                const res = await fetch(`/api/stressor-and-responses/${this.itemId}`);
                if (res.ok) {
                    this.item = await res.json();
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        async deleteItem() {
            if (!confirm('この記録を削除しますか？')) return;

            try {
                await fetch(`/api/stressor-and-responses/${this.itemId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                window.location.href = '/stressor-and-responses/list';
            } catch (e) {
                console.error(e);
            }
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        getSchemaName(key) {
            const schemaNames = {
                'abandonment': '見捨てられ／不安定',
                'mistrust_abuse': '不信／虐待',
                'emotional_deprivation': '情緒的剥奪',
                'defectiveness_shame': '欠陥／恥',
                'social_isolation': '社会的孤立／疎外',
                'dependence_incompetence': '依存／無能',
                'vulnerability_to_harm': '損害や疾病に対する脆弱性',
                'enmeshment': '巻き込まれ／未発達な自己',
                'failure': '失敗',
                'entitlement_grandiosity': '権利要求／尊大さ',
                'insufficient_self_control': '自制と自律の欠如',
                'subjugation': '服従',
                'self_sacrifice': '自己犠牲',
                'approval_seeking': '承認欲求／評価の追求',
                'negativity_pessimism': '否定／悲観',
                'emotional_inhibition': '感情抑制',
                'unrelenting_standards': '厳密な基準／過度の批判',
                'punitiveness': '罰への懲罰的志向'
            };
            return schemaNames[key] || key;
        },

        getSchemaDescription(key) {
            const descriptions = {
                'abandonment': '「大切な人は自分を置いて去っていく」という予感に常に支配され、パートナーの連絡が遅れるだけでパニックになることがあります。',
                'mistrust_abuse': '「人は私を傷つけ、利用し、騙す存在だ」という強い警戒心を持ち、他者の親切に「裏がある」と疑ってしまいます。',
                'emotional_deprivation': '「自分の話を聴き、理解し、守ってくれる人は誰もいない」という慢性的な欠乏感を抱えています。',
                'defectiveness_shame': '「自分は本質的にダメな人間で、愛される価値がない」という自己嫌悪感を持ち、批判に極めて敏感です。',
                'social_isolation': '「自分は他の人とは根本的に異なっていて、どこにも居場所がない」という孤立感を感じています。',
                'dependence_incompetence': '「自分一人では日々の生活や重大な決定をこなせない」という無力感を抱え、誰かに決めてもらわないと不安です。',
                'vulnerability_to_harm': '「いつか必ず恐ろしい災難が自分を襲う」という過剰な恐怖を持ち、常に最悪の事態を想定します。',
                'enmeshment': '「自分と親（またはパートナー）の間に境界線はない」と感じ、相手の機嫌で自分の気分が変わります。',
                'failure': '「自分は同年代の人に比べて能力が低く、何をやっても最終的には失敗する」という確信を持っています。',
                'entitlement_grandiosity': '「自分は特別な存在であり、一般のルールに従う必要はない」と感じ、欲しいものを最優先します。',
                'insufficient_self_control': '「不快なことや欲求不満を我慢することは耐えられない」と感じ、衝動的に行動しがちです。',
                'subjugation': '「相手に従わないと怒られる、見捨てられる」という恐怖から、本音を押し殺して言いなりになります。',
                'self_sacrifice': '「他人の苦しみを放っておくのは罪悪感がある」と感じ、頼まれてもいないのに他人の世話を焼きます。',
                'approval_seeking': '「ありのままの自分には価値がない」と感じ、他者からの称賛や認められることに依存しています。',
                'negativity_pessimism': '「人生のポジティブな面はまやかしで、ネガティブな側面こそが真実だ」という悲観主義を持っています。',
                'emotional_inhibition': '「感情を素直に出すのは恥ずべきこと」と感じ、感情の起伏が乏しく、常に理性的であろうとします。',
                'unrelenting_standards': '「常に最高水準の基準を満たさなければならない」という完璧主義で、効率や細部に過剰にこだわります。',
                'punitiveness': '「間違いを犯した人間は厳しく罰せられるべきだ」と考え、自分にも他人にも不寛容です。'
            };
            return descriptions[key] || '';
        }
    };
}
</script>
@endsection
