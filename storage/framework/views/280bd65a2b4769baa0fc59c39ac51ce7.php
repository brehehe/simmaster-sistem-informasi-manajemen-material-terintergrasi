<div>
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-blue-700">📦 Stock Material Polres</h1>
            <p class="text-gray-500 text-sm mt-0.5">Detail stok material — material utama & pendukung — beserta posisi rak & nomor seri</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Total Jenis Material</div>
            <div class="text-2xl font-bold"><?php echo e($stocks->total()); ?></div>
            <div class="text-xs opacity-70 mt-1">Kelompok material</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-600 to-green-600 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Total Stok Keseluruhan</div>
            <div class="text-2xl font-bold"><?php echo e(number_format($totalStock, 0, ',', '.')); ?></div>
            <div class="text-xs opacity-70 mt-1">Unit tersedia</div>
        </div>
        <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Material Bernomor Seri</div>
            <div class="text-2xl font-bold"><?php echo e(number_format($serializedCount, 0, ',', '.')); ?></div>
            <div class="text-xs opacity-70 mt-1">Item tercatat</div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Total Baris Detail</div>
            <div class="text-2xl font-bold"><?php echo e(number_format($totalDetailRows, 0, ',', '.')); ?></div>
            <div class="text-xs opacity-70 mt-1">Baris stock detail</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-<?php echo e(auth()->user()->hasRole('Admin') ? '4' : '3'); ?> gap-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('Admin')): ?>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Filter Polres</label>
                    <div wire:ignore>
                        <select id="select-police-station" x-data x-init="
                            const selectize = $($el).selectize({
                                dropdownParent: 'body', allowClear: true, plugins: ['clear_button'],
                                onChange: function(val) { window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('policeStationId', val); }
                            })[0].selectize;
                        " placeholder="Semua Polres">
                            <option value="">Semua Polres</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $policeStations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $station): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($station->id); ?>" <?php if($policeStationId == $station->id): echo 'selected'; endif; ?>><?php echo e($station->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Filter Jenis Material</label>
                <div wire:ignore>
                    <select id="select-type" x-data x-init="
                        const selectize = $($el).selectize({
                            dropdownParent: 'body', allowClear: true, plugins: ['clear_button'],
                            onChange: function(val) { window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('typeId', val); }
                        })[0].selectize;
                    " placeholder="Semua Material">
                        <option value="">Semua Material</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($t->id); ?>" <?php if($typeId == $t->id): echo 'selected'; endif; ?>><?php echo e($t->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Filter Detail Material</label>
                <div wire:ignore wire:key="select-type-detail-wrapper-<?php echo e($typeId); ?>">
                    <select id="select-type-detail-<?php echo e($typeId); ?>" x-data x-init="
                        const selectize = $($el).selectize({
                            dropdownParent: 'body', allowClear: true, plugins: ['clear_button'],
                            onChange: function(val) { window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('typeDetailId', val); }
                        })[0].selectize;
                    " placeholder="Semua Detail">
                        <option value="">Semua Detail</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $typeDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $td): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($td->id); ?>" <?php if($typeDetailId == $td->id): echo 'selected'; endif; ?>><?php echo e($td->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pencarian</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari material, no seri, rak..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>
        </div>

        <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-500">Tampilkan:</span>
            <select wire:model.live="perPage" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm bg-gray-50">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-xs text-gray-500">data per halaman</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm min-w-[900px]">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-700 to-blue-600 text-white">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase w-10">No</th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('Admin')): ?>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Polres</th>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase min-w-[130px]">Jenis Material</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase min-w-[110px]">Detail Material</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase min-w-[120px] bg-blue-800/30">No Seri A</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase min-w-[120px] bg-blue-800/30">No Seri B</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase min-w-[110px]">Posisi Rak</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase w-28 bg-green-700/30">Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupIndex => $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $details = $stock['details'];
                            $rowspan = $details->count();
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detailIndex => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailIndex == 0): ?>
                                    <td class="px-4 py-3 text-center text-xs text-gray-500 align-top border-r border-gray-200 font-medium" rowspan="<?php echo e($rowspan); ?>">
                                        <?php echo e($stocks->firstItem() + $groupIndex); ?>

                                    </td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('Admin')): ?>
                                        <td class="px-4 py-3 text-xs text-gray-700 align-top border-r border-gray-200" rowspan="<?php echo e($rowspan); ?>">
                                            <span class="font-semibold"><?php echo e($stock['policeStation']->name ?? '-'); ?></span>
                                        </td>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <td class="px-4 py-3 align-top border-r border-gray-200" rowspan="<?php echo e($rowspan); ?>">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                            <?php echo e($stock['type']->name ?? '-'); ?>

                                        </span>
                                        <div class="text-[10px] text-gray-400 mt-1">
                                            Total: <span class="font-bold text-blue-600"><?php echo e(number_format($stock['total_quantity'], 0, ',', '.')); ?></span> unit
                                        </div>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    <?php echo e($detail->typeDetail->name ?? '-'); ?>

                                </td>

                                
                                <td class="px-4 py-3 text-xs bg-blue-50/30">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detail->number_serial_first || $detail->code): ?>
                                        <span class="font-mono text-blue-700 font-semibold">
                                            <?php echo e($detail->number_serial_first ?: $detail->code); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-300">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-4 py-3 text-xs bg-blue-50/30">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detail->number_serial_second): ?>
                                        <span class="font-mono text-blue-600"><?php echo e($detail->number_serial_second); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-300">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detail->rack?->name): ?>
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-orange-700 bg-orange-50 border border-orange-200 px-2 py-0.5 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4zM3 9a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                                            <?php echo e($detail->rack->name); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-300 text-xs">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-4 py-3 text-center bg-green-50/40">
                                    <span class="text-sm font-bold <?php echo e($detail->total_quantity > 0 ? 'text-green-700' : 'text-red-400'); ?>">
                                        <?php echo e(number_format($detail->total_quantity, 0, ',', '.')); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <svg class="mx-auto h-14 w-14 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-gray-500 font-medium">Tidak ada data stock</p>
                                <p class="text-gray-400 text-xs mt-1">Belum ada stock material di level Polres</p>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stocks->hasPages()): ?>
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-gray-500">
                    Menampilkan <strong><?php echo e($stocks->firstItem()); ?>–<?php echo e($stocks->lastItem()); ?></strong> dari <strong><?php echo e($stocks->total()); ?></strong> kelompok
                </div>
                <div class="flex items-center gap-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$stocks->onFirstPage()): ?>
                        <button wire:click="previousPage" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php $cp = $stocks->currentPage(); $lp = $stocks->lastPage(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($p = max(1, $cp-2); $p <= min($lp, $cp+2); $p++): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p == $cp): ?>
                            <span class="px-3 py-1.5 text-xs font-bold text-white bg-blue-600 rounded-lg"><?php echo e($p); ?></span>
                        <?php else: ?>
                            <button wire:click="gotoPage(<?php echo e($p); ?>)" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"><?php echo e($p); ?></button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stocks->hasMorePages()): ?>
                        <button wire:click="nextPage" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 text-xs text-gray-500">
                Menampilkan <strong><?php echo e($stocks->count()); ?></strong> hasil
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /var/www/html/armaster/resources/views/livewire/admin/menu-polres/stock/admin-menu-polres-stock-index.blade.php ENDPATH**/ ?>