@extends('layouts.app')

@section('title', 'ストレッサーとストレス反応')
@section('page-title', 'ストレッサーとストレス反応')

@section('content')
<div x-data="stressorApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>
    <!-- 編集モード時のヘッダー -->
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a :href="'/stressor-and-responses/' + itemId" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
            ← 詳細に戻る
        </a>
    </div>

    <!-- ローディング（編集モードのみ） -->
    <div x-show="loading && isEditMode" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <!-- コピー成功トースト -->
    <div
        x-show="showCopyToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2"
    >
        <span>📋</span>
        <span>コピーしました！</span>
    </div>

    <!-- 自動保存トースト -->
    <div
        x-show="showAutoSaveToast"
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
        自動保存しました
    </div>

    <!-- 手動保存トースト -->
    <div
        x-show="showManualSaveToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="fixed top-16 right-4 bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        保存しました
    </div>

    <!-- フローティング保存ボタン -->
    <button
        type="button"
        @click="manualSave()"
        :disabled="floatingSaving || !isFormValid()"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center hover:from-emerald-600 hover:to-teal-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed z-30"
        title="保存する"
    >
        <template x-if="!floatingSaving">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8zM16 20v-6H8v6M8 4v4h6"></path>
            </svg>
        </template>
        <template x-if="floatingSaving">
            <svg class="animate-spin w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </template>
    </button>

    <!-- フォーム -->
    <div x-show="!loading || !isEditMode">
    <form @submit.prevent="saveItem()">
        <div class="space-y-5">
            <!-- ストレッサーセクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    ストレッサー
                    <span class="text-red-500 text-sm">*</span>
                </h3>
                <textarea
                    x-model="formData.stressor"
                    rows="6"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="例：仕事の締め切りが重なった"
                    maxlength="1000"
                    required
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="formData.stressor.length + '/1000'"></div>
            </div>

            <!-- タグセクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    🏷️ タグ
                    <span class="text-gray-400 font-normal text-sm">（任意・複数選択可）</span>
                </h3>
                <p class="text-xs text-gray-500 mb-3">
                    このストレッサーに関連するカテゴリーを選択してください
                </p>

                <!-- タグ選択UI -->
                <div class="flex flex-wrap gap-2">
                    <template x-for="tag in availableTags" :key="tag.id">
                        <button
                            type="button"
                            @click="toggleTag(tag.id)"
                            class="px-3 py-1.5 text-sm rounded-full border transition-all"
                            :class="isTagSelected(tag.id)
                                ? 'bg-emerald-500 text-white border-emerald-500'
                                : 'bg-white text-gray-700 border-gray-300 hover:border-emerald-400 hover:bg-emerald-50'"
                            x-text="tag.name"
                        ></button>
                    </template>
                </div>
            </div>

            <!-- ストレス反応セクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    ストレス反応
                    <span class="text-gray-400 font-normal text-sm">（任意）</span>
                </h3>
                
                <div class="space-y-4">
                    <!-- 認知（自動思考） -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            認知（自動思考）
                        </label>
                        <textarea
                            x-model="formData.cognition"
                            rows="8"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                            placeholder="例：もう間に合わない、失敗したらどうしよう"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="formData.cognition.length + '/1000'"></div>
                    </div>

                    <!-- 気分・感情 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            気分・感情
                            <!-- 感情リストトグルボタン -->
                            <button
                                type="button"
                                @click="showMoodEmotions = !showMoodEmotions"
                                class="ml-2 inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full transition-all"
                                :class="showMoodEmotions ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                感情リスト
                            </button>
                        </label>

                        <!-- 感情リスト -->
                        <div
                            x-show="showMoodEmotions"
                            x-collapse
                            class="mb-2 p-3 bg-blue-50 border border-blue-200 rounded-lg"
                        >
                            <p class="text-xs text-blue-600 mb-2">タップして感情を追加できます</p>

                            <!-- ネガティブエリア -->
                            <div class="mb-3">
                                <p class="text-xs font-semibold text-gray-500 mb-1.5">😔 ネガティブ</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="emotion in negativeEmotions" :key="emotion">
                                        <button
                                            type="button"
                                            @click="addEmotionToMood(emotion)"
                                            class="px-2.5 py-1 text-sm bg-white border border-gray-300 rounded-full hover:bg-gray-100 hover:border-gray-400 transition-all"
                                            x-text="emotion"
                                        ></button>
                                    </template>
                                </div>
                            </div>

                            <!-- ポジティブエリア -->
                            <div>
                                <p class="text-xs font-semibold text-gray-500 mb-1.5">😊 ポジティブ</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="emotion in positiveEmotions" :key="emotion">
                                        <button
                                            type="button"
                                            @click="addEmotionToMood(emotion)"
                                            class="px-2.5 py-1 text-sm bg-white border border-blue-300 rounded-full hover:bg-blue-100 hover:border-blue-400 transition-all"
                                            x-text="emotion"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <textarea
                            x-model="formData.mood"
                            rows="8"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="例：不安(80%)、焦り(70%)"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="formData.mood.length + '/1000'"></div>
                    </div>

                    <!-- 身体反応 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            身体反応
                        </label>
                        <textarea
                            x-model="formData.body_reaction"
                            rows="4"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                            placeholder="例：頭痛、肩こり、動悸、胃の痛み"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="formData.body_reaction.length + '/1000'"></div>
                    </div>

                    <!-- 行動 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            行動
                        </label>
                        <textarea
                            x-model="formData.behavior"
                            rows="4"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                            placeholder="例：仕事を先延ばしにした、イライラして周りに当たった"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="formData.behavior.length + '/1000'"></div>
                    </div>
                </div>
            </div>

            <!-- 刺激されたスキーマセクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    刺激されたスキーマ
                    <span class="text-gray-400 font-normal text-sm">（任意・複数選択可）</span>
                </h3>
                <p class="text-xs text-gray-500 mb-4">
                    このストレス場面で活性化された早期不適応スキーマを選択してください。タップで解説を確認できます。
                </p>

                <!-- 選択されたスキーマ表示 -->
                <div x-show="formData.stimulated_schemas.length > 0" class="mb-4">
                    <div class="flex flex-wrap gap-2">
                        <template x-for="schemaKey in formData.stimulated_schemas" :key="schemaKey">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
                                <span x-text="getSchemaName(schemaKey)"></span>
                                <button
                                    type="button"
                                    @click="toggleSchema(schemaKey)"
                                    class="ml-1 text-indigo-500 hover:text-indigo-700 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        </template>
                    </div>
                </div>

                <!-- スキーマ選択UI -->
                <div class="space-y-3">
                    <!-- 第1領域：切断と拒絶 -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-red-100" x-data="{ domainOpen: false }">
                        <button type="button" @click="domainOpen = !domainOpen" class="w-full bg-red-500 text-white px-4 py-2.5 flex items-center justify-between hover:bg-red-600 transition-colors">
                            <h4 class="text-sm font-bold">第1領域：切断と拒絶</h4>
                            <svg class="w-5 h-5 transition-transform" :class="domainOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="domainOpen" x-collapse class="p-3 space-y-3">
                            <!-- 1. 見捨てられ/不安定スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('abandonment')" @click.stop @change="toggleSchema('abandonment')" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500">
                                            <span class="font-medium text-gray-800 text-sm">見捨てられ／不安定</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「大切な人は自分を置いて去っていく。愛する人は死ぬか、他の人を選んで自分を捨てる」という予感に常に支配されています。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>パートナーの連絡が遅れるだけでパニックになり、相手を激しく責めたり逆に必死にしがみついたりします。一方で、別れの痛みを恐れるあまり、最初から深い関係を避け、孤独の中に引きこもることもあります。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>幼少期に親との死別や離婚を経験した、あるいは親の機嫌によって可愛がられたり無視されたりする「気まぐれな養育」を受けたことが影響しています。</p>
                                </div>
                            </div>

                            <!-- 2. 不信/虐待スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('mistrust_abuse')" @click.stop @change="toggleSchema('mistrust_abuse')" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500">
                                            <span class="font-medium text-gray-800 text-sm">不信／虐待</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「人は私を傷つけ、利用し、騙す存在だ。油断すると攻撃される」という強い警戒心を持っており、世界を敵対的な場所だと感じています。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>他者の親切に「裏がある」と疑い、心を開けません。自分が被害者にならないよう先制攻撃で他者を威圧したり、逆に自分を粗末に扱う相手をあえて選んで「やはり人は信じられない」と確信を深めるループに陥りがちです。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>幼少期に家族から身体的・心理的な虐待を受けたり、信頼していた人にひどく裏切られたりした過酷な体験が根底にあります。</p>
                                </div>
                            </div>

                            <!-- 3. 情緒的剥奪スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('emotional_deprivation')" @click.stop @change="toggleSchema('emotional_deprivation')" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500">
                                            <span class="font-medium text-gray-800 text-sm">情緒的剥奪</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「自分の話を聴き、理解し、守ってくれる人は誰もいない。自分は生涯、心理的に満たされることはない」という慢性的で静かな欠乏感です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>自分のニーズを伝えることを諦めているため、何も言わずに不機嫌になったり、原因不明の体調不良（心身症）として苦しさを訴えたりします。他者と関わっていても、心のどこかで「どうせ誰もわかってくれない」と冷めています。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>親が感情的に冷淡だった、または親自身に余裕がなく、子供の感情的な欲求（甘えや共感）に関心を示さない環境で育った場合に見られます。</p>
                                </div>
                            </div>

                            <!-- 4. 欠陥/恥スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('defectiveness_shame')" @click.stop @change="toggleSchema('defectiveness_shame')" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500">
                                            <span class="font-medium text-gray-800 text-sm">欠陥／恥</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「自分は本質的にダメな人間で、愛される価値がない。本当の自分を知られたら、きっと誰もが呆れて離れていく」という自分自身への強い嫌悪感です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>批判や拒絶に極めて敏感で、少しの注意でも全人格を否定されたように落ち込みます。この「恥」を隠すために、完璧な自分を演じて武装したり、逆に自虐的な態度をとって他人に自分を攻撃させるような行動をとることがあります。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>親から否定的なレッテルを貼られたり、欠点ばかりを指摘されたりして、ありのままの自分を尊重された経験が乏しいことが原因です。</p>
                                </div>
                            </div>

                            <!-- 5. 社会的孤立/疎外スキーマ -->
                            <div x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('social_isolation')" @click.stop @change="toggleSchema('social_isolation')" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500">
                                            <span class="font-medium text-gray-800 text-sm">社会的孤立／疎外</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「自分は他の人とは根本的に異なっていて、どこにも居場所がない。自分はどのグループにも馴染めない異邦人だ」という感覚です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>職場や友人グループの中にいても、常に一歩引いた「部外者」として振る舞います。能力があっても集団を避け、フリーランスや一匹狼的な生き方を選びがちです。交流の場では、自分だけが浮いているような強い違和感に苦しみます。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>幼少期に家族が周囲から浮いていた、自分の外見や家柄が周囲と違っていた、あるいは学校でいじめを受け「みんなとは違う」と強く意識せざるを得なかった経験が関係しています。</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 第2領域：自律性と機能の障害 -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-orange-100" x-data="{ domainOpen: false }">
                        <button type="button" @click="domainOpen = !domainOpen" class="w-full bg-orange-500 text-white px-4 py-2.5 flex items-center justify-between hover:bg-orange-600 transition-colors">
                            <h4 class="text-sm font-bold">第2領域：自律性と機能の障害</h4>
                            <svg class="w-5 h-5 transition-transform" :class="domainOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="domainOpen" x-collapse class="p-3 space-y-3">
                            <!-- 6. 依存/無能スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('dependence_incompetence')" @click.stop @change="toggleSchema('dependence_incompetence')" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                            <span class="font-medium text-gray-800 text-sm">依存／無能</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「自分一人では日々の生活や重大な決定をこなせない。誰か頼れる人がいないと、自分は生きていけない子供のような存在だ」という無力感です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>進路決定から今日の献立まで、誰かに決めてもらわないと不安で動けません。依存先がいなくなると激しいパニックに陥り、すぐに新しい「保護者役」を探そうとします。自分で新しいことに挑戦するのを極端に避けます。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>親が過保護で子供の代わりに何でもやってしまった、あるいは逆に親が子供の判断を常に否定し、自信を奪い続けた場合に形成されます。</p>
                                </div>
                            </div>

                            <!-- 7. 損害や疾病に対する脆弱性スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('vulnerability_to_harm')" @click.stop @change="toggleSchema('vulnerability_to_harm')" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                            <span class="font-medium text-gray-800 text-sm">損害や疾病に対する脆弱性</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「いつか必ず、恐ろしい災難（不治の病、強盗、飛行機事故、破産など）が自分を襲う。世界は危険に満ちていて、自分は防ぐことができない」という過剰な恐怖です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>些細な体の異変で重病を疑って病院を巡ったり、ニュースを見て過度に不安になり外出を控えたりします。常に最悪の事態を想定して予防策に奔走するため、リラックスして人生を楽しむことができません。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>親自身が極度の心配性で、「世の中は危ないところだ」「油断すると大変なことになる」という恐怖のメッセージを繰り返し伝えられたことが影響しています。</p>
                                </div>
                            </div>

                            <!-- 8. 巻き込まれ/未発達な自己スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('enmeshment')" @click.stop @change="toggleSchema('enmeshment')" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                            <span class="font-medium text-gray-800 text-sm">巻き込まれ／未発達な自己</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「自分と親（またはパートナー）の間に境界線はない。相手の幸せが自分の幸せであり、相手を離れて自分一人の人生を歩むことは裏切りだ」という感覚です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>相手の機嫌によって自分の気分が180度変わります。自分の好みや意見がわからず、相手がいないと「空っぽ」になったように感じます。大人になっても親にすべてを報告しなければならない、といった心理的拘束を感じ続けます。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>親が子供を自分の延長線上として扱い、子供が自立しようとすると寂しがったり怒ったりして、心理的な分離を許さなかった環境が原因です。</p>
                                </div>
                            </div>

                            <!-- 9. 失敗スキーマ -->
                            <div x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('failure')" @click.stop @change="toggleSchema('failure')" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                            <span class="font-medium text-gray-800 text-sm">失敗</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「自分は同年代の人に比べて根本的に能力が低く、何をやっても最終的には失敗する。自分は落ちこぼれだ」という確信です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>実際に能力があっても「これは運が良かっただけだ」と成功を無視します。失敗するのが怖いために、最初から挑戦を諦めたり、わざと準備を怠って「やっぱりダメだった」という予測を的中させる（自己ハンディキャップ）行動をとります。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>学校や家庭で「お前はどんくさい」「何をやってもダメだ」と批判され続けたり、優秀な兄弟と比較されて挫折感を味わい続けたりした経験が根底にあります。</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 第3領域：制約の欠如 -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-yellow-100" x-data="{ domainOpen: false }">
                        <button type="button" @click="domainOpen = !domainOpen" class="w-full bg-yellow-500 text-white px-4 py-2.5 flex items-center justify-between hover:bg-yellow-600 transition-colors">
                            <h4 class="text-sm font-bold">第3領域：制約の欠如</h4>
                            <svg class="w-5 h-5 transition-transform" :class="domainOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="domainOpen" x-collapse class="p-3 space-y-3">
                            <!-- 10. 権利要求/尊大さスキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('entitlement_grandiosity')" @click.stop @change="toggleSchema('entitlement_grandiosity')" class="w-4 h-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                                            <span class="font-medium text-gray-800 text-sm">権利要求／尊大さ</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「自分は特別な存在であり、一般のルールに従う必要はない。欲しいものは、他者の都合に関わらず手に入れる権利がある」という感覚です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>他者のニーズを軽視し、自分の欲望を最優先します。順番待ちを嫌う、借金を返さない、相手が思い通りにならないと激昂するといった行動が見られます。一見自信家ですが、実は欠乏感を隠すために虚勢を張っている場合も多いです。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>幼少期に甘やかされすぎて境界線を学ばなかった、あるいは逆にひどく冷遇された反動で「もう二度と我慢したくない」と強く思うようになったことが原因です。</p>
                                </div>
                            </div>

                            <!-- 11. 自制と自律の欠如スキーマ -->
                            <div x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('insufficient_self_control')" @click.stop @change="toggleSchema('insufficient_self_control')" class="w-4 h-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                                            <span class="font-medium text-gray-800 text-sm">自制と自律の欠如</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「不快なことや退屈なこと、欲求不満を我慢することは耐えられない。自分をコントロールするのは無理だ」という諦めです。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>目標達成のための地道な努力ができず、すぐに投げ出します。感情を抑えられず衝動的に行動し、アルコールや買い物、過食などの依存に走りやすい傾向があります。約束を守れず、社会生活に支障をきたすことも少なくありません。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>幼少期に適切な「しつけ」を受けなかった、または苦痛な感情に対処する力を養う機会がなく、衝動のままに動くことが許容（あるいは放置）されていたことが影響しています。</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 第4領域：他者への志向 -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-green-100" x-data="{ domainOpen: false }">
                        <button type="button" @click="domainOpen = !domainOpen" class="w-full bg-green-500 text-white px-4 py-2.5 flex items-center justify-between hover:bg-green-600 transition-colors">
                            <h4 class="text-sm font-bold">第4領域：他者への志向</h4>
                            <svg class="w-5 h-5 transition-transform" :class="domainOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="domainOpen" x-collapse class="p-3 space-y-3">
                            <!-- 12. 服従スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('subjugation')" @click.stop @change="toggleSchema('subjugation')" class="w-4 h-4 rounded border-gray-300 text-green-500 focus:ring-green-500">
                                            <span class="font-medium text-gray-800 text-sm">服従</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「相手に従わないと怒られる、見捨てられる、あるいは報復される。自分の欲求を出すことは、トラブルを引き起こす危険なことだ」という恐怖に基づいた感覚です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>自分の本音を押し殺して相手の言いなりになります。表面上は従順ですが、内面には強い怒りが溜まっており、わざと遅刻したり仕事を遅らせたりする（受動的攻撃）や、突然の感情爆発として現れることがあります。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>非常に支配的な親や、怒ると手が付けられない親のもとで、自分の意思を持つことが生存への脅威だった環境で形成されます。</p>
                                </div>
                            </div>

                            <!-- 13. 自己犠牲スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('self_sacrifice')" @click.stop @change="toggleSchema('self_sacrifice')" class="w-4 h-4 rounded border-gray-300 text-green-500 focus:ring-green-500">
                                            <span class="font-medium text-gray-800 text-sm">自己犠牲</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「他人の苦しみを放っておくのは罪悪感がある。自分のことを後回しにしてでも、相手を助け、面倒を見るのが自分の役割だ」という義務感です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>頼まれてもいないのに他人の世話を焼き、自分の心身を削ります。一見、服従スキーマに似ていますが、動機は「恐怖」ではなく「相手への共感や罪悪感」です。長期的には疲れ果て、助けている相手に対して恨みを感じることもあります。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>親が病弱だったり、精神的に不安定だったりして、子供が親の悩みを聞いたり世話をしたりする「ヤングケアラー」のような役割を担わされていた場合によく見られます。</p>
                                </div>
                            </div>

                            <!-- 14. 承認欲求/評価の追求スキーマ -->
                            <div x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('approval_seeking')" @click.stop @change="toggleSchema('approval_seeking')" class="w-4 h-4 rounded border-gray-300 text-green-500 focus:ring-green-500">
                                            <span class="font-medium text-gray-800 text-sm">承認欲求／評価の追求</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「ありのままの自分には価値がない。他者から称賛され、認められて初めて、自分は存在していいことになる」という、外側からの評価への依存です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>「自分が何をしたいか」よりも「どう見られるか」を行動基準にします。地位、外見、財産などの追求に執着し、相手によって自分を演じ分けるカメレオンのような面があります。評価が得られないと、途端に激しい無価値感に襲われます。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>「テストで100点を取った時だけ褒められる」といった条件付きの愛しか与えられず、成果を出さないと存在を無視されるような環境で育ったことが原因です。</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 第5領域：過剰警戒と抑制 -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-purple-100" x-data="{ domainOpen: false }">
                        <button type="button" @click="domainOpen = !domainOpen" class="w-full bg-purple-500 text-white px-4 py-2.5 flex items-center justify-between hover:bg-purple-600 transition-colors">
                            <h4 class="text-sm font-bold">第5領域：過剰警戒と抑制</h4>
                            <svg class="w-5 h-5 transition-transform" :class="domainOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="domainOpen" x-collapse class="p-3 space-y-3">
                            <!-- 15. 否定/悲観スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('negativity_pessimism')" @click.stop @change="toggleSchema('negativity_pessimism')" class="w-4 h-4 rounded border-gray-300 text-purple-500 focus:ring-purple-500">
                                            <span class="font-medium text-gray-800 text-sm">否定／悲観</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「人生のポジティブな面はまやかしで、ネガティブな側面（死、病、失敗）こそが真実だ。今は良くても、どうせ最後には悪いことが起きる」という悲観主義です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>常に最悪のシナリオを想定し、周囲の明るい話題にも「でも、こういうリスクがあるよ」と水を差してしまいます。失望して傷つくのを防ぐために、あらかじめ期待を捨て、心配や愚痴をこぼし続けることで心の準備をしようとします。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>親が常に不安げで不平不満ばかり言っていた、あるいは幼少期に予期せぬ不幸が重なり、「人生は苦しいものだ」と学習せざるを得なかったことが関係しています。</p>
                                </div>
                            </div>

                            <!-- 16. 感情抑制スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('emotional_inhibition')" @click.stop @change="toggleSchema('emotional_inhibition')" class="w-4 h-4 rounded border-gray-300 text-purple-500 focus:ring-purple-500">
                                            <span class="font-medium text-gray-800 text-sm">感情抑制</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「感情（特に怒りや喜び、性的衝動）を素直に出すのは、恥ずべきことであり、自分を失う危険なことだ。常に理性的でなければならない」という自制心です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>感情の起伏が乏しく、周囲からは「何を考えているかわからない」「ロボットのよう」と思われることがあります。遊び心を出すことも苦手です。感情を抑え込み続けた結果、不眠や頭痛などの身体症状が出たり、ある日突然糸が切れたように爆発したりします。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>感情表現を「子供っぽい」「はしたない」と厳しく制限されたり、感情を出すと親から拒絶されたりした厳格な家庭環境で形成されます。</p>
                                </div>
                            </div>

                            <!-- 17. 厳密な基準/過度の批判スキーマ -->
                            <div class="border-b border-gray-100 pb-3" x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('unrelenting_standards')" @click.stop @change="toggleSchema('unrelenting_standards')" class="w-4 h-4 rounded border-gray-300 text-purple-500 focus:ring-purple-500">
                                            <span class="font-medium text-gray-800 text-sm">厳密な基準／過度の批判</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「自分も他人も、常に最高水準の基準を満たさなければならない。完璧でないことは失敗であり、批判に値する」という、休息を許さない強迫的な信念です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>完璧主義で、効率やルール、細部に過剰にこだわります。どれだけ成果を出しても「まだ足りない」と自分を追い込み、常に焦燥感に駆られています。リラックスして遊ぶことを「時間の無駄」と感じ、自分にも他人にも非常に厳しい態度をとります。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>親の期待が非常に高く、完璧にできた時だけ評価された、あるいは親自身が自分を厳しく律して休まず働いている姿を見て育った場合に生じます。</p>
                                </div>
                            </div>

                            <!-- 18. 罰への懲罰的志向スキーマ -->
                            <div x-data="{ open: false }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 cursor-pointer" @click="open = !open">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :checked="formData.stimulated_schemas.includes('punitiveness')" @click.stop @change="toggleSchema('punitiveness')" class="w-4 h-4 rounded border-gray-300 text-purple-500 focus:ring-purple-500">
                                            <span class="font-medium text-gray-800 text-sm">罰への懲罰的志向</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 space-y-2 pl-6">
                                    <p><strong class="text-gray-700">深い思い込み：</strong>「間違いを犯した人間は、情状酌量の余地なく厳しく罰せられるべきだ。自分も他人も、ミスをすれば報いを受けるのが当然だ」という不寛容な信念です。</p>
                                    <p><strong class="text-gray-700">典型的な行動・特徴：</strong>他人のミスを許せず、攻撃的に非難します。自分自身の失敗に対しても、反省を超えて「自分を痛めつける」ような過酷な自己処罰を行います。事情や背景を考慮することが苦手で、物事を白黒はっきりした善悪で裁こうとします。</p>
                                    <p><strong class="text-gray-700">背景・ルーツ：</strong>失敗した時に理由を聞いてもらえず、ただ厳しく叱責されたり罰を与えられたりした経験、あるいは道徳的・宗教的に非常に潔癖で厳しい規律の中で育ったことが影響しています。</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- エラーメッセージ -->
            <div x-show="error" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3" x-text="error"></div>

            <!-- ボタンエリア -->
            <div class="space-y-3">
                <!-- 送信ボタン -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-4 px-6 rounded-xl font-semibold hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="submitting || !isFormValid()"
                >
                    <span x-show="!submitting && !isEditMode" class="flex items-center justify-center gap-2">
                        ✨ 保存する
                    </span>
                    <span x-show="!submitting && isEditMode" class="flex items-center justify-center gap-2">
                        ✨ 更新する
                    </span>
                    <span x-show="submitting" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isEditMode ? '更新中...' : '保存中...'"></span>
                    </span>
                </button>

                <!-- コピーボタン -->
                <button
                    type="button"
                    @click="copyToClipboard()"
                    class="w-full bg-white border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all flex items-center justify-center gap-2"
                    :disabled="!hasAnyContent()"
                    :class="{ 'opacity-50 cursor-not-allowed': !hasAnyContent() }"
                >
                    📋 入力内容をコピー
                </button>
            </div>
        </div>
    </form>
    </div>
</div>

<script>
function stressorApp(itemId) {
    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        formData: {
            stressor: '',
            cognition: '',
            mood: '',
            body_reaction: '',
            behavior: '',
            stimulated_schemas: [],
            tag_ids: []
        },
        loading: false,
        submitting: false,
        error: '',
        showCopyToast: false,
        showAutoSaveToast: false,
        showManualSaveToast: false,
        floatingSaving: false,

        // タグ一覧
        availableTags: [],

        // 自動保存用
        autoSaveSnapshots: [],
        autoSaveInterval: null,
        autoSaving: false,

        // 感情リストの表示状態
        showMoodEmotions: false,

        // ネガティブ感情リスト
        negativeEmotions: [
            '怒り', 'イライラ', '腹立たしい', 'ムカつく', '憤り',
            '悲しい', '寂しい', '切ない', '虚しい', '孤独',
            '不安', '心配', '恐怖', '怖い', 'パニック', '焦り', '緊張',
            '落ち込み', '憂うつ', '絶望', '無力感', '疲労感',
            '恥ずかしい', '罪悪感', '後悔', '自己嫌悪', '情けない',
            '嫉妬', '羨ましい', '妬ましい', '劣等感',
            '困惑', '戸惑い', 'もどかしい', '退屈', '不満', '失望',
            '複雑', 'モヤモヤ'
        ],
        // ポジティブ感情リスト
        positiveEmotions: [
            '嬉しい', '楽しい', '幸せ', 'ワクワク', '期待',
            '安心', 'ホッとした', '満足', '達成感', '充実感',
            '感謝', '愛情', '親しみ', '誇らしい', '自信',
            '驚き', 'スッキリ'
        ],

        async init() {
            // タグ一覧を取得
            await this.loadTags();

            if (this.isEditMode) {
                await this.loadItem();
            }

            // 初期スナップショットを取得
            this.takeSnapshot();

            // 30秒ごとに自動保存チェック
            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        async loadTags() {
            try {
                const res = await apiFetch('/api/tags');
                if (res.ok) {
                    this.availableTags = await res.json();
                }
            } catch (error) {
                console.error('タグの取得に失敗しました:', error);
            }
        },

        toggleTag(tagId) {
            const index = this.formData.tag_ids.indexOf(tagId);
            if (index > -1) {
                this.formData.tag_ids.splice(index, 1);
            } else {
                this.formData.tag_ids.push(tagId);
            }
        },

        isTagSelected(tagId) {
            return this.formData.tag_ids.includes(tagId);
        },

        getTagName(tagId) {
            const tag = this.availableTags.find(t => t.id === tagId);
            return tag ? tag.name : '';
        },

        takeSnapshot() {
            const snapshot = {
                stressor: this.formData.stressor,
                cognition: this.formData.cognition,
                mood: this.formData.mood,
                body_reaction: this.formData.body_reaction,
                behavior: this.formData.behavior,
                stimulated_schemas: JSON.stringify(this.formData.stimulated_schemas),
                tag_ids: JSON.stringify(this.formData.tag_ids)
            };
            this.autoSaveSnapshots.push(snapshot);

            if (this.autoSaveSnapshots.length > 2) {
                this.autoSaveSnapshots.shift();
            }
        },

        hasChangedFromPreviousSnapshot() {
            if (this.autoSaveSnapshots.length < 2) {
                if (this.autoSaveSnapshots.length === 1) {
                    return this.hasValueChanged(this.autoSaveSnapshots[0]);
                }
                return false;
            }
            const oldSnapshot = this.autoSaveSnapshots[0];
            return this.hasValueChanged(oldSnapshot);
        },

        hasValueChanged(snapshot) {
            return (
                this.formData.stressor !== snapshot.stressor ||
                this.formData.cognition !== snapshot.cognition ||
                this.formData.mood !== snapshot.mood ||
                this.formData.body_reaction !== snapshot.body_reaction ||
                this.formData.behavior !== snapshot.behavior ||
                JSON.stringify(this.formData.stimulated_schemas) !== snapshot.stimulated_schemas ||
                JSON.stringify(this.formData.tag_ids) !== snapshot.tag_ids
            );
        },

        async checkAndAutoSave() {
            if (
                this.formData.stressor.trim() &&
                this.hasChangedFromPreviousSnapshot() &&
                !this.submitting &&
                !this.autoSaving
            ) {
                await this.performAutoSave();
            }
            this.takeSnapshot();
        },

        // 共通の保存処理
        async performSave(isManual = false) {
            try {
                if (this.itemId) {
                    // 既存データの更新
                    const res = await apiFetch(`/api/stressor-and-responses/${this.itemId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        this.showSaveNotification(isManual);
                    }
                } else {
                    // 新規作成
                    const res = await apiFetch('/api/stressor-and-responses', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        const data = await res.json();
                        this.itemId = data.id;
                        this.isEditMode = true;
                        history.replaceState(null, '', `/stressor-and-responses/${this.itemId}/edit`);
                        this.showSaveNotification(isManual);
                    }
                }
            } catch (error) {
                console.error(isManual ? '保存に失敗しました:' : '自動保存に失敗しました:', error);
            }
        },

        async performAutoSave() {
            this.autoSaving = true;
            try {
                await this.performSave(false);
            } finally {
                this.autoSaving = false;
            }
        },

        // 手動保存（フローティングボタン用）
        async manualSave() {
            if (this.floatingSaving || !this.isFormValid()) return;

            this.floatingSaving = true;
            try {
                await this.performSave(true);
            } finally {
                this.floatingSaving = false;
            }
        },

        showSaveNotification(isManual = false) {
            if (isManual) {
                this.showManualSaveToast = true;
                setTimeout(() => {
                    this.showManualSaveToast = false;
                }, 2000);
            } else {
                this.showAutoSaveToast = true;
                setTimeout(() => {
                    this.showAutoSaveToast = false;
                }, 2000);
            }
        },

        async loadItem() {
            this.loading = true;
            try {
                const res = await apiFetch(`/api/stressor-and-responses/${this.itemId}`);
                if (res.ok) {
                    const item = await res.json();
                    this.formData.stressor = item.stressor || '';
                    this.formData.cognition = item.cognition || '';
                    this.formData.mood = item.mood || '';
                    this.formData.body_reaction = item.body_reaction || '';
                    this.formData.behavior = item.behavior || '';
                    this.formData.stimulated_schemas = item.stimulated_schemas || [];
                    this.formData.tag_ids = item.tag_ids || [];
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        addEmotionToMood(emotion) {
            if (this.formData.mood.trim().length > 0) {
                this.formData.mood += '\n' + emotion;
            } else {
                this.formData.mood = emotion;
            }
        },

        toggleSchema(schemaKey) {
            const index = this.formData.stimulated_schemas.indexOf(schemaKey);
            if (index > -1) {
                this.formData.stimulated_schemas.splice(index, 1);
            } else {
                this.formData.stimulated_schemas.push(schemaKey);
            }
        },

        isFormValid() {
            return this.formData.stressor.trim();
        },

        hasAnyContent() {
            return this.formData.stressor.trim() ||
                   this.formData.cognition.trim() ||
                   this.formData.mood.trim() ||
                   this.formData.body_reaction.trim() ||
                   this.formData.behavior.trim() ||
                   this.formData.stimulated_schemas.length > 0;
        },

        generateCopyText() {
            const sections = [];
            sections.push('【ストレッサーとストレス反応】');
            sections.push('');

            sections.push('■ ストレッサー');
            sections.push(this.formData.stressor.trim() || '未入力');
            sections.push('');

            sections.push('■ 認知（自動思考）');
            sections.push(this.formData.cognition.trim() || '未入力');
            sections.push('');

            sections.push('■ 気分・感情');
            sections.push(this.formData.mood.trim() || '未入力');
            sections.push('');

            sections.push('■ 身体反応');
            sections.push(this.formData.body_reaction.trim() || '未入力');
            sections.push('');

            sections.push('■ 行動');
            sections.push(this.formData.behavior.trim() || '未入力');
            sections.push('');

            sections.push('■ 刺激されたスキーマ');
            if (this.formData.stimulated_schemas.length > 0) {
                const schemaNames = this.formData.stimulated_schemas.map(key => this.getSchemaName(key));
                sections.push(schemaNames.join('、'));
            } else {
                sections.push('未選択');
            }

            return sections.join('\n').trim();
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

        async copyToClipboard() {
            const text = this.generateCopyText();

            try {
                await navigator.clipboard.writeText(text);
                this.showCopyToast = true;
                setTimeout(() => {
                    this.showCopyToast = false;
                }, 2000);
            } catch (err) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    this.showCopyToast = true;
                    setTimeout(() => {
                        this.showCopyToast = false;
                    }, 2000);
                } catch (err) {
                    console.error('コピーに失敗しました:', err);
                }
                document.body.removeChild(textArea);
            }
        },

        async saveItem() {
            if (this.isEditMode) {
                await this.updateItem();
            } else {
                await this.createItem();
            }
        },

        async createItem() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = 'ストレッサーを入力してください';
                return;
            }

            this.submitting = true;
            try {
                const res = await apiFetch('/api/stressor-and-responses', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                window.location.href = '/stressor-and-responses/list';
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
            }
        },

        async updateItem() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = 'ストレッサーを入力してください';
                return;
            }

            this.submitting = true;
            try {
                const res = await apiFetch(`/api/stressor-and-responses/${this.itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                window.location.href = `/stressor-and-responses/${this.itemId}`;
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection
