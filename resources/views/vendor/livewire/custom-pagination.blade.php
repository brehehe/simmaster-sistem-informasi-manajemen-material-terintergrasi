@if ($paginator->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Info Text -->
            <div class="text-sm text-gray-600">
                Menampilkan <span class="font-semibold">{{ $paginator->firstItem() }}</span>
                sampai <span class="font-semibold">{{ $paginator->lastItem() }}</span>
                dari <span class="font-semibold">{{ $paginator->total() }}</span> hasil
            </div>

            <!-- Pagination Numbers -->
            <div class="flex items-center gap-1">
                {{-- Previous Button --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                @endif

                {{-- Page Numbers --}}
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    $start = max(1, $currentPage - 2);
                    $end = min($lastPage, $currentPage + 2);
                @endphp

                @if ($start > 1)
                    <button wire:click="gotoPage(1)" wire:loading.attr="disabled"
                        class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        1
                    </button>
                    @if ($start > 2)
                        <span class="px-2 py-2 text-sm text-gray-400">...</span>
                    @endif
                @endif

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $currentPage)
                        <span class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg">
                            {{ $page }}
                        </span>
                    @else
                        <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled"
                            class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            {{ $page }}
                        </button>
                    @endif
                @endfor

                @if ($end < $lastPage)
                    @if ($end < $lastPage - 1)
                        <span class="px-2 py-2 text-sm text-gray-400">...</span>
                    @endif
                    <button wire:click="gotoPage({{ $lastPage }})" wire:loading.attr="disabled"
                        class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        {{ $lastPage }}
                    </button>
                @endif

                {{-- Next Button --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                @else
                    <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        <div class="text-sm text-gray-600">
            Menampilkan <span class="font-semibold">{{ $paginator->count() }}</span> hasil
        </div>
    </div>
@endif
