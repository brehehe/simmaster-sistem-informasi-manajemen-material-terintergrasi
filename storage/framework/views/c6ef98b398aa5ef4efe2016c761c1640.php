<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="<?php echo e(route('menu-polres.material-usage-detail')); ?>"  class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-blue-600">
                        <?php echo e($isEditMode ? 'Detail Material Digunakan' : 'Input Material Digunakan'); ?>

                    </h1>
                </div>
                <p class="text-gray-500 ml-14">Data penggunaan material di Polres</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('success')): ?>
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('error')): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Header Info Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50/50">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Informasi Penggunaan
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <!-- Tanggal (auto, no code shown) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="date" <?php if($isEditMode): echo 'disabled'; endif; ?> class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:bg-gray-100 disabled:text-gray-500">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasRole('Admin')): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Polres <span class="text-red-500">*</span></label>
                        <div wire:ignore wire:key="select-police-station-<?php echo e(rand()); ?>">
                            <select id="select-police-station" wire:model="policeStationId" <?php if($isEditMode): echo 'disabled'; endif; ?>
                                x-data x-init="
                                    setTimeout(() => {
                                        const el = $($el).selectize({
                                            dropdownParent: 'body',
                                            allowClear: true,
                                            onChange: function(val) { window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('policeStationId', val || ''); }
                                        })[0].selectize;
                                        el.setValue(window.Livewire.find('<?php echo e($_instance->getId()); ?>').get('policeStationId'), true);
                                    }, 10);
                                ">
                                <option value="">-- Pilih Polres --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $policeStations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ps->id); ?>"><?php echo e($ps->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['policeStationId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea wire:model="description" rows="1" <?php if($isEditMode): echo 'disabled'; endif; ?> class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:bg-gray-100 disabled:text-gray-500" placeholder="Catatan penggunaan material..."></textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Material Utama -->
                <div class="border border-blue-100 bg-blue-50/50 p-4 rounded-xl">
                    <label class="flex items-center gap-2 text-sm font-semibold text-blue-900 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                        </svg>
                        Material Utama <span class="text-red-500">*</span>
                    </label>
                    <div wire:ignore wire:key="select-type-id-<?php echo e(rand()); ?>">
                        <select id="select-type-id" wire:model="typeId" <?php if($isEditMode): echo 'disabled'; endif; ?>
                            x-data x-init="
                                setTimeout(() => {
                                    const el = $($el).selectize({
                                        dropdownParent: 'body',
                                        allowClear: true,
                                        onChange: function(val) { window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('typeId', val || ''); }
                                    })[0].selectize;
                                    el.setValue(window.Livewire.find('<?php echo e($_instance->getId()); ?>').get('typeId'), true);
                                }, 10);
                            ">
                            <option value="">-- Pilih Material --</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['typeId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($typeId): ?>
                <div class="flex items-start gap-3 p-4 rounded-xl bg-green-50 border border-green-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-xs text-green-800">
                        <p class="font-bold mb-1">Petunjuk Pengisian Detail:</p>
                        <ul class="space-y-0.5 list-disc list-inside">
                            <li>Isi <strong>No Seri A</strong> (awal) dan <strong>No Seri B</strong> (akhir) sesuai barang</li>
                            <li>Jumlah dihitung otomatis dari stok tersedia</li>
                            <li>Tambahkan baris baru untuk <strong>material pendukung</strong> seperti blanko, dll.</li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50/50 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Detail Penggunaan Material
            </h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isEditMode): ?>
                <div class="flex items-center gap-2">
                    <button wire:click="addDetail" type="button"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Tambah Item
                    </button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="p-0">
            <div class="overflow-x-auto overflow-y-visible pb-24">
                <table class="w-full text-sm text-left align-top border-collapse min-w-[1400px]">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                        <tr>
                            <th class="px-3 py-3 font-semibold w-10 text-center text-xs">No</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_type_detail): ?>
                                <th class="px-3 py-3 font-semibold text-xs min-w-[130px]">Detail Material</th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[130px]">Service</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[130px]">Service Detail</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[150px] bg-blue-50">No Seri A (Awal)</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[150px] bg-blue-50">No Seri B (Akhir)</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[120px]">Jenis Penggunaan</th>
                            <th class="px-3 py-3 font-semibold text-xs w-24 text-center bg-blue-100">Tersedia</th>
                            <th class="px-3 py-3 font-semibold text-xs w-24">Jumlah <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[130px]">Catatan</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isEditMode): ?>
                                <th class="px-3 py-3 font-semibold w-14 text-center text-xs"></th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $rowTypeDetailId = $detail['type_detail_id'] ?? null;
                                $rowServiceId = $detail['service_id'] ?? null;
                                $filteredServices = collect($this->services)->where(
                                    $rowTypeDetailId ? 'type_detail_id' : 'type_id',
                                    $rowTypeDetailId ? $rowTypeDetailId : $typeId
                                );
                                $selectedSvc = collect($this->services)->firstWhere('id', $rowServiceId);
                                $filteredServiceDetails = data_get($selectedSvc, 'details', []);
                            ?>
                            <tr wire:key="row-<?php echo e($index); ?>-<?php echo e($typeId); ?>" class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-3 text-center font-medium text-gray-500 align-top text-xs pt-4"><?php echo e($index + 1); ?></td>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_type_detail): ?>
                                    <td class="px-3 py-3 align-top">
                                        <select wire:model.live="details.<?php echo e($index); ?>.type_detail_id" <?php if($isEditMode): echo 'disabled'; endif; ?>
                                            class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                            <option value="">-- Semua Detail --</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->typeDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $td): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(data_get($td, 'id')); ?>"><?php echo e(data_get($td, 'name')); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <td class="px-3 py-3 align-top">
                                    <select wire:model.live="details.<?php echo e($index); ?>.service_id" <?php if($isEditMode): echo 'disabled'; endif; ?>
                                        class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                        <option value="">-- Semua Service --</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $filteredServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(data_get($svc, 'id')); ?>"><?php echo e(data_get($svc, 'name')); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <select wire:model.live="details.<?php echo e($index); ?>.service_detail_id" <?php if($isEditMode): echo 'disabled'; endif; ?>
                                        class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                        <option value="">-- Semua --</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $filteredServiceDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sdt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(data_get($sdt, 'id')); ?>"><?php echo e(data_get($sdt, 'name')); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </td>

                                
                                <td class="px-2 py-3 align-top bg-blue-50/30">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_with_serial_number): ?>
                                        
                                        <div wire:ignore wire:key="stock-key-<?php echo e($index); ?>-<?php echo e(md5(json_encode($stockOptions[$index] ?? []))); ?>">
                                            <select x-data x-init="
                                                const selectize = $($el).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    onChange: function(val) {
                                                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('details.<?php echo e($index); ?>.selected_stock_key', val);
                                                    }
                                                })[0].selectize;
                                            " wire:model="details.<?php echo e($index); ?>.selected_stock_key" <?php if($isEditMode): echo 'disabled'; endif; ?>
                                                class="w-full text-xs rounded border border-blue-300 disabled:bg-gray-100">
                                                <option value="">-- Pilih Barang --</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $stockOptions[$index] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($opt['key']); ?>"><?php echo e($opt['number_serial_first'] ?: $opt['item_code']); ?> (Stok: <?php echo e($opt['quantity']); ?>)</option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($detail['number_serial_first'])): ?>
                                            <div class="text-[10px] text-blue-700 font-mono mt-1 px-1"><?php echo e($detail['number_serial_first']); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php else: ?>
                                        <input type="text"
                                            wire:model.live="details.<?php echo e($index); ?>.number_serial_first"
                                            placeholder="Nomor Seri Awal..." <?php if($isEditMode): echo 'disabled'; endif; ?>
                                            class="w-full px-2 py-2 text-xs font-mono rounded border border-blue-300 focus:border-blue-500 bg-white disabled:bg-gray-100 disabled:text-gray-500">
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["details.{$index}.stock_detail_id"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-[10px] mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-2 py-3 align-top bg-blue-50/30">
                                    <input type="text"
                                        wire:model.live="details.<?php echo e($index); ?>.number_serial_second"
                                        placeholder="Nomor Seri Akhir..." <?php if($isEditMode): echo 'disabled'; endif; ?>
                                        class="w-full px-2 py-2 text-xs font-mono rounded border border-blue-300 focus:border-blue-500 bg-white disabled:bg-gray-100 disabled:text-gray-500">
                                </td>

                                
                                <td class="px-3 py-3 align-top">
                                    <select wire:model.live="details.<?php echo e($index); ?>.usage_type" <?php if($isEditMode): echo 'disabled'; endif; ?>
                                        class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                        <option value="Material Digunakan">Material Digunakan</option>
                                        <option value="Material Pendukung">Material Pendukung</option>
                                        <option value="Pembangunan">Pembangunan</option>
                                        <option value="Pemeliharaan">Pemeliharaan</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </td>

                                <!-- Tersedia -->
                                <td class="px-3 py-3 align-top bg-blue-50/40 text-center">
                                    <div class="font-bold <?php echo e($detail['available_quantity'] > 0 ? 'text-blue-600' : 'text-gray-400'); ?> pt-2 text-sm">
                                        <?php echo e(number_format($detail['available_quantity'], 0, ',', '.')); ?>

                                    </div>
                                    <div class="text-[10px] text-gray-400">unit</div>
                                </td>

                                <!-- Quantity -->
                                <td class="px-3 py-3 align-top">
                                    <input type="number" min="1" step="1" max="<?php echo e($detail['available_quantity']); ?>"
                                        wire:model.live="details.<?php echo e($index); ?>.quantity"
                                        placeholder="Qty" <?php if($isEditMode): echo 'disabled'; endif; ?>
                                        class="w-full px-2 py-2 text-xs font-bold text-center rounded border border-blue-300 focus:border-blue-500 bg-blue-50 disabled:bg-gray-100 disabled:text-gray-400 text-blue-700">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["details.{$index}.quantity"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-[10px] mt-1 text-center"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <input type="text" wire:model.defer="details.<?php echo e($index); ?>.description" placeholder="Ket..." <?php if($isEditMode): echo 'disabled'; endif; ?>
                                        class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                </td>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isEditMode): ?>
                                    <td class="px-3 py-3 text-center align-top pt-4">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($details) > 1): ?>
                                            <button type="button" wire:click="removeDetail(<?php echo e($index); ?>)"
                                                class="p-1.5 inline-flex items-center justify-center rounded bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Hapus Item">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="11" class="py-12 text-center text-gray-400 italic">Klik "Tambah Item" untuk mulai input penggunaan material...</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 mt-4">
        <a href="<?php echo e(route('menu-polres.material-usage-detail')); ?>"  class="w-full sm:w-auto px-8 py-3 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all text-center">Lihat Riwayat</a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isEditMode): ?>
            <button wire:click="save" type="button"
                class="w-full sm:w-auto px-10 py-3 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl hover:from-blue-700 hover:to-indigo-800 shadow-xl shadow-blue-500/30 transition-all transform hover:scale-105 text-center">
                <span wire:loading.remove wire:target="save">💾 Simpan Data</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <script>
        document.addEventListener('lived', () => {
            $('.selectize-dropdown').remove();
        });
    </script>
</div>
<?php /**PATH /var/www/html/armaster/resources/views/livewire/admin/menu-polres/material-usage/detail/admin-menu-polres-material-usage-detail-index.blade.php ENDPATH**/ ?>