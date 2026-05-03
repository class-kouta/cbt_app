@extends('layouts.app')

@section('title', 'マインドフルネス瞑想 - ' . config('app.name'))
@section('page-title', 'マインドフルネス瞑想')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="max-w-4xl mx-auto" x-data="mindfulnessPlayer()" x-cloak>
    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 md:p-10 border border-gray-200">
        <!-- ヘッダー -->
        <div class="flex flex-col items-center text-center mb-8">
            <div class="w-16 h-16 sm:w-20 sm:h-20 mb-4 text-green-600">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M12 21C12 21 4 16 4 10C4 7 6 4 9 4C10.5 4 11.5 5 12 6C12.5 5 13.5 4 15 4C18 4 20 7 20 10C20 16 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="8" r="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 10V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M9 17C9 17 10.5 15 12 15C13.5 15 15 17 15 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">マインドフルネス瞑想</h1>
            <p class="text-gray-500 text-sm sm:text-base">自然の音を聴きながら、心を落ち着けましょう</p>
        </div>

        <!-- 音の種類選択 -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">🎵 音の種類を選択</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <template x-for="sound in sounds" :key="sound.id">
                    <button
                        @click="selectedSound = sound.id"
                        :class="selectedSound === sound.id
                            ? 'bg-green-100 border-green-500 text-green-800 ring-2 ring-green-300'
                            : 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100'"
                        class="flex flex-col items-center gap-2 p-4 sm:p-5 rounded-xl border-2 transition-all duration-200"
                        :disabled="isPlaying"
                    >
                        <span class="text-2xl sm:text-3xl" x-text="sound.emoji"></span>
                        <span class="text-sm sm:text-base font-medium" x-text="sound.label"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- 再生時間選択 -->
        <div class="mb-8">
            <label class="block text-sm font-semibold text-gray-700 mb-3">⏱ 再生時間を選択</label>
            <div class="grid grid-cols-3 gap-2 sm:gap-3">
                <template x-for="d in durations" :key="d">
                    <button
                        @click="selectedDuration = d"
                        :class="selectedDuration === d
                            ? 'bg-green-100 border-green-500 text-green-800 ring-2 ring-green-300'
                            : 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100'"
                        class="py-3 px-2 rounded-xl border-2 transition-all duration-200 text-center"
                        :disabled="isPlaying"
                    >
                        <span class="text-sm sm:text-base font-semibold" x-text="d + '分'"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- プレイヤーコントロール -->
        <div class="bg-gradient-to-r from-green-50 to-teal-50 rounded-xl p-6 sm:p-8">
            <!-- 再生中の表示 -->
            <div x-show="isPlaying || isPaused" class="mb-6">
                <div class="flex items-center justify-center gap-2 mb-4">
                    <span class="text-xl" x-text="sounds.find(s => s.id === selectedSound)?.emoji || ''"></span>
                    <span class="text-lg font-semibold text-gray-800" x-text="sounds.find(s => s.id === selectedSound)?.label || ''"></span>
                    <span class="text-gray-500">-</span>
                    <span class="text-lg text-gray-600" x-text="selectedDuration + '分'"></span>
                </div>

                <!-- プログレスバー -->
                <div class="relative w-full h-2 bg-gray-200 rounded-full mb-2 cursor-pointer" @click="seek($event)">
                    <div
                        class="absolute top-0 left-0 h-full bg-green-500 rounded-full transition-all duration-300"
                        :style="'width: ' + progressPercent + '%'"
                    ></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500">
                    <span x-text="formatTime(currentTime)"></span>
                    <span x-text="formatTime(totalDuration)"></span>
                </div>
            </div>

            <!-- ボタン群 -->
            <div class="flex items-center justify-center gap-4">
                <!-- 再生ボタン -->
                <template x-if="!isPlaying && !isPaused">
                    <button
                        @click="play()"
                        :disabled="!selectedSound || !selectedDuration || isLoading"
                        :class="(!selectedSound || !selectedDuration)
                            ? 'bg-gray-300 cursor-not-allowed text-gray-500'
                            : 'bg-green-600 hover:bg-green-700 text-white shadow-lg hover:shadow-xl'"
                        class="flex items-center gap-2 px-8 py-3 rounded-full font-semibold transition-all duration-200"
                    >
                        <template x-if="isLoading">
                            <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!isLoading">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </template>
                        <span x-text="isLoading ? '読み込み中...' : '再生'"></span>
                    </button>
                </template>

                <template x-if="isPlaying">
                    <div class="flex items-center gap-3">
                        <button
                            @click="pause()"
                            class="flex items-center gap-2 px-6 py-3 rounded-full font-semibold bg-yellow-500 hover:bg-yellow-600 text-white shadow-lg hover:shadow-xl transition-all duration-200"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                            </svg>
                            <span>一時停止</span>
                        </button>
                        <button
                            @click="stop()"
                            class="flex items-center gap-2 px-6 py-3 rounded-full font-semibold bg-red-500 hover:bg-red-600 text-white shadow-lg hover:shadow-xl transition-all duration-200"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 6h12v12H6z"/>
                            </svg>
                            <span>停止</span>
                        </button>
                    </div>
                </template>

                <template x-if="isPaused">
                    <div class="flex items-center gap-3">
                        <button
                            @click="resume()"
                            class="flex items-center gap-2 px-6 py-3 rounded-full font-semibold bg-green-600 hover:bg-green-700 text-white shadow-lg hover:shadow-xl transition-all duration-200"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            <span>再開</span>
                        </button>
                        <button
                            @click="stop()"
                            class="flex items-center gap-2 px-6 py-3 rounded-full font-semibold bg-red-500 hover:bg-red-600 text-white shadow-lg hover:shadow-xl transition-all duration-200"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 6h12v12H6z"/>
                            </svg>
                            <span>停止</span>
                        </button>
                    </div>
                </template>
            </div>

            <!-- エラー表示 -->
            <div x-show="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm text-center" x-text="errorMessage"></div>
        </div>

        <!-- 戻るリンク -->
        <div class="mt-8 text-center">
            <a href="/" class="inline-flex items-center gap-2 text-green-600 hover:text-green-800 font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                トップに戻る
            </a>
        </div>
    </div>
</div>

<script>
function mindfulnessPlayer() {
    return {
        sounds: @json($sounds),
        durations: @json($durations),
        selectedSound: null,
        selectedDuration: null,
        audio: null,
        isPlaying: false,
        isPaused: false,
        isLoading: false,
        currentTime: 0,
        totalDuration: 0,
        progressPercent: 0,
        errorMessage: '',
        progressInterval: null,
        _listeners: {},

        async play() {
            if (!this.selectedSound || !this.selectedDuration) return;
            this.stop();
            this.isLoading = true;
            this.errorMessage = '';

            try {
                const res = await fetch(
                    `/api/mindfulness/audio-url?sound=${this.selectedSound}&duration=${this.selectedDuration}`
                );
                if (!res.ok) throw new Error('音声URLの取得に失敗しました');
                const data = await res.json();

                this.audio = new Audio(data.url);

                this._listeners.loadedmetadata = () => {
                    this.totalDuration = this.audio.duration;
                    this.isLoading = false;
                    this.isPlaying = true;
                    this.startProgressUpdate();
                };
                this._listeners.ended = () => {
                    this.isPlaying = false;
                    this.isPaused = false;
                    this.progressPercent = 100;
                    this.stopProgressUpdate();
                };
                this._listeners.error = () => {
                    this.isLoading = false;
                    this.isPlaying = false;
                    this.errorMessage = '音声の読み込みに失敗しました。しばらくしてからもう一度お試しください。';
                    this.stopProgressUpdate();
                };

                this.audio.addEventListener('loadedmetadata', this._listeners.loadedmetadata);
                this.audio.addEventListener('ended', this._listeners.ended);
                this.audio.addEventListener('error', this._listeners.error);

                this.audio.play();
            } catch (e) {
                this.isLoading = false;
                this.errorMessage = '音声の取得に失敗しました。ネットワーク接続を確認してください。';
            }
        },

        pause() {
            if (this.audio) {
                this.audio.pause();
                this.isPlaying = false;
                this.isPaused = true;
                this.stopProgressUpdate();
            }
        },

        resume() {
            if (this.audio) {
                this.audio.play();
                this.isPlaying = true;
                this.isPaused = false;
                this.startProgressUpdate();
            }
        },

        stop() {
            if (this.audio) {
                this.audio.pause();
                this.audio.currentTime = 0;
                for (const [event, handler] of Object.entries(this._listeners)) {
                    this.audio.removeEventListener(event, handler);
                }
                this.audio = null;
            }
            this._listeners = {};
            this.isPlaying = false;
            this.isPaused = false;
            this.isLoading = false;
            this.currentTime = 0;
            this.totalDuration = 0;
            this.progressPercent = 0;
            this.stopProgressUpdate();
        },

        seek(event) {
            if (!this.audio || !this.totalDuration) return;
            const bar = event.currentTarget;
            const rect = bar.getBoundingClientRect();
            const clickX = event.clientX - rect.left;
            const percent = clickX / rect.width;
            this.audio.currentTime = percent * this.totalDuration;
            this.updateProgress();
        },

        startProgressUpdate() {
            this.stopProgressUpdate();
            this.progressInterval = setInterval(() => {
                this.updateProgress();
            }, 250);
        },

        stopProgressUpdate() {
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
                this.progressInterval = null;
            }
        },

        updateProgress() {
            if (this.audio) {
                this.currentTime = this.audio.currentTime;
                this.totalDuration = this.audio.duration || 0;
                this.progressPercent = this.totalDuration > 0
                    ? (this.currentTime / this.totalDuration) * 100
                    : 0;
            }
        },

        formatTime(seconds) {
            if (!seconds || isNaN(seconds)) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return mins + ':' + secs.toString().padStart(2, '0');
        },
    };
}
</script>
@endsection
