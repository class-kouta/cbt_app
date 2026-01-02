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
                            rows="4"
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
                            rows="4"
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
                <div class="space-y-4">
                    <!-- 第1領域：切断と拒絶 -->
                    <div class="border border-red-200 rounded-lg overflow-hidden">
                        <button
                            type="button"
                            @click="schemaDomains.domain1 = !schemaDomains.domain1"
                            class="w-full flex items-center justify-between px-4 py-3 bg-red-50 hover:bg-red-100 transition-colors text-left"
                        >
                            <span class="font-semibold text-red-700 text-sm">第1領域：切断と拒絶</span>
                            <svg class="w-5 h-5 text-red-500 transition-transform" :class="schemaDomains.domain1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="schemaDomains.domain1" x-collapse class="p-3 space-y-2 bg-white">
                            <template x-for="schema in schemasByDomain.domain1" :key="schema.key">
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="flex items-center gap-3 p-3 hover:bg-gray-50">
                                        <label class="flex items-center gap-3 flex-1 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                :checked="formData.stimulated_schemas.includes(schema.key)"
                                                @change="toggleSchema(schema.key)"
                                                class="w-5 h-5 rounded border-gray-300 text-red-500 focus:ring-red-500"
                                            >
                                            <span class="font-medium text-gray-700 text-sm" x-text="schema.name"></span>
                                        </label>
                                        <button
                                            type="button"
                                            @click="toggleSchemaDetail(schema.key)"
                                            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors"
                                            title="解説を見る"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="openSchemaDetail === schema.key" x-collapse class="px-4 py-3 bg-red-50 text-sm text-gray-600 space-y-2 border-t border-red-100">
                                        <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="schema.belief"></span></p>
                                        <p><strong class="text-gray-700">典型的な行動：</strong><span x-text="schema.behavior"></span></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- 第2領域：自律性と機能の障害 -->
                    <div class="border border-orange-200 rounded-lg overflow-hidden">
                        <button
                            type="button"
                            @click="schemaDomains.domain2 = !schemaDomains.domain2"
                            class="w-full flex items-center justify-between px-4 py-3 bg-orange-50 hover:bg-orange-100 transition-colors text-left"
                        >
                            <span class="font-semibold text-orange-700 text-sm">第2領域：自律性と機能の障害</span>
                            <svg class="w-5 h-5 text-orange-500 transition-transform" :class="schemaDomains.domain2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="schemaDomains.domain2" x-collapse class="p-3 space-y-2 bg-white">
                            <template x-for="schema in schemasByDomain.domain2" :key="schema.key">
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="flex items-center gap-3 p-3 hover:bg-gray-50">
                                        <label class="flex items-center gap-3 flex-1 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                :checked="formData.stimulated_schemas.includes(schema.key)"
                                                @change="toggleSchema(schema.key)"
                                                class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                                            >
                                            <span class="font-medium text-gray-700 text-sm" x-text="schema.name"></span>
                                        </label>
                                        <button
                                            type="button"
                                            @click="toggleSchemaDetail(schema.key)"
                                            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors"
                                            title="解説を見る"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="openSchemaDetail === schema.key" x-collapse class="px-4 py-3 bg-orange-50 text-sm text-gray-600 space-y-2 border-t border-orange-100">
                                        <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="schema.belief"></span></p>
                                        <p><strong class="text-gray-700">典型的な行動：</strong><span x-text="schema.behavior"></span></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- 第3領域：制約の欠如 -->
                    <div class="border border-yellow-200 rounded-lg overflow-hidden">
                        <button
                            type="button"
                            @click="schemaDomains.domain3 = !schemaDomains.domain3"
                            class="w-full flex items-center justify-between px-4 py-3 bg-yellow-50 hover:bg-yellow-100 transition-colors text-left"
                        >
                            <span class="font-semibold text-yellow-700 text-sm">第3領域：制約の欠如</span>
                            <svg class="w-5 h-5 text-yellow-500 transition-transform" :class="schemaDomains.domain3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="schemaDomains.domain3" x-collapse class="p-3 space-y-2 bg-white">
                            <template x-for="schema in schemasByDomain.domain3" :key="schema.key">
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="flex items-center gap-3 p-3 hover:bg-gray-50">
                                        <label class="flex items-center gap-3 flex-1 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                :checked="formData.stimulated_schemas.includes(schema.key)"
                                                @change="toggleSchema(schema.key)"
                                                class="w-5 h-5 rounded border-gray-300 text-yellow-500 focus:ring-yellow-500"
                                            >
                                            <span class="font-medium text-gray-700 text-sm" x-text="schema.name"></span>
                                        </label>
                                        <button
                                            type="button"
                                            @click="toggleSchemaDetail(schema.key)"
                                            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors"
                                            title="解説を見る"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="openSchemaDetail === schema.key" x-collapse class="px-4 py-3 bg-yellow-50 text-sm text-gray-600 space-y-2 border-t border-yellow-100">
                                        <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="schema.belief"></span></p>
                                        <p><strong class="text-gray-700">典型的な行動：</strong><span x-text="schema.behavior"></span></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- 第4領域：他者への志向 -->
                    <div class="border border-green-200 rounded-lg overflow-hidden">
                        <button
                            type="button"
                            @click="schemaDomains.domain4 = !schemaDomains.domain4"
                            class="w-full flex items-center justify-between px-4 py-3 bg-green-50 hover:bg-green-100 transition-colors text-left"
                        >
                            <span class="font-semibold text-green-700 text-sm">第4領域：他者への志向</span>
                            <svg class="w-5 h-5 text-green-500 transition-transform" :class="schemaDomains.domain4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="schemaDomains.domain4" x-collapse class="p-3 space-y-2 bg-white">
                            <template x-for="schema in schemasByDomain.domain4" :key="schema.key">
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="flex items-center gap-3 p-3 hover:bg-gray-50">
                                        <label class="flex items-center gap-3 flex-1 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                :checked="formData.stimulated_schemas.includes(schema.key)"
                                                @change="toggleSchema(schema.key)"
                                                class="w-5 h-5 rounded border-gray-300 text-green-500 focus:ring-green-500"
                                            >
                                            <span class="font-medium text-gray-700 text-sm" x-text="schema.name"></span>
                                        </label>
                                        <button
                                            type="button"
                                            @click="toggleSchemaDetail(schema.key)"
                                            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors"
                                            title="解説を見る"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="openSchemaDetail === schema.key" x-collapse class="px-4 py-3 bg-green-50 text-sm text-gray-600 space-y-2 border-t border-green-100">
                                        <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="schema.belief"></span></p>
                                        <p><strong class="text-gray-700">典型的な行動：</strong><span x-text="schema.behavior"></span></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- 第5領域：過剰警戒と抑制 -->
                    <div class="border border-purple-200 rounded-lg overflow-hidden">
                        <button
                            type="button"
                            @click="schemaDomains.domain5 = !schemaDomains.domain5"
                            class="w-full flex items-center justify-between px-4 py-3 bg-purple-50 hover:bg-purple-100 transition-colors text-left"
                        >
                            <span class="font-semibold text-purple-700 text-sm">第5領域：過剰警戒と抑制</span>
                            <svg class="w-5 h-5 text-purple-500 transition-transform" :class="schemaDomains.domain5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="schemaDomains.domain5" x-collapse class="p-3 space-y-2 bg-white">
                            <template x-for="schema in schemasByDomain.domain5" :key="schema.key">
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="flex items-center gap-3 p-3 hover:bg-gray-50">
                                        <label class="flex items-center gap-3 flex-1 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                :checked="formData.stimulated_schemas.includes(schema.key)"
                                                @change="toggleSchema(schema.key)"
                                                class="w-5 h-5 rounded border-gray-300 text-purple-500 focus:ring-purple-500"
                                            >
                                            <span class="font-medium text-gray-700 text-sm" x-text="schema.name"></span>
                                        </label>
                                        <button
                                            type="button"
                                            @click="toggleSchemaDetail(schema.key)"
                                            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors"
                                            title="解説を見る"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="openSchemaDetail === schema.key" x-collapse class="px-4 py-3 bg-purple-50 text-sm text-gray-600 space-y-2 border-t border-purple-100">
                                        <p><strong class="text-gray-700">深い思い込み：</strong><span x-text="schema.belief"></span></p>
                                        <p><strong class="text-gray-700">典型的な行動：</strong><span x-text="schema.behavior"></span></p>
                                    </div>
                                </div>
                            </template>
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
            stimulated_schemas: []
        },
        loading: false,
        submitting: false,
        error: '',
        showCopyToast: false,
        showAutoSaveToast: false,

        // 自動保存用
        autoSaveSnapshots: [],
        autoSaveInterval: null,
        autoSaving: false,

        // 感情リストの表示状態
        showMoodEmotions: false,

        // スキーマ領域の開閉状態
        schemaDomains: {
            domain1: false,
            domain2: false,
            domain3: false,
            domain4: false,
            domain5: false
        },

        // スキーマ詳細の開閉状態
        openSchemaDetail: null,

        // スキーマデータ（領域別）
        schemasByDomain: {
            domain1: [
                { key: 'abandonment', name: '見捨てられ／不安定', belief: '大切な人は自分を置いて去っていく。愛する人は死ぬか、他の人を選んで自分を捨てる。', behavior: 'パートナーの連絡が遅れるだけでパニックになり、相手を激しく責めたり必死にしがみついたりする。' },
                { key: 'mistrust_abuse', name: '不信／虐待', belief: '人は私を傷つけ、利用し、騙す存在だ。油断すると攻撃される。', behavior: '他者の親切に「裏がある」と疑い、心を開けない。先制攻撃で他者を威圧することも。' },
                { key: 'emotional_deprivation', name: '情緒的剥奪', belief: '自分の話を聴き、理解し、守ってくれる人は誰もいない。', behavior: '自分のニーズを伝えることを諦め、何も言わずに不機嫌になったり、原因不明の体調不良として苦しさを訴える。' },
                { key: 'defectiveness_shame', name: '欠陥／恥', belief: '自分は本質的にダメな人間で、愛される価値がない。', behavior: '批判や拒絶に極めて敏感で、少しの注意でも全人格を否定されたように落ち込む。' },
                { key: 'social_isolation', name: '社会的孤立／疎外', belief: '自分は他の人とは根本的に異なっていて、どこにも居場所がない。', behavior: '職場や友人グループの中にいても、常に一歩引いた「部外者」として振る舞う。' }
            ],
            domain2: [
                { key: 'dependence_incompetence', name: '依存／無能', belief: '自分一人では日々の生活や重大な決定をこなせない。', behavior: '進路決定から今日の献立まで、誰かに決めてもらわないと不安で動けない。' },
                { key: 'vulnerability_to_harm', name: '損害や疾病に対する脆弱性', belief: 'いつか必ず、恐ろしい災難が自分を襲う。世界は危険に満ちている。', behavior: '些細な体の異変で重病を疑って病院を巡ったり、ニュースを見て過度に不安になる。' },
                { key: 'enmeshment', name: '巻き込まれ／未発達な自己', belief: '自分と親（またはパートナー）の間に境界線はない。', behavior: '相手の機嫌によって自分の気分が180度変わる。自分の好みや意見がわからない。' },
                { key: 'failure', name: '失敗', belief: '自分は同年代の人に比べて根本的に能力が低く、何をやっても最終的には失敗する。', behavior: '実際に能力があっても「これは運が良かっただけ」と成功を無視する。' }
            ],
            domain3: [
                { key: 'entitlement_grandiosity', name: '権利要求／尊大さ', belief: '自分は特別な存在であり、一般のルールに従う必要はない。', behavior: '他者のニーズを軽視し、自分の欲望を最優先する。順番待ちを嫌う。' },
                { key: 'insufficient_self_control', name: '自制と自律の欠如', belief: '不快なことや退屈なこと、欲求不満を我慢することは耐えられない。', behavior: '目標達成のための地道な努力ができず、すぐに投げ出す。衝動的に行動する。' }
            ],
            domain4: [
                { key: 'subjugation', name: '服従', belief: '相手に従わないと怒られる、見捨てられる、あるいは報復される。', behavior: '自分の本音を押し殺して相手の言いなりになる。内面には強い怒りが溜まっている。' },
                { key: 'self_sacrifice', name: '自己犠牲', belief: '他人の苦しみを放っておくのは罪悪感がある。相手を助けるのが自分の役割だ。', behavior: '頼まれてもいないのに他人の世話を焼き、自分の心身を削る。' },
                { key: 'approval_seeking', name: '承認欲求／評価の追求', belief: 'ありのままの自分には価値がない。他者から認められて初めて存在していい。', behavior: '「どう見られるか」を行動基準にする。地位、外見、財産などの追求に執着。' }
            ],
            domain5: [
                { key: 'negativity_pessimism', name: '否定／悲観', belief: '人生のポジティブな面はまやかしで、ネガティブな側面こそが真実だ。', behavior: '常に最悪のシナリオを想定し、周囲の明るい話題にも水を差してしまう。' },
                { key: 'emotional_inhibition', name: '感情抑制', belief: '感情を素直に出すのは、恥ずべきことであり危険なことだ。', behavior: '感情の起伏が乏しく、「何を考えているかわからない」と思われる。' },
                { key: 'unrelenting_standards', name: '厳密な基準／過度の批判', belief: '自分も他人も、常に最高水準の基準を満たさなければならない。', behavior: '完璧主義で、効率やルール、細部に過剰にこだわる。常に焦燥感に駆られる。' },
                { key: 'punitiveness', name: '罰への懲罰的志向', belief: '間違いを犯した人間は、厳しく罰せられるべきだ。', behavior: '他人のミスを許せず、攻撃的に非難する。自分の失敗に対しても過酷な自己処罰を行う。' }
            ]
        },

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

        takeSnapshot() {
            const snapshot = {
                stressor: this.formData.stressor,
                cognition: this.formData.cognition,
                mood: this.formData.mood,
                body_reaction: this.formData.body_reaction,
                behavior: this.formData.behavior,
                stimulated_schemas: JSON.stringify(this.formData.stimulated_schemas)
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
                JSON.stringify(this.formData.stimulated_schemas) !== snapshot.stimulated_schemas
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

        async performAutoSave() {
            this.autoSaving = true;

            try {
                if (this.itemId) {
                    const res = await fetch(`/api/stressor-and-responses/${this.itemId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        this.showAutoSaveNotification();
                    }
                } else {
                    const res = await fetch('/api/stressor-and-responses', {
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
                        this.showAutoSaveNotification();
                    }
                }
            } catch (error) {
                console.error('自動保存に失敗しました:', error);
            } finally {
                this.autoSaving = false;
            }
        },

        showAutoSaveNotification() {
            this.showAutoSaveToast = true;
            setTimeout(() => {
                this.showAutoSaveToast = false;
            }, 2000);
        },

        async loadItem() {
            this.loading = true;
            try {
                const res = await fetch(`/api/stressor-and-responses/${this.itemId}`);
                if (res.ok) {
                    const item = await res.json();
                    this.formData.stressor = item.stressor || '';
                    this.formData.cognition = item.cognition || '';
                    this.formData.mood = item.mood || '';
                    this.formData.body_reaction = item.body_reaction || '';
                    this.formData.behavior = item.behavior || '';
                    this.formData.stimulated_schemas = item.stimulated_schemas || [];
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

        toggleSchemaDetail(schemaKey) {
            if (this.openSchemaDetail === schemaKey) {
                this.openSchemaDetail = null;
            } else {
                this.openSchemaDetail = schemaKey;
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
                const res = await fetch('/api/stressor-and-responses', {
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
                const res = await fetch(`/api/stressor-and-responses/${this.itemId}`, {
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
