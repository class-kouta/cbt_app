@props([
    'themeColorFrom' => 'emerald-500',
    'themeColorTo' => 'teal-500',
    'themeBorderColor' => 'emerald-500',
])

<!-- ページネーション -->
<div x-show="!loading && total > 0" class="mt-6 bg-white rounded-xl shadow-md p-4">
    <!-- 件数表示 -->
    <div class="text-center text-sm text-gray-600 mb-4">
        <span x-text="'全' + total.toLocaleString() + '件中　' + (from || 0) + ' ～ ' + (to || 0) + '件目を表示'"></span>
    </div>
    
    <!-- ページネーションボタン -->
    <div x-show="lastPage > 1" class="flex justify-center items-center gap-2 flex-wrap">
        <!-- 前へ -->
        <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
            class="px-3 py-2 text-sm border border-gray-300 rounded-lg transition-all"
        >
            ‹
        </button>
        
        <!-- ページ番号 -->
        <template x-for="page in visiblePages" :key="page">
            <button
                @click="goToPage(page)"
                :class="page === currentPage 
                    ? 'bg-gradient-to-r from-{{ $themeColorFrom }} to-{{ $themeColorTo }} text-white border-{{ $themeBorderColor }}' 
                    : 'hover:bg-gray-100 border-gray-300'"
                class="px-3 py-2 text-sm border rounded-lg transition-all min-w-[40px]"
                x-text="page"
            ></button>
        </template>
        
        <!-- 次へ -->
        <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === lastPage"
            :class="currentPage === lastPage ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
            class="px-3 py-2 text-sm border border-gray-300 rounded-lg transition-all"
        >
            ›
        </button>
    </div>
</div>
