<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">
                    <?php echo e($isEditMode ? 'Edit' : 'Tambah'); ?> Stock Awal
                </h1>
                <p class="text-gray-500 mt-1"><?php echo e($isEditMode ? 'Perbarui' : 'Buat'); ?> data Stock Awal</p>
            </div>
            <a href="<?php echo e(route('menu-polres.last-stock')); ?>" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('error')): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Main Form Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Utama</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <!-- Code (Read-only) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="code" readonly
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="date" <?php if($lastStockId): echo 'disabled'; endif; ?>
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white focus:bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Material Master Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Material <span class="text-red-500">*</span>
                    </label>
                    <div wire:ignore wire:key="select-type-master-<?php echo e(rand()); ?>">
                        <select id="select-type-master" <?php if($lastStockId): echo 'disabled'; endif; ?> x-data x-ref="input" x-init="
                            const selectize = $($refs.input).selectize({
                                dropdownParent: 'body',
                                allowClear: true,
                                plugins: <?php echo e($lastStockId ? '[]' : "['clear_button']"); ?>,
                                onChange: function(e) {
                                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('typeId', e ? e : '');
                                }
                            })[0].selectize;
                            if ($refs.input.disabled) { selectize.disable(); }
                        "
                            wire:model="typeId">
                            <option value="">-- Pilih Material --</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($type->id); ?>"
                                    <?php echo e($typeId == $type->id ? 'selected' : ''); ?>>
                                    <?php echo e($type->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['typeId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Police Station (Optional for Admin, auto for Polres) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Polres
                    </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSelectPoliceStation): ?>
                        <div wire:ignore wire:key="select-police-station-<?php echo e(rand()); ?>">
                            <select id="select-police-station" <?php if($lastStockId): echo 'disabled'; endif; ?> x-data x-ref="input" x-init="
                                const selectize = $($refs.input).selectize({
                                    dropdownParent: 'body',
                                    allowClear: true,
                                    plugins: <?php echo e($lastStockId ? '[]' : "['clear_button']"); ?>,
                                    onChange: function(e) {
                                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('policeStationId', e ? e : '');
                                    }
                                })[0].selectize;
                                if ($refs.input.disabled) { selectize.disable(); }
                            "
                                wire:model="policeStationId">
                                <option value="">-- Pilih Polres --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $policeStations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ps->id); ?>"
                                        <?php echo e($policeStationId == $ps->id ? 'selected' : ''); ?>><?php echo e($ps->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <?php
                            $selectedPolres = $policeStations->firstWhere('id', $policeStationId);
                        ?>
                        <input type="text" value="<?php echo e($selectedPolres?->name ?? '-'); ?>" readonly
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea wire:model="description" <?php if($lastStockId): echo 'disabled'; endif; ?> rows="1" placeholder="Masukkan deskripsi (opsional)"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white focus:bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Items Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-visible mb-20">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Perincian Item</h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lastStockId && $typeId): ?>
                    <button wire:click="addDetail" type="button"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Tambah Baris
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($typeId): ?>
                <div class="overflow-x-auto overflow-y-visible min-h-[400px]">
                    <table class="w-full text-sm text-left border-separate border-spacing-y-2">
                        <thead class="text-xs uppercase text-gray-400 font-bold">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3 min-w-[200px]">Detail Material</th>
                                <th class="px-4 py-3 min-w-[200px]">Servis</th>
                                <th class="px-4 py-3 min-w-[200px]">Detail Servis</th>
                                <th class="px-4 py-3 min-w-[200px]">Rak</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_with_serial_number): ?>
                                    <th class="px-4 py-3 min-w-[150px]">Kode</th>
                                    <th class="px-4 py-3 min-w-[150px]">SN 1</th>
                                    <th class="px-4 py-3 min-w-[150px]">SN 2</th>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <th class="px-4 py-3 min-w-[120px]">Qty <span class="text-red-500">*</span></th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lastStockId): ?>
                                    <th class="px-4 py-3 w-[50px]">Action</th>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="overflow-visible">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr wire:key="row-<?php echo e($index); ?>" class="bg-gray-50/50 hover:bg-gray-100/50 transition-colors duration-200">
                                    <td class="px-4 py-3 font-bold text-gray-500 rounded-l-xl"><?php echo e($index + 1); ?></td>
                                    
                                    <!-- Detail Material -->
                                    <td class="px-4 py-3 overflow-visible">
                                        <div wire:ignore wire:key="td-<?php echo e($index); ?>-<?php echo e($typeId); ?>-<?php echo e(rand()); ?>">
                                            <select <?php if($lastStockId): echo 'disabled'; endif; ?> class="selectize-td" x-data x-ref="input" x-init="
                                                $($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    onChange: function(e) {
                                                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('details.<?php echo e($index); ?>.type_detail_id', e ? e : '');
                                                    }
                                                });
                                            " wire:model="details.<?php echo e($index); ?>.type_detail_id">
                                                <option value="">-- Semua --</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $typeDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $td): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($td->id); ?>"><?php echo e($td->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                    </td>

                                    <!-- Service -->
                                    <td class="px-4 py-3 overflow-visible">
                                        <div wire:ignore wire:key="svc-<?php echo e($index); ?>-<?php echo e($typeId); ?>-<?php echo e(rand()); ?>">
                                            <select <?php if($lastStockId): echo 'disabled'; endif; ?> class="selectize-svc" x-data x-ref="input" x-init="
                                                $($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    onChange: function(e) {
                                                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('details.<?php echo e($index); ?>.service_id', e ? e : '');
                                                    }
                                                });
                                            " wire:model="details.<?php echo e($index); ?>.service_id">
                                                <option value="">-- Semua --</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($svc->id); ?>"><?php echo e($svc->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                    </td>

                                    <!-- Service Detail -->
                                    <td class="px-4 py-3 overflow-visible">
                                        <div wire:ignore wire:key="svcd-<?php echo e($index); ?>-<?php echo e($detail['service_id'] ?? 'none'); ?>-<?php echo e(rand()); ?>">
                                            <select <?php if($lastStockId): echo 'disabled'; endif; ?> class="selectize-svcd" x-data x-ref="input" x-init="
                                                $($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    onChange: function(e) {
                                                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('details.<?php echo e($index); ?>.service_detail_id', e ? e : '');
                                                    }
                                                });
                                            " wire:model="details.<?php echo e($index); ?>.service_detail_id">
                                                <option value="">-- Semua --</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($detail['service_id'])): ?>
                                                    <?php
                                                        $selectedService = collect($services)->firstWhere('id', $detail['service_id']);
                                                    ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedService && $selectedService->details): ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $selectedService->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($sd->id); ?>"><?php echo e($sd->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                    </td>

                                    <!-- Rack -->
                                    <td class="px-4 py-3 overflow-visible">
                                        <div wire:ignore wire:key="rack-<?php echo e($index); ?>-<?php echo e($policeStationId ?? 'none'); ?>-<?php echo e(rand()); ?>">
                                            <select <?php if($lastStockId): echo 'disabled'; endif; ?> class="selectize-rack" x-data x-ref="input" x-init="
                                                $($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    onChange: function(e) {
                                                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('details.<?php echo e($index); ?>.rack_id', e ? e : '');
                                                    }
                                                });
                                            " wire:model="details.<?php echo e($index); ?>.rack_id">
                                                <option value="">-- Tanpa Rak --</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $racks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($rack->id); ?>"><?php echo e($rack->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                    </td>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_with_serial_number): ?>
                                        <td class="px-4 py-3">
                                            <input type="text" wire:model.blur="details.<?php echo e($index); ?>.code" <?php if($lastStockId): echo 'disabled'; endif; ?>
                                                class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white" placeholder="Kode">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" wire:model.blur="details.<?php echo e($index); ?>.number_serial_first" <?php if($lastStockId): echo 'disabled'; endif; ?>
                                                class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white" placeholder="SN1">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" wire:model.blur="details.<?php echo e($index); ?>.number_serial_second" <?php if($lastStockId): echo 'disabled'; endif; ?>
                                                class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white" placeholder="SN2">
                                        </td>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <td class="px-4 py-3">
                                        <input type="number" wire:model.blur="details.<?php echo e($index); ?>.quantity" <?php if($lastStockId): echo 'disabled'; endif; ?> step="0.01"
                                            class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white" placeholder="0">
                                    </td>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lastStockId): ?>
                                        <td class="px-4 py-3 rounded-r-xl">
                                            <button type="button" wire:click="removeDetail(<?php echo e($index); ?>)"
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </td>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="py-12 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">Pilih Master Material terlebih dahulu</p>
                    <p class="text-gray-400 text-sm mt-1">Gunakan dropdown Material di bagian Informasi Utama</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md border-t border-gray-100 p-4 z-20 shadow-[0_-10px_20px_rgba(0,0,0,0.05)]">
        <div class="max-w-7xl mx-auto flex items-center justify-end gap-3 px-4">
            <a href="<?php echo e(route('menu-polres.last-stock')); ?>" 
                class="px-6 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                Batal
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lastStockId): ?>
                <button wire:click="save" type="button"
                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all duration-200">
                    <span wire:loading.remove wire:target="save">Simpan Data Stock Awal</span>
                    <span wire:loading wire:target="save">Memproses...</span>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/armaster/resources/views/livewire/admin/menu-polres/last-stock/detail/admin-menu-polres-last-stock-detail-index.blade.php ENDPATH**/ ?>