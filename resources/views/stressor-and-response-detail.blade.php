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
                                    class="absolute z-10 left-0 top-full mt-2 w-80 p-4 bg-white rounded-lg shadow-lg border border-gray-200"
                                >
                                    <div class="text-sm text-gray-600 space-y-2">
                                        <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="getSchemaDetail(schemaKey, 'belief')"></span></p>
                                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong><span x-text="getSchemaDetail(schemaKey, 'behavior')"></span></p>
                                        <p><strong class="text-gray-700">背景・ルーツ：</strong><span x-text="getSchemaDetail(schemaKey, 'background')"></span></p>
                                    </div>
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

        getSchemaDetail(key, type) {
            const schemaDetails = {
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
                    belief: '「自分と親（またはパートナー）の間に境界線はない。相手の幸せが自分の幸せであり、相手を離れて自分一人の人生を歩むことは裏切りだ」という感覚です。',
                    behavior: '相手の機嫌によって自分の気分が180度変わります。自分の好みや意見がわからず、相手がいないと「空っぽ」になったように感じます。大人になっても親にすべてを報告しなければならない、といった心理的拘束を感じ続けます。',
                    background: '親が子供を自分の延長線上として扱い、子供が自立しようとすると寂しがったり怒ったりして、心理的な分離を許さなかった環境が原因です。'
                },
                'failure': {
                    belief: '「自分は同年代の人に比べて根本的に能力が低く、何をやっても最終的には失敗する。自分は落ちこぼれだ」という確信です。',
                    behavior: '実際に能力があっても「これは運が良かっただけだ」と成功を無視します。失敗するのが怖いために、最初から挑戦を諦めたり、わざと準備を怠って「やっぱりダメだった」という予測を的中させる（自己ハンディキャップ）行動をとります。',
                    background: '学校や家庭で「お前はどんくさい」「何をやってもダメだ」と批判され続けたり、優秀な兄弟と比較されて挫折感を味わい続けたりした経験が根底にあります。'
                },
                'entitlement_grandiosity': {
                    belief: '「自分は特別な存在であり、一般のルールに従う必要はない。欲しいものは、他者の都合に関わらず手に入れる権利がある」という感覚です。',
                    behavior: '他者のニーズを軽視し、自分の欲望を最優先します。順番待ちを嫌う、借金を返さない、相手が思い通りにならないと激昂するといった行動が見られます。一見自信家ですが、実は欠乏感を隠すために虚勢を張っている場合も多いです。',
                    background: '幼少期に甘やかされすぎて境界線を学ばなかった、あるいは逆にひどく冷遇された反動で「もう二度と我慢したくない」と強く思うようになったことが原因です。'
                },
                'insufficient_self_control': {
                    belief: '「不快なことや退屈なこと、欲求不満を我慢することは耐えられない。自分をコントロールするのは無理だ」という諦めです。',
                    behavior: '目標達成のための地道な努力ができず、すぐに投げ出します。感情を抑えられず衝動的に行動し、アルコールや買い物、過食などの依存に走りやすい傾向があります。約束を守れず、社会生活に支障をきたすことも少なくありません。',
                    background: '幼少期に適切な「しつけ」を受けなかった、または苦痛な感情に対処する力を養う機会がなく、衝動のままに動くことが許容（あるいは放置）されていたことが影響しています。'
                },
                'subjugation': {
                    belief: '「相手に従わないと怒られる、見捨てられる、あるいは報復される。自分の欲求を出すことは、トラブルを引き起こす危険なことだ」という恐怖に基づいた感覚です。',
                    behavior: '自分の本音を押し殺して相手の言いなりになります。表面上は従順ですが、内面には強い怒りが溜まっており、わざと遅刻したり仕事を遅らせたりする（受動的攻撃）や、突然の感情爆発として現れることがあります。',
                    background: '非常に支配的な親や、怒ると手が付けられない親のもとで、自分の意思を持つことが生存への脅威だった環境で形成されます。'
                },
                'self_sacrifice': {
                    belief: '「他人の苦しみを放っておくのは罪悪感がある。自分のことを後回しにしてでも、相手を助け、面倒を見るのが自分の役割だ」という義務感です。',
                    behavior: '頼まれてもいないのに他人の世話を焼き、自分の心身を削ります。一見、服従スキーマに似ていますが、動機は「恐怖」ではなく「相手への共感や罪悪感」です。長期的には疲れ果て、助けている相手に対して恨みを感じることもあります。',
                    background: '親が病弱だったり、精神的に不安定だったりして、子供が親の悩みを聞いたり世話をしたりする「ヤングケアラー」のような役割を担わされていた場合によく見られます。'
                },
                'approval_seeking': {
                    belief: '「ありのままの自分には価値がない。他者から称賛され、認められて初めて、自分は存在していいことになる」という、外側からの評価への依存です。',
                    behavior: '「自分が何をしたいか」よりも「どう見られるか」を行動基準にします。地位、外見、財産などの追求に執着し、相手によって自分を演じ分けるカメレオンのような面があります。評価が得られないと、途端に激しい無価値感に襲われます。',
                    background: '「テストで100点を取った時だけ褒められる」といった条件付きの愛しか与えられず、成果を出さないと存在を無視されるような環境で育ったことが原因です。'
                },
                'negativity_pessimism': {
                    belief: '「人生のポジティブな面はまやかしで、ネガティブな側面（死、病、失敗）こそが真実だ。今は良くても、どうせ最後には悪いことが起きる」という悲観主義です。',
                    behavior: '常に最悪のシナリオを想定し、周囲の明るい話題にも「でも、こういうリスクがあるよ」と水を差してしまいます。失望して傷つくのを防ぐために、あらかじめ期待を捨て、心配や愚痴をこぼし続けることで心の準備をしようとします。',
                    background: '親が常に不安げで不平不満ばかり言っていた、あるいは幼少期に予期せぬ不幸が重なり、「人生は苦しいものだ」と学習せざるを得なかったことが関係しています。'
                },
                'emotional_inhibition': {
                    belief: '「感情（特に怒りや喜び、性的衝動）を素直に出すのは、恥ずべきことであり、自分を失う危険なことだ。常に理性的でなければならない」という自制心です。',
                    behavior: '感情の起伏が乏しく、周囲からは「何を考えているかわからない」「ロボットのよう」と思われることがあります。遊び心を出すことも苦手です。感情を抑え込み続けた結果、不眠や頭痛などの身体症状が出たり、ある日突然糸が切れたように爆発したりします。',
                    background: '感情表現を「子供っぽい」「はしたない」と厳しく制限されたり、感情を出すと親から拒絶されたりした厳格な家庭環境で形成されます。'
                },
                'unrelenting_standards': {
                    belief: '「自分も他人も、常に最高水準の基準を満たさなければならない。完璧でないことは失敗であり、批判に値する」という、休息を許さない強迫的な信念です。',
                    behavior: '完璧主義で、効率やルール、細部に過剰にこだわります。どれだけ成果を出しても「まだ足りない」と自分を追い込み、常に焦燥感に駆られています。リラックスして遊ぶことを「時間の無駄」と感じ、自分にも他人にも非常に厳しい態度をとります。',
                    background: '親の期待が非常に高く、完璧にできた時だけ評価された、あるいは親自身が自分を厳しく律して休まず働いている姿を見て育った場合に生じます。'
                },
                'punitiveness': {
                    belief: '「間違いを犯した人間は、情状酌量の余地なく厳しく罰せられるべきだ。自分も他人も、ミスをすれば報いを受けるのが当然だ」という不寛容な信念です。',
                    behavior: '他人のミスを許せず、攻撃的に非難します。自分自身の失敗に対しても、反省を超えて「自分を痛めつける」ような過酷な自己処罰を行います。事情や背景を考慮することが苦手で、物事を白黒はっきりした善悪で裁こうとします。',
                    background: '失敗した時に理由を聞いてもらえず、ただ厳しく叱責されたり罰を与えられたりした経験、あるいは道徳的・宗教的に非常に潔癖で厳しい規律の中で育ったことが影響しています。'
                }
            };
            return schemaDetails[key]?.[type] || '';
        }
    };
}
</script>
@endsection
