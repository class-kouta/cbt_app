@extends('layouts.app')

@section('title', '早期不適応スキーマ')
@section('page-title', '早期不適応スキーマ')

@section('content')
<div x-data="schemaApp()" x-init="init()" x-cloak>
    <!-- 保存トースト -->
    <div
        x-show="showSaveToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="fixed top-16 right-4 bg-orange-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span x-text="saveToastMessage"></span>
    </div>

    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <!-- メインコンテンツ -->
    <div x-show="!loading" class="space-y-8">
        <!-- 説明 -->
        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-3">早期不適応スキーマとは？</h2>
            <p class="text-sm text-gray-600 leading-relaxed">
                幼少期の体験によって形成された、自分自身や他者、世界に対する深い思い込みのパターンです。
                これらのスキーマは無意識のうちに私たちの感情や行動に影響を与え、生きづらさの原因となることがあります。
            </p>
            <p class="text-sm text-gray-600 leading-relaxed mt-2">
                各スキーマについて、自分がどれくらい「囚われている」と感じるかを0%〜100%で評価してみましょう。
                30秒ごとに自動保存されます。
            </p>
        </div>

        <!-- 第1領域：切断と拒絶 -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-red-500 text-white px-4 py-3">
                <h2 class="text-lg font-bold">第1領域：切断と拒絶</h2>
                <p class="text-sm opacity-90">安全、受容、養育といった基本的なニーズが満たされなかった領域</p>
            </div>
            <div class="p-4 space-y-6">
                <!-- 1. 見捨てられ/不安定スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-red-500">1.</span>
                                見捨てられ／不安定スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.abandonment" :class="getIntensityClass(schemas.abandonment) + ' ' + getIntensityBorderClass(schemas.abandonment)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「大切な人は自分を置いて去っていく。愛する人は死ぬか、他の人を選んで自分を捨てる」という予感に常に支配されています。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>パートナーの連絡が遅れるだけでパニックになり、相手を激しく責めたり逆に必死にしがみついたりします。一方で、別れの痛みを恐れるあまり、最初から深い関係を避け、孤独の中に引きこもることもあります。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>幼少期に親との死別や離婚を経験した、あるいは親の機嫌によって可愛がられたり無視されたりする「気まぐれな養育」を受けたことが影響しています。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.abandonment_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. 不信/虐待スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-red-500">2.</span>
                                不信／虐待スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.mistrust_abuse" :class="getIntensityClass(schemas.mistrust_abuse) + ' ' + getIntensityBorderClass(schemas.mistrust_abuse)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「人は私を傷つけ、利用し、騙す存在だ。油断すると攻撃される」という強い警戒心を持っており、世界を敵対的な場所だと感じています。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>他者の親切に「裏がある」と疑い、心を開けません。自分が被害者にならないよう先制攻撃で他者を威圧したり、逆に自分を粗末に扱う相手をあえて選んで「やはり人は信じられない」と確信を深めるループに陥りがちです。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>幼少期に家族から身体的・心理的な虐待を受けたり、信頼していた人にひどく裏切られたりした過酷な体験が根底にあります。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.mistrust_abuse_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 3. 情緒的剥奪スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-red-500">3.</span>
                                情緒的剥奪スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.emotional_deprivation" :class="getIntensityClass(schemas.emotional_deprivation) + ' ' + getIntensityBorderClass(schemas.emotional_deprivation)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「自分の話を聴き、理解し、守ってくれる人は誰もいない。自分は生涯、心理的に満たされることはない」という慢性的で静かな欠乏感です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>自分のニーズを伝えることを諦めているため、何も言わずに不機嫌になったり、原因不明の体調不良（心身症）として苦しさを訴えたりします。他者と関わっていても、心のどこかで「どうせ誰もわかってくれない」と冷めています。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>親が感情的に冷淡だった、または親自身に余裕がなく、子供の感情的な欲求（甘えや共感）に関心を示さない環境で育った場合に見られます。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.emotional_deprivation_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 4. 欠陥/恥スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-red-500">4.</span>
                                欠陥／恥スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.defectiveness_shame" :class="getIntensityClass(schemas.defectiveness_shame) + ' ' + getIntensityBorderClass(schemas.defectiveness_shame)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「自分は本質的にダメな人間で、愛される価値がない。本当の自分を知られたら、きっと誰もが呆れて離れていく」という自分自身への強い嫌悪感です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>批判や拒絶に極めて敏感で、少しの注意でも全人格を否定されたように落ち込みます。この「恥」を隠すために、完璧な自分を演じて武装したり、逆に自虐的な態度をとって他人に自分を攻撃させるような行動をとることがあります。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>親から否定的なレッテルを貼られたり、欠点ばかりを指摘されたりして、ありのままの自分を尊重された経験が乏しいことが原因です。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.defectiveness_shame_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 5. 社会的孤立/疎外スキーマ -->
                <div class="pb-2" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-red-500">5.</span>
                                社会的孤立／疎外スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.social_isolation" :class="getIntensityClass(schemas.social_isolation) + ' ' + getIntensityBorderClass(schemas.social_isolation)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「自分は他の人とは根本的に異なっていて、どこにも居場所がない。自分はどのグループにも馴染めない異邦人だ」という感覚です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>職場や友人グループの中にいても、常に一歩引いた「部外者」として振る舞います。能力があっても集団を避け、フリーランスや一匹狼的な生き方を選びがちです。交流の場では、自分だけが浮いているような強い違和感に苦しみます。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>幼少期に家族が周囲から浮いていた、自分の外見や家柄が周囲と違っていた、あるいは学校でいじめを受け「みんなとは違う」と強く意識せざるを得なかった経験が関係しています。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.social_isolation_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 第2領域：自律性と機能の障害 -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-orange-500 text-white px-4 py-3">
                <h2 class="text-lg font-bold">第2領域：自律性と機能の障害</h2>
                <p class="text-sm opacity-90">自分への自信や、自立して生きる能力が育たなかった領域</p>
            </div>
            <div class="p-4 space-y-6">
                <!-- 6. 依存/無能スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-orange-500">6.</span>
                                依存／無能スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.dependence_incompetence" :class="getIntensityClass(schemas.dependence_incompetence) + ' ' + getIntensityBorderClass(schemas.dependence_incompetence)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「自分一人では日々の生活や重大な決定をこなせない。誰か頼れる人がいないと、自分は生きていけない子供のような存在だ」という無力感です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>進路決定から今日の献立まで、誰かに決めてもらわないと不安で動けません。依存先がいなくなると激しいパニックに陥り、すぐに新しい「保護者役」を探そうとします。自分で新しいことに挑戦するのを極端に避けます。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>親が過保護で子供の代わりに何でもやってしまった、あるいは逆に親が子供の判断を常に否定し、自信を奪い続けた場合に形成されます。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.dependence_incompetence_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 7. 損害や疾病に対する脆弱性スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-orange-500">7.</span>
                                損害や疾病に対する脆弱性スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.vulnerability_to_harm" :class="getIntensityClass(schemas.vulnerability_to_harm) + ' ' + getIntensityBorderClass(schemas.vulnerability_to_harm)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「いつか必ず、恐ろしい災難（不治の病、強盗、飛行機事故、破産など）が自分を襲う。世界は危険に満ちていて、自分は防ぐことができない」という過剰な恐怖です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>些細な体の異変で重病を疑って病院を巡ったり、ニュースを見て過度に不安になり外出を控えたりします。常に最悪の事態を想定して予防策に奔走するため、リラックスして人生を楽しむことができません。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>親自身が極度の心配性で、「世の中は危ないところだ」「油断すると大変なことになる」という恐怖のメッセージを繰り返し伝えられたことが影響しています。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.vulnerability_to_harm_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 8. 巻き込まれ/未発達な自己スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-orange-500">8.</span>
                                巻き込まれ／未発達な自己スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.enmeshment" :class="getIntensityClass(schemas.enmeshment) + ' ' + getIntensityBorderClass(schemas.enmeshment)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「自分と親（またはパートナー）の間に境界線はない。相手の幸せが自分の幸せであり、相手を離れて自分一人の人生を歩むことは裏切りだ」という感覚です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>相手の機嫌によって自分の気分が180度変わります。自分の好みや意見がわからず、相手がいないと「空っぽ」になったように感じます。大人になっても親にすべてを報告しなければならない、といった心理的拘束を感じ続けます。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>親が子供を自分の延長線上として扱い、子供が自立しようとすると寂しがったり怒ったりして、心理的な分離を許さなかった環境が原因です。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.enmeshment_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 9. 失敗スキーマ -->
                <div class="pb-2" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-orange-500">9.</span>
                                失敗スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.failure" :class="getIntensityClass(schemas.failure) + ' ' + getIntensityBorderClass(schemas.failure)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「自分は同年代の人に比べて根本的に能力が低く、何をやっても最終的には失敗する。自分は落ちこぼれだ」という確信です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>実際に能力があっても「これは運が良かっただけだ」と成功を無視します。失敗するのが怖いために、最初から挑戦を諦めたり、わざと準備を怠って「やっぱりダメだった」という予測を的中させる（自己ハンディキャップ）行動をとります。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>学校や家庭で「お前はどんくさい」「何をやってもダメだ」と批判され続けたり、優秀な兄弟と比較されて挫折感を味わい続けたりした経験が根底にあります。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.failure_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 第3領域：制約の欠如 -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-yellow-500 text-white px-4 py-3">
                <h2 class="text-lg font-bold">第3領域：制約の欠如</h2>
                <p class="text-sm opacity-90">自制心、責任感、他者の権利の尊重といった限界設定が学べなかった領域</p>
            </div>
            <div class="p-4 space-y-6">
                <!-- 10. 権利要求/尊大さスキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-yellow-600">10.</span>
                                権利要求／尊大さスキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.entitlement_grandiosity" :class="getIntensityClass(schemas.entitlement_grandiosity) + ' ' + getIntensityBorderClass(schemas.entitlement_grandiosity)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「自分は特別な存在であり、一般のルールに従う必要はない。欲しいものは、他者の都合に関わらず手に入れる権利がある」という感覚です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>他者のニーズを軽視し、自分の欲望を最優先します。順番待ちを嫌う、借金を返さない、相手が思い通りにならないと激昂するといった行動が見られます。一見自信家ですが、実は欠乏感を隠すために虚勢を張っている場合も多いです。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>幼少期に甘やかされすぎて境界線を学ばなかった、あるいは逆にひどく冷遇された反動で「もう二度と我慢したくない」と強く思うようになったことが原因です。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.entitlement_grandiosity_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 11. 自制と自律の欠如スキーマ -->
                <div class="pb-2" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-yellow-600">11.</span>
                                自制と自律の欠如スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.insufficient_self_control" :class="getIntensityClass(schemas.insufficient_self_control) + ' ' + getIntensityBorderClass(schemas.insufficient_self_control)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「不快なことや退屈なこと、欲求不満を我慢することは耐えられない。自分をコントロールするのは無理だ」という諦めです。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>目標達成のための地道な努力ができず、すぐに投げ出します。感情を抑えられず衝動的に行動し、アルコールや買い物、過食などの依存に走りやすい傾向があります。約束を守れず、社会生活に支障をきたすことも少なくありません。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>幼少期に適切な「しつけ」を受けなかった、または苦痛な感情に対処する力を養う機会がなく、衝動のままに動くことが許容（あるいは放置）されていたことが影響しています。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.insufficient_self_control_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 第4領域：他者への志向 -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-green-500 text-white px-4 py-3">
                <h2 class="text-lg font-bold">第4領域：他者への志向</h2>
                <p class="text-sm opacity-90">愛や承認を得るために、自分のニーズを犠牲にして他者に合わせる領域</p>
            </div>
            <div class="p-4 space-y-6">
                <!-- 12. 服従スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-green-600">12.</span>
                                服従スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.subjugation" :class="getIntensityClass(schemas.subjugation) + ' ' + getIntensityBorderClass(schemas.subjugation)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「相手に従わないと怒られる、見捨てられる、あるいは報復される。自分の欲求を出すことは、トラブルを引き起こす危険なことだ」という恐怖に基づいた感覚です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>自分の本音を押し殺して相手の言いなりになります。表面上は従順ですが、内面には強い怒りが溜まっており、わざと遅刻したり仕事を遅らせたりする（受動的攻撃）や、突然の感情爆発として現れることがあります。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>非常に支配的な親や、怒ると手が付けられない親のもとで、自分の意思を持つことが生存への脅威だった環境で形成されます。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.subjugation_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 13. 自己犠牲スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-green-600">13.</span>
                                自己犠牲スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.self_sacrifice" :class="getIntensityClass(schemas.self_sacrifice) + ' ' + getIntensityBorderClass(schemas.self_sacrifice)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「他人の苦しみを放っておくのは罪悪感がある。自分のことを後回しにしてでも、相手を助け、面倒を見るのが自分の役割だ」という義務感です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>頼まれてもいないのに他人の世話を焼き、自分の心身を削ります。一見、服従スキーマに似ていますが、動機は「恐怖」ではなく「相手への共感や罪悪感」です。長期的には疲れ果て、助けている相手に対して恨みを感じることもあります。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>親が病弱だったり、精神的に不安定だったりして、子供が親の悩みを聞いたり世話をしたりする「ヤングケアラー」のような役割を担わされていた場合によく見られます。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.self_sacrifice_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 14. 承認欲求/評価の追求スキーマ -->
                <div class="pb-2" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-green-600">14.</span>
                                承認欲求／評価の追求スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.approval_seeking" :class="getIntensityClass(schemas.approval_seeking) + ' ' + getIntensityBorderClass(schemas.approval_seeking)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「ありのままの自分には価値がない。他者から称賛され、認められて初めて、自分は存在していいことになる」という、外側からの評価への依存です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>「自分が何をしたいか」よりも「どう見られるか」を行動基準にします。地位、外見、財産などの追求に執着し、相手によって自分を演じ分けるカメレオンのような面があります。評価が得られないと、途端に激しい無価値感に襲われます。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>「テストで100点を取った時だけ褒められる」といった条件付きの愛しか与えられず、成果を出さないと存在を無視されるような環境で育ったことが原因です。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.approval_seeking_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 第5領域：過剰警戒と抑制 -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-purple-500 text-white px-4 py-3">
                <h2 class="text-lg font-bold">第5領域：過剰警戒と抑制</h2>
                <p class="text-sm opacity-90">自発性や喜びを抑圧し、厳格なルールや警戒心に縛られる領域</p>
            </div>
            <div class="p-4 space-y-6">
                <!-- 15. 否定/悲観スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-purple-600">15.</span>
                                否定／悲観スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.negativity_pessimism" :class="getIntensityClass(schemas.negativity_pessimism) + ' ' + getIntensityBorderClass(schemas.negativity_pessimism)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「人生のポジティブな面はまやかしで、ネガティブな側面（死、病、失敗）こそが真実だ。今は良くても、どうせ最後には悪いことが起きる」という悲観主義です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>常に最悪のシナリオを想定し、周囲の明るい話題にも「でも、こういうリスクがあるよ」と水を差してしまいます。失望して傷つくのを防ぐために、あらかじめ期待を捨て、心配や愚痴をこぼし続けることで心の準備をしようとします。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>親が常に不安げで不平不満ばかり言っていた、あるいは幼少期に予期せぬ不幸が重なり、「人生は苦しいものだ」と学習せざるを得なかったことが関係しています。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.negativity_pessimism_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 16. 感情抑制スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-purple-600">16.</span>
                                感情抑制スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.emotional_inhibition" :class="getIntensityClass(schemas.emotional_inhibition) + ' ' + getIntensityBorderClass(schemas.emotional_inhibition)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「感情（特に怒りや喜び、性的衝動）を素直に出すのは、恥ずべきことであり、自分を失う危険なことだ。常に理性的でなければならない」という自制心です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>感情の起伏が乏しく、周囲からは「何を考えているかわからない」「ロボットのよう」と思われることがあります。遊び心を出すことも苦手です。感情を抑え込み続けた結果、不眠や頭痛などの身体症状が出たり、ある日突然糸が切れたように爆発したりします。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>感情表現を「子供っぽい」「はしたない」と厳しく制限されたり、感情を出すと親から拒絶されたりした厳格な家庭環境で形成されます。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.emotional_inhibition_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 17. 厳密な基準/過度の批判スキーマ -->
                <div class="border-b border-gray-200 pb-6" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-purple-600">17.</span>
                                厳密な基準／過度の批判スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.unrelenting_standards" :class="getIntensityClass(schemas.unrelenting_standards) + ' ' + getIntensityBorderClass(schemas.unrelenting_standards)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「自分も他人も、常に最高水準の基準を満たさなければならない。完璧でないことは失敗であり、批判に値する」という、休息を許さない強迫的な信念です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>完璧主義で、効率やルール、細部に過剰にこだわります。どれだけ成果を出しても「まだ足りない」と自分を追い込み、常に焦燥感に駆られています。リラックスして遊ぶことを「時間の無駄」と感じ、自分にも他人にも非常に厳しい態度をとります。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>親の期待が非常に高く、完璧にできた時だけ評価された、あるいは親自身が自分を厳しく律して休まず働いている姿を見て育った場合に生じます。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.unrelenting_standards_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 18. 罰への懲罰的志向スキーマ -->
                <div class="pb-2" x-data="{ open: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 cursor-pointer" @click="open = !open">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-purple-600">18.</span>
                                罰への懲罰的志向スキーマ
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                            <select x-model="schemas.punitiveness" :class="getIntensityClass(schemas.punitiveness) + ' ' + getIntensityBorderClass(schemas.punitiveness)" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                                <option value="">未選択</option>
                                <template x-for="i in 21" :key="i">
                                    <option :value="(i-1)*5" x-text="(i-1)*5 + '%'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 space-y-2">
                        <p><strong class="text-gray-700">深い思い込み：</strong>「間違いを犯した人間は、情状酌量の余地なく厳しく罰せられるべきだ。自分も他人も、ミスをすれば報いを受けるのが当然だ」という不寛容な信念です。</p>
                        <p><strong class="text-gray-700">典型的な行動・特徴：</strong>他人のミスを許せず、攻撃的に非難します。自分自身の失敗に対しても、反省を超えて「自分を痛めつける」ような過酷な自己処罰を行います。事情や背景を考慮することが苦手で、物事を白黒はっきりした善悪で裁こうとします。</p>
                        <p><strong class="text-gray-700">背景・ルーツ：</strong>失敗した時に理由を聞いてもらえず、ただ厳しく叱責されたり罰を与えられたりした経験、あるいは道徳的・宗教的に非常に潔癖で厳しい規律の中で育ったことが影響しています。</p>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-gray-700 font-medium mb-2">📝 このスキーマに関連する過去の経験や思い出</label>
                            <textarea
                                x-model="experiences.punitiveness_experience"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-y"
                                placeholder="このスキーマに関連する過去の経験、エピソード、思い当たることなどを自由に記入してください..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 備考欄 -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gray-600 text-white px-4 py-3">
                <h2 class="text-lg font-bold">📋 備考欄</h2>
                <p class="text-sm opacity-90">スキーマ全体に関するメモや、気づいたこと、その他なんでも自由に記入できます</p>
            </div>
            <div class="p-4">
                <textarea
                    x-model="notes"
                    rows="5"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-500 focus:border-transparent resize-y"
                    placeholder="スキーマ全体を通しての気づき、治療の目標、日々の振り返り、その他なんでも自由にメモしてください..."
                ></textarea>
            </div>
        </div>

        <!-- エラーメッセージ -->
        <div x-show="error" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3" x-text="error"></div>

        <!-- 手動保存ボタン -->
        <div class="mt-6">
            <button
                @click="saveManually()"
                class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-4 px-6 rounded-xl font-semibold hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="submitting"
            >
                <span x-show="!submitting" class="flex items-center justify-center gap-2">
                    保存する
                </span>
                <span x-show="submitting" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    保存中...
                </span>
            </button>
        </div>
    </div>
</div>

<script>
function schemaApp() {
    return {
        schemaId: null,
        schemas: {
            abandonment: '',
            mistrust_abuse: '',
            emotional_deprivation: '',
            defectiveness_shame: '',
            social_isolation: '',
            dependence_incompetence: '',
            vulnerability_to_harm: '',
            enmeshment: '',
            failure: '',
            entitlement_grandiosity: '',
            insufficient_self_control: '',
            subjugation: '',
            self_sacrifice: '',
            approval_seeking: '',
            negativity_pessimism: '',
            emotional_inhibition: '',
            unrelenting_standards: '',
            punitiveness: ''
        },
        experiences: {
            abandonment_experience: '',
            mistrust_abuse_experience: '',
            emotional_deprivation_experience: '',
            defectiveness_shame_experience: '',
            social_isolation_experience: '',
            dependence_incompetence_experience: '',
            vulnerability_to_harm_experience: '',
            enmeshment_experience: '',
            failure_experience: '',
            entitlement_grandiosity_experience: '',
            insufficient_self_control_experience: '',
            subjugation_experience: '',
            self_sacrifice_experience: '',
            approval_seeking_experience: '',
            negativity_pessimism_experience: '',
            emotional_inhibition_experience: '',
            unrelenting_standards_experience: '',
            punitiveness_experience: ''
        },
        notes: '',
        loading: true,
        submitting: false,
        error: '',
        showSaveToast: false,
        saveToastMessage: '',
        autoSaveInterval: null,
        autoSaveSnapshots: [],
        autoSaving: false,

        // %の値に応じた背景色クラスを返す
        getIntensityClass(value) {
            if (value === '' || value === null) return 'bg-white';
            const num = parseInt(value, 10);
            if (num <= 20) return 'bg-green-100 text-green-800';
            if (num <= 40) return 'bg-yellow-100 text-yellow-800';
            if (num <= 60) return 'bg-orange-100 text-orange-800';
            if (num <= 80) return 'bg-orange-200 text-orange-900 font-semibold';
            return 'bg-red-200 text-red-900 font-bold';
        },

        // %の値に応じたボーダー色クラスを返す
        getIntensityBorderClass(value) {
            if (value === '' || value === null) return 'border-gray-300';
            const num = parseInt(value, 10);
            if (num <= 20) return 'border-green-400';
            if (num <= 40) return 'border-yellow-400';
            if (num <= 60) return 'border-orange-400';
            if (num <= 80) return 'border-orange-500';
            return 'border-red-500 ring-2 ring-red-300';
        },

        async init() {
            await this.loadSchemas();
            this.takeSnapshot();
            
            // 30秒ごとに自動保存チェック
            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        async loadSchemas() {
            try {
                const res = await fetch('/api/early-maladaptive-schemas');
                if (res.ok) {
                    const data = await res.json();
                    if (data.id) {
                        this.schemaId = data.id;
                        // 各フィールドを設定（nullの場合は空文字）
                        Object.keys(this.schemas).forEach(key => {
                            this.schemas[key] = data[key] !== null ? String(data[key]) : '';
                        });
                        // 経験フィールドを設定
                        Object.keys(this.experiences).forEach(key => {
                            this.experiences[key] = data[key] !== null ? data[key] : '';
                        });
                        // 備考欄を設定
                        this.notes = data.notes !== null ? data.notes : '';
                    }
                }
            } catch (error) {
                console.error('データの読み込みに失敗しました:', error);
            } finally {
                this.loading = false;
            }
        },

        takeSnapshot() {
            const snapshot = { 
                schemas: { ...this.schemas },
                experiences: { ...this.experiences },
                notes: this.notes
            };
            this.autoSaveSnapshots.push(snapshot);
            if (this.autoSaveSnapshots.length > 2) {
                this.autoSaveSnapshots.shift();
            }
        },

        hasChangedFromPreviousSnapshot() {
            if (this.autoSaveSnapshots.length < 1) {
                return false;
            }
            const oldSnapshot = this.autoSaveSnapshots[0];
            // スキーマ値の変更チェック
            const schemasChanged = Object.keys(this.schemas).some(key => 
                this.schemas[key] !== oldSnapshot.schemas[key]
            );
            // 経験フィールドの変更チェック
            const experiencesChanged = Object.keys(this.experiences).some(key => 
                this.experiences[key] !== oldSnapshot.experiences[key]
            );
            // 備考欄の変更チェック
            const notesChanged = this.notes !== oldSnapshot.notes;
            return schemasChanged || experiencesChanged || notesChanged;
        },

        hasAnyValue() {
            const hasSchemaValue = Object.values(this.schemas).some(v => v !== '' && v !== null);
            const hasExperienceValue = Object.values(this.experiences).some(v => v !== '' && v !== null);
            const hasNotesValue = this.notes !== '' && this.notes !== null;
            return hasSchemaValue || hasExperienceValue || hasNotesValue;
        },

        async checkAndAutoSave() {
            if (
                this.hasAnyValue() &&
                this.hasChangedFromPreviousSnapshot() &&
                !this.submitting &&
                !this.autoSaving
            ) {
                await this.performAutoSave();
            }
            this.takeSnapshot();
        },

        async performAutoSave() {
            this.autoSaving = true;
            try {
                await this.saveData();
                this.showNotification('自動保存しました');
            } catch (error) {
                console.error('自動保存に失敗しました:', error);
            } finally {
                this.autoSaving = false;
            }
        },

        showNotification(message) {
            this.saveToastMessage = message;
            this.showSaveToast = true;
            setTimeout(() => {
                this.showSaveToast = false;
            }, 2000);
        },

        async saveManually() {
            this.submitting = true;
            this.error = '';
            try {
                await this.saveData();
                this.showNotification('保存しました');
            } catch (e) {
                this.error = e.message;
            } finally {
                this.submitting = false;
            }
        },

        async saveData() {
            const payload = {};
            // スキーマ値（整数）
            Object.keys(this.schemas).forEach(key => {
                payload[key] = this.schemas[key] !== '' ? parseInt(this.schemas[key], 10) : null;
            });
            // 経験フィールド（文字列）
            Object.keys(this.experiences).forEach(key => {
                payload[key] = this.experiences[key] !== '' ? this.experiences[key] : null;
            });
            // 備考欄（文字列）
            payload.notes = this.notes !== '' ? this.notes : null;

            let res;
            if (this.schemaId) {
                res = await fetch(`/api/early-maladaptive-schemas/${this.schemaId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            } else {
                res = await fetch('/api/early-maladaptive-schemas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            }

            if (!res.ok) {
                const data = await res.json();
                throw new Error(data.message || 'エラーが発生しました');
            }

            const data = await res.json();
            if (data.id && !this.schemaId) {
                this.schemaId = data.id;
            }
        }
    };
}
</script>
@endsection
