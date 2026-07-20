<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">
                    {{ $isEditMode ? 'Edit' : 'Tambah' }} Penerimaan Barang
                </h1>
                <p class="text-gray-500 mt-1">{{ $isEditMode ? 'Perbarui' : 'Buat' }} data Penerimaan Barang</p>
            </div>
            <a href="{{ route('menu-polda.reception') }}" 
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
    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Form Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Utama</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <!-- Nomor SPPM Korlantas -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nomor SPPM Korlantas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="code" placeholder="Contoh: SPPM/016/I/2026/KORLANTAS"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white">
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal SPPM -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal SPPM Korlantas <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="sppm_date"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white">
                    @error('sppm_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor BAPPM -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nomor BAPPM <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="bappm_number" placeholder="Contoh: BAPPM / 012 / V / 2026 / Ditlantas"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white">
                    @error('bappm_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal BAPPM -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal BAPPM <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="date"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white">
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tipe <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="type" @disabled($receptionId)
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-white text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 disabled:bg-gray-100 disabled:text-gray-500 disabled:border-gray-300 disabled:cursor-not-allowed">
                        <option value="penerimaan">Penerimaan</option>
                        <option value="stock-awal">Stock Awal</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name / Deskripsi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Keterangan BAPPM
                    </label>
                    <input type="text" wire:model="name" placeholder="Contoh: Penerimaan TNKB Korlantas Tahap I"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Regional Police -->
                @if (auth()->user()->hasRole('Admin'))
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Polda <span class="text-red-500">*</span>
                        </label>
                        @if ($canSelectRegionalPolice)
                            <div wire:ignore wire:key="select-regional-police-{{ rand() }}">
                                <select id="select-regional-police" wire:model="regionalPoliceId" @disabled($receptionId) x-data x-ref="input" x-init="
                                        const selectize = $($refs.input).selectize({
                                            dropdownParent: 'body',
                                            allowClear: true,
                                            onChange: function(e) {
                                                @this.set('regionalPoliceId', e ?? '');
                                            }
                                        })[0].selectize;

                                        if ($refs.input.disabled) {
                                            selectize.disable();
                                        }
                                    ">
                                    <option value="">-- Pilih Polda --</option>
                                    @foreach ($regionalPolices as $rp)
                                        <option value="{{ $rp->id }}">{{ $rp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            @php
                                $selectedPolda = $regionalPolices->firstWhere('id', $regionalPoliceId);
                            @endphp
                            <input type="text" value="{{ $selectedPolda?->name ?? '-' }}" readonly
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                        @endif
                        @error('regionalPoliceId')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <div class="md:col-span-1 border border-blue-100 bg-blue-50/50 p-4 rounded-xl">
                    <label class="flex items-center gap-2 text-sm font-semibold text-blue-900 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                        </svg>
                        Material Utama <span class="text-red-500">*</span>
                    </label>
                    <div wire:ignore wire:key="select-type-global-{{ rand() }}">
                        <select id="select-type-global" @disabled($receptionId) x-data x-ref="input" x-init="
                            const selectize = $($refs.input).selectize({
                                dropdownParent: 'body',
                                allowClear: true,
                                plugins: {{ $receptionId ? '[]' : "['clear_button']" }},
                                onChange: function(e) {
                                    @this.set('typeId', e ? e : '');
                                }
                            })[0].selectize;
                            if ($refs.input.disabled) {
                                selectize.disable();
                            }
                        " wire:model="typeId">
                            <option value="">-- Pilih Material --</option>
                            @foreach ($types as $typeOpt)
                                <option value="{{ $typeOpt->id }}"
                                    {{ $typeId == $typeOpt->id ? 'selected' : '' }}>
                                    {{ $typeOpt->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('typeId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>  
            </div>

            <!-- Accordion for Tim Komisi & Pejabat BAPPM -->
            <div x-data="{ openKomisi: false }" class="mt-6 border border-gray-200 rounded-xl overflow-hidden">
                <button type="button" @click="openKomisi = !openKomisi" class="flex w-full items-center justify-between bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Tim Komisi & Pejabat Penandatangan BAPPM (Klik untuk Ubah)
                    </span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform duration-200" :class="openKomisi ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="openKomisi" x-collapse class="p-4 bg-white border-t border-gray-100 space-y-6">
                    <!-- Anggota 1 -->
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3 text-sm pb-1 border-b border-gray-100">Ketua Tim Komisi (Anggota 1)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                                <input type="text" wire:model="commission_member_1_name" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Pangkat</label>
                                <input type="text" wire:model="commission_member_1_rank" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">NRP / NIP</label>
                                <input type="text" wire:model="commission_member_1_nip" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Jabatan</label>
                                <input type="text" wire:model="commission_member_1_position" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                        </div>
                    </div>

                    <!-- Anggota 2 -->
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3 text-sm pb-1 border-b border-gray-100">Anggota Tim Komisi (Anggota 2)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                                <input type="text" wire:model="commission_member_2_name" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Pangkat</label>
                                <input type="text" wire:model="commission_member_2_rank" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">NRP / NIP</label>
                                <input type="text" wire:model="commission_member_2_nip" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Jabatan</label>
                                <input type="text" wire:model="commission_member_2_position" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                        </div>
                    </div>

                    <!-- Anggota 3 -->
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3 text-sm pb-1 border-b border-gray-100">Anggota Tim Komisi (Anggota 3)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                                <input type="text" wire:model="commission_member_3_name" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Pangkat</label>
                                <input type="text" wire:model="commission_member_3_rank" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">NRP / NIP</label>
                                <input type="text" wire:model="commission_member_3_nip" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Jabatan</label>
                                <input type="text" wire:model="commission_member_3_position" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                            </div>
                        </div>
                    </div>

                    <!-- Kasi Fasmat & Ordonatur -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                        <div>
                            <h4 class="font-bold text-gray-900 mb-3 text-sm pb-1 border-b border-gray-100">KASI FASMAT SBST</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                                    <input type="text" wire:model="kasi_fasmat_name" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pangkat</label>
                                    <input type="text" wire:model="kasi_fasmat_rank" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">NRP / NIP</label>
                                    <input type="text" wire:model="kasi_fasmat_nip" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-bold text-gray-900 mb-3 text-sm pb-1 border-b border-gray-100">ORDONATUR (DIREKTUR LALU LINTAS)</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                                    <input type="text" wire:model="ordonatur_name" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pangkat</label>
                                    <input type="text" wire:model="ordonatur_rank" class="w-full px-3 py-1.5 text-xs rounded border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Items Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Detail Item Barang (Material Utama)</h2>
                @if (!$receptionId)
                    <button wire:click="addDetail" type="button"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                        </svg>
                        Tambah Item
                    </button>
                @endif
            </div>

            @if (count($details) > 0)
                <div class="overflow-x-auto overflow-y-visible pb-12">
                    <table class="w-full text-sm text-left align-top border-collapse min-w-[800px]">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                            <tr>
                                <th class="px-3 py-3 font-semibold w-12 text-center text-xs">No</th>
                                @if($is_type_detail)
                                    <th class="px-3 py-3 font-semibold text-xs min-w-[150px]">Detail Material</th>
                                @endif
                                <th class="px-3 py-3 font-semibold text-xs min-w-[150px]">Service</th>
                                <th class="px-3 py-3 font-semibold text-xs min-w-[150px]">Service Detail</th>
                                @if($is_with_serial_number)
                                    <th class="px-3 py-3 font-semibold text-xs min-w-[120px]">Kode Barang</th>
                                    <th class="px-3 py-3 font-semibold text-xs min-w-[120px]">SN 1</th>
                                    <th class="px-3 py-3 font-semibold text-xs min-w-[120px]">SN 2</th>
                                @endif
                                <th class="px-3 py-3 font-semibold text-xs w-24">Qty <span class="text-red-500">*</span></th>
                                @if (!$receptionId)
                                    <th class="px-3 py-3 font-semibold w-16 text-center text-xs">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($details as $index => $detail)
                                @php
                                    $rowTypeDetailId = $detail['type_detail_id'] ?? null;
                                    $rowServiceId = $detail['service_id'] ?? null;
                                    $filteredServices = collect($this->services)->where(
                                        $rowTypeDetailId ? 'type_detail_id' : 'type_id',
                                        $rowTypeDetailId ? $rowTypeDetailId : $typeId
                                    );
                                    $selectedSvc = collect($this->services)->firstWhere('id', $rowServiceId);
                                    $filteredServiceDetails = data_get($selectedSvc, 'details', []);
                                @endphp
                                <tr wire:key="detail-{{ $index }}" class="hover:bg-gray-50/20 transition-colors">
                                    <td class="px-3 py-3 text-center font-medium text-gray-500 align-top text-xs pt-5">
                                        {{ $index + 1 }}
                                    </td>

                                    @if($is_type_detail)
                                        <td class="px-3 py-3 align-top">
                                            <select wire:model.live="details.{{ $index }}.type_detail_id" @disabled($receptionId)
                                                class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-500">
                                                <option value="">-- Opsional --</option>
                                                @foreach($this->typeDetails as $td)
                                                    <option value="{{ data_get($td, 'id') }}">{{ data_get($td, 'name') }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endif
                                    
                                    <td class="px-3 py-3 align-top">
                                        <select wire:model.live="details.{{ $index }}.service_id" @disabled($receptionId)
                                            class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-500">
                                            <option value="">-- Pilih Service --</option>
                                            @foreach($filteredServices as $svc)
                                                <option value="{{ data_get($svc, 'id') }}">{{ data_get($svc, 'name') }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="px-3 py-3 align-top">
                                        <select wire:model.live="details.{{ $index }}.service_detail_id" @disabled($receptionId)
                                            class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-500">
                                            <option value="">-- Pilih Detail --</option>
                                            @foreach($filteredServiceDetails as $sdt)
                                                <option value="{{ data_get($sdt, 'id') }}">{{ data_get($sdt, 'name') }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    @if($is_with_serial_number)
                                        <td class="px-3 py-3 align-top">
                                            <input type="text" wire:model.blur="details.{{ $index }}.code" placeholder="Kode Barang" @disabled($receptionId) class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-400">
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <input type="text" wire:model.blur="details.{{ $index }}.number_serial_first" placeholder="SN 1" @disabled($receptionId) class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-400">
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <input type="text" wire:model.blur="details.{{ $index }}.number_serial_second" placeholder="SN 2" @disabled($receptionId) class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-400">
                                        </td>
                                    @endif

                                    <td class="px-3 py-3 align-top relative">
                                        <input type="number" min="0" step="0.01" wire:model.live="details.{{ $index }}.quantity" placeholder="Qty" @disabled($receptionId) class="w-full px-2 py-2 text-xs font-bold text-center rounded border border-purple-300 focus:border-purple-500 bg-purple-50 disabled:bg-gray-100 disabled:text-gray-400">
                                    </td>

                                    @if (!$receptionId)
                                        <td class="px-3 py-3 text-center align-top pt-4">
                                            @if (count($details) > 1)
                                                <button type="button" wire:click="removeDetail({{ $index }})"
                                                    class="p-1.5 inline-flex items-center justify-center rounded bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Hapus Item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">Belum ada item detail</p>
                    <p class="text-gray-400 text-sm mt-1">Klik "Tambah Item" untuk menambahkan detail barang</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Materiil Pendukung Card (Conditionally Rendered) -->
    @if (count($supportingMaterials) > 0)
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mt-6">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Materiil Pendukung
                </h2>
                <p class="text-sm text-gray-500 mb-6">Materiil pendukung untuk material utama yang dipilih. Input jumlah untuk menambahkan ke stock.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($supportingMaterials as $index => $sm)
                        <div class="p-4 border border-gray-100 rounded-xl bg-gray-50/50 flex flex-col justify-between" wire:key="support-{{ $index }}">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-bold text-gray-900 text-sm">{{ $sm['name'] }}</span>
                                <span class="text-xs px-2 py-0.5 bg-green-50 text-green-700 font-semibold rounded-full border border-green-200">Pendukung</span>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-1">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah (Qty)</label>
                                    <input type="number" min="0" step="0.01" wire:model.live="supportingMaterials.{{ $index }}.quantity" placeholder="0" @disabled($receptionId)
                                        class="w-full px-2 py-1.5 text-xs font-bold text-center rounded border border-green-300 focus:border-green-500 bg-white disabled:bg-gray-100 disabled:text-gray-400">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan / Catatan</label>
                                    <input type="text" wire:model.blur="supportingMaterials.{{ $index }}.description" placeholder="Catatan (opsional)" @disabled($receptionId)
                                        class="w-full px-2 py-1.5 text-xs rounded border border-gray-200 focus:border-green-500 bg-white disabled:bg-gray-100 disabled:text-gray-400">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="mt-6 flex items-center justify-end gap-3">
        <a href="{{ route('menu-polda.reception') }}" 
            class="px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">
            {{ $receptionId ? 'Kembali' : 'Batal' }}
        </a>
        @if (!$receptionId)
            <button wire:click="save" type="button"
                class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all duration-200">
                <span wire:loading.remove wire:target="save">Simpan Data</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        @endif
    </div>
</div>
