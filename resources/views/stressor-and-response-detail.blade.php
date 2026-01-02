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
                'abandonment': '「大切な人は自分を置いて去っていく。愛する人は死ぬか、他の人を選んで自分を捨てる」という予感に常に支配されています。パートナーの連絡が遅れるだけでパニックになり、相手を激しく責めたり必死にしがみついたりします。',
                'mistrust_abuse': '「人は私を傷つけ、利用し、騙す存在だ。油断すると攻撃される」という強い警戒心を持っており、世界を敵対的な場所だと感じています。他者の親切に「裏がある」と疑い、心を開けません。',
                'emotional_deprivation': '「自分の話を聴き、理解し、守ってくれる人は誰もいない。自分は生涯、心理的に満たされることはない」という慢性的で静かな欠乏感です。他者と関わっていても、心のどこかで「どうせ誰もわかってくれない」と冷めています。',
                'defectiveness_shame': '「自分は本質的にダメな人間で、愛される価値がない。本当の自分を知られたら、きっと誰もが呆れて離れていく」という自分自身への強い嫌悪感です。批判や拒絶に極めて敏感で、少しの注意でも全人格を否定されたように落ち込みます。',
                'social_isolation': '「自分は他の人とは根本的に異なっていて、どこにも居場所がない。自分はどのグループにも馴染めない異邦人だ」という感覚です。職場や友人グループの中にいても、常に一歩引いた「部外者」として振る舞います。',
                'dependence_incompetence': '「自分一人では日々の生活や重大な決定をこなせない。誰か頼れる人がいないと、自分は生きていけない子供のような存在だ」という無力感です。進路決定から今日の献立まで、誰かに決めてもらわないと不安で動けません。',
                'vulnerability_to_harm': '「いつか必ず、恐ろしい災難が自分を襲う。世界は危険に満ちていて、自分は防ぐことができない」という過剰な恐怖です。常に最悪の事態を想定して予防策に奔走するため、リラックスして人生を楽しむことができません。',
                'enmeshment': '「自分と親（またはパートナー）の間に境界線はない。相手の幸せが自分の幸せであり、相手を離れて自分一人の人生を歩むことは裏切りだ」という感覚です。相手の機嫌によって自分の気分が180度変わります。',
                'failure': '「自分は同年代の人に比べて根本的に能力が低く、何をやっても最終的には失敗する。自分は落ちこぼれだ」という確信です。実際に能力があっても「これは運が良かっただけだ」と成功を無視します。',
                'entitlement_grandiosity': '「自分は特別な存在であり、一般のルールに従う必要はない。欲しいものは、他者の都合に関わらず手に入れる権利がある」という感覚です。他者のニーズを軽視し、自分の欲望を最優先します。',
                'insufficient_self_control': '「不快なことや退屈なこと、欲求不満を我慢することは耐えられない。自分をコントロールするのは無理だ」という諦めです。目標達成のための地道な努力ができず、すぐに投げ出します。',
                'subjugation': '「相手に従わないと怒られる、見捨てられる、あるいは報復される。自分の欲求を出すことは、トラブルを引き起こす危険なことだ」という恐怖に基づいた感覚です。自分の本音を押し殺して相手の言いなりになります。',
                'self_sacrifice': '「他人の苦しみを放っておくのは罪悪感がある。自分のことを後回しにしてでも、相手を助け、面倒を見るのが自分の役割だ」という義務感です。頼まれてもいないのに他人の世話を焼き、自分の心身を削ります。',
                'approval_seeking': '「ありのままの自分には価値がない。他者から称賛され、認められて初めて、自分は存在していいことになる」という、外側からの評価への依存です。「どう見られるか」を行動基準にします。',
                'negativity_pessimism': '「人生のポジティブな面はまやかしで、ネガティブな側面こそが真実だ。今は良くても、どうせ最後には悪いことが起きる」という悲観主義です。常に最悪のシナリオを想定し、周囲の明るい話題にも水を差してしまいます。',
                'emotional_inhibition': '「感情を素直に出すのは、恥ずべきことであり、自分を失う危険なことだ。常に理性的でなければならない」という自制心です。感情の起伏が乏しく、周囲からは「何を考えているかわからない」と思われることがあります。',
                'unrelenting_standards': '「自分も他人も、常に最高水準の基準を満たさなければならない。完璧でないことは失敗であり、批判に値する」という、休息を許さない強迫的な信念です。完璧主義で、効率やルール、細部に過剰にこだわります。',
                'punitiveness': '「間違いを犯した人間は、情状酌量の余地なく厳しく罰せられるべきだ。自分も他人も、ミスをすれば報いを受けるのが当然だ」という不寛容な信念です。他人のミスを許せず、攻撃的に非難します。'
            };
            return descriptions[key] || '';
        }
    };
}
</script>
@endsection
