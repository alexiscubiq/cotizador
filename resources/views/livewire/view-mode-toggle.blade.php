<div class="border-t border-gray-200 dark:border-gray-700 px-3 py-3">
    <div class="flex items-center justify-between gap-2">
        <span class="text-xs text-gray-500 dark:text-gray-400">Vista:</span>
        <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 rounded-md p-0.5">
            <button
                wire:click="toggleMode('wts')"
                class="px-2 py-1 text-xs rounded transition-colors {{ $viewMode === 'wts' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                type="button">
                WTS
            </button>
            <button
                wire:click="toggleMode('supplier')"
                class="px-2 py-1 text-xs rounded transition-colors {{ $viewMode === 'supplier' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                type="button">
                Proveedor
            </button>
        </div>
    </div>
</div>
