<div class="space-y-6" x-data="{
    values: $wire.entangle('quantities'),
    get filled() {
        return Object.values(this.values).filter(v => v !== '' && v !== null && v !== undefined).length;
    },
    sumKeys(keys) {
        if (!keys || !keys.length) return 0;
        let sum = 0;
        for (let i = 0; i < keys.length; i++) {
            const val = this.values[keys[i]];
            sum += (parseInt(val, 10) || 0);
        }
        return sum;
    },
    formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    },
    get grandTotal() {
        let sum = 0;
        const vals = Object.values(this.values);
        for (let i = 0; i < vals.length; i++) {
            sum += (parseInt(vals[i], 10) || 0);
        }
        return sum;
    }
}">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-blue-600">{{ $materialUsageId ? 'Edit' : 'Tambah' }} Penggunaan Material</h1>
            <p class="mt-1 text-sm text-gray-500">Laporan penggunaan material harian Polres (Format SBST / L-Mat Ditlantas Polda Jatim)</p>
        </div>
        <a href="{{ route('menu-polres.material-usage') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200">
            <span aria-hidden="true">←</span> Kembali
        </a>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid gap-3 sm:grid-cols-4">
        <div class="rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-600 to-cyan-500 p-5 text-white shadow-sm">
            <p class="text-xs font-semibold text-blue-100 uppercase tracking-wider">Item Layanan</p>
            <p class="mt-1 text-3xl font-bold">{{ count($rows) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kolom Terisi</p>
            <p class="mt-1 text-3xl font-bold text-green-600" x-text="filled"></p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Belum Diisi</p>
            <p class="mt-1 text-3xl font-bold text-amber-600" x-text="{{ count($rows) }} - filled"></p>
        </div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-5 shadow-sm">
            <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Total Fisik Terinput</p>
            <p class="mt-1 text-3xl font-extrabold text-blue-900 tabular-nums">
                <span x-text="formatNumber(grandTotal)">0</span>
                <span class="text-xs font-semibold text-blue-600">Unit</span>
            </p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        @if($errors->any())
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <strong>Data belum dapat disimpan.</strong> {{ $errors->first() }}
            </div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50">
            <div class="border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50/50 px-6 py-4">
                <h2 class="text-lg font-bold text-gray-900">Informasi Laporan</h2>
            </div>
            <div class="grid gap-6 p-6 sm:grid-cols-2">
                <div>
                    <label for="usage-date" class="mb-2 block text-sm font-semibold text-gray-700">Tanggal penggunaan <span class="text-red-500">*</span></label>
                    <input id="usage-date" type="date" wire:model="date" required max="{{ now()->toDateString() }}" class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <p class="mt-1 text-[11px] text-gray-400">Tanggal otomatis berganti tepat pada pukul 24.00 WIB tengah malam.</p>
                    @error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                @if(auth()->user()->hasRole('Admin'))
                    <div>
                        <label for="usage-station" class="mb-2 block text-sm font-semibold text-gray-700">Polres <span class="text-red-500">*</span></label>
                        <select id="usage-station" wire:model="policeStationId" required class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            <option value="">Pilih Polres</option>
                            @foreach($policeStations as $station)
                                <option value="{{ $station->id }}">{{ $station->name }}</option>
                            @endforeach
                        </select>
                        @error('policeStationId')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @else
                    <div>
                        <p class="mb-2 text-sm font-semibold text-gray-700">Polres</p>
                        <p class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ auth()->user()->policeStation?->name ?? 'Polres Anda' }}</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- Material Details Table with Grouping & Running Subtotals -->
        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50">
            <div class="border-b border-gray-100 px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Rincian Penggunaan Material</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Khusus SIM telah dikelompokkan: Kelompok Baru tersendiri dan Kelompok Perpanjangan tersendiri sesuai rekapan SBST / L-Mat.</p>
                </div>
                <div class="text-xs px-3 py-1.5 rounded-lg bg-blue-50 text-blue-800 font-semibold border border-blue-200">
                    Setiap kolom memiliki baris jumlah untuk verifikasi fisik
                </div>
            </div>
            <div class="border-b border-blue-100 bg-blue-50/60 px-6 py-2.5 text-xs text-blue-800">
                Seluruh kolom otomatis bernilai 0. Ubah hanya layanan yang digunakan. Cek baris "Jumlah" di bawah masing-masing kelompok untuk mencocokkan total dengan lembar rekapan bamat.
            </div>

            @php
                $sections = [];
                $rowNumber = 1;

                foreach ($rows as $key => $row) {
                    $typeId = $row['type_id'];
                    $typeName = $row['type_name'] ?? explode(' / ', $row['label'])[0];
                    $isSim = stripos($typeName, 'SIM') !== false;

                    if (!isset($sections[$typeId])) {
                        $sections[$typeId] = [
                            'type_id' => $typeId,
                            'name' => $typeName,
                            'is_sim' => $isSim,
                            'keys' => [],
                            'groups' => [
                                'baru' => [
                                    'title' => 'Penerbitan SIM Baru',
                                    'badge' => 'Kategori Baru',
                                    'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'header_bg' => 'bg-blue-50/80 text-blue-900 border-blue-200',
                                    'subtotal_bg' => 'bg-blue-50/90 font-bold border-y-2 border-blue-200 text-blue-900',
                                    'subtotal_pill' => 'bg-blue-600 text-white',
                                    'keys' => [],
                                    'items' => [],
                                ],
                                'perpanjangan' => [
                                    'title' => 'Penerbitan SIM Perpanjangan',
                                    'badge' => 'Kategori Perpanjangan',
                                    'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'header_bg' => 'bg-emerald-50/80 text-emerald-900 border-emerald-200',
                                    'subtotal_bg' => 'bg-emerald-50/90 font-bold border-y-2 border-emerald-200 text-emerald-900',
                                    'subtotal_pill' => 'bg-emerald-600 text-white',
                                    'keys' => [],
                                    'items' => [],
                                ],
                                'lainnya' => [
                                    'title' => 'Layanan SIM Lainnya',
                                    'badge' => 'Lainnya',
                                    'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'header_bg' => 'bg-amber-50/80 text-amber-900 border-amber-200',
                                    'subtotal_bg' => 'bg-amber-50/90 font-bold border-y-2 border-amber-200 text-amber-900',
                                    'subtotal_pill' => 'bg-amber-600 text-white',
                                    'keys' => [],
                                    'items' => [],
                                ],
                            ],
                            'items' => [],
                        ];
                    }

                    $sections[$typeId]['keys'][] = $key;

                    if ($isSim) {
                        $grp = $row['group'] ?? (stripos($row['label'], 'Perpanjangan') !== false ? 'perpanjangan' : (stripos($row['label'], 'Baru') !== false ? 'baru' : 'lainnya'));
                        if (!in_array($grp, ['baru', 'perpanjangan'])) {
                            $grp = 'lainnya';
                        }
                        $sections[$typeId]['groups'][$grp]['keys'][] = $key;
                        $sections[$typeId]['groups'][$grp]['items'][$key] = $row;
                    } else {
                        $sections[$typeId]['items'][$key] = $row;
                    }
                }
            @endphp

            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-left text-sm border-collapse">
                    <thead class="bg-gray-100 text-xs uppercase tracking-wide text-gray-600 border-b border-gray-200">
                        <tr>
                            <th class="w-14 px-5 py-3 text-center">No</th>
                            <th class="px-5 py-3">Material / Golongan Layanan</th>
                            <th class="w-24 px-4 py-3 text-center">Satuan</th>
                            <th class="w-52 px-5 py-3 text-right">Jumlah Penggunaan <span class="text-red-500">*</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sections as $typeId => $section)
                            @if($section['is_sim'])
                                {{-- SIM Master Header --}}
                                <tr class="bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 text-white shadow-xs">
                                    <th colspan="4" class="px-5 py-3 text-sm font-bold tracking-wider uppercase">
                                        <div class="flex items-center justify-between">
                                            <span class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                                </svg>
                                                Material: {{ $section['name'] }}
                                            </span>
                                            <span class="text-xs font-normal text-blue-200 bg-blue-900/60 px-3 py-1 rounded-full border border-blue-400/40">
                                                Format Rekapan Standar SBST / L-Mat
                                            </span>
                                        </div>
                                    </th>
                                </tr>

                                {{-- Iterate Groups: Baru, Perpanjangan, Lainnya --}}
                                @foreach($section['groups'] as $grpKey => $grp)
                                    @if(!empty($grp['items']))
                                        {{-- Sub Group Header --}}
                                        <tr class="{{ $grp['header_bg'] }} border-b border-gray-200">
                                            <th colspan="3" class="px-5 py-2.5 font-bold text-xs uppercase tracking-wider">
                                                <div class="flex items-center gap-2">
                                                    <span>{{ $grp['title'] }}</span>
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold border {{ $grp['badge_color'] }}">
                                                        {{ $grp['badge'] }}
                                                    </span>
                                                </div>
                                            </th>
                                            <th class="px-5 py-2.5 text-right text-xs uppercase tracking-wider text-gray-500">
                                                Jumlah (Unit)
                                            </th>
                                        </tr>

                                        {{-- Items --}}
                                        @foreach($grp['items'] as $key => $row)
                                            <tr wire:key="usage-{{ $key }}" class="transition-colors hover:bg-blue-50/40">
                                                <td class="px-5 py-2.5 text-center text-xs text-gray-400 font-medium">{{ $rowNumber++ }}</td>
                                                <td class="px-5 py-2.5 font-medium text-gray-800">
                                                    <label for="quantity-{{ $key }}" class="cursor-pointer">
                                                        {{ $row['label'] }}
                                                    </label>
                                                </td>
                                                <td class="px-4 py-2.5 text-center text-xs text-gray-500 font-medium">{{ $row['unit'] }}</td>
                                                <td class="px-5 py-2.5 text-right">
                                                    <input id="quantity-{{ $key }}" type="number" inputmode="numeric" min="0" max="999999999" step="1" required
                                                        x-model="values['{{ $key }}']"
                                                        class="w-full rounded-lg border px-3 py-1.5 text-right text-sm font-semibold tabular-nums focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 {{ $errors->has('quantities.'.$key) ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                                    @error('quantities.'.$key)<p role="alert" class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- Subtotal Row for this SIM Group --}}
                                        <tr class="{{ $grp['subtotal_bg'] }}">
                                            <td colspan="3" class="px-5 py-3 text-right text-xs uppercase tracking-wider">
                                                Jumlah {{ $grp['title'] }}:
                                            </td>
                                            <td class="px-5 py-3 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <span class="text-xs font-normal text-gray-500">Subtotal:</span>
                                                    <span class="inline-block px-3 py-1 rounded-lg {{ $grp['subtotal_pill'] }} text-xs font-black tabular-nums shadow-xs">
                                                        <span x-text="formatNumber(sumKeys(@js($grp['keys'])))">0</span>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                {{-- Total Keseluruhan SIM --}}
                                <tr class="bg-slate-800 text-white font-bold border-b-2 border-slate-900">
                                    <td colspan="3" class="px-5 py-3 text-right text-xs uppercase tracking-wider text-slate-200">
                                        Total Penggunaan Material {{ $section['name'] }} (Baru + Perpanjangan):
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="text-xs font-normal text-slate-300">Total SIM:</span>
                                            <span class="inline-block px-3 py-1 rounded-lg bg-white text-slate-900 text-xs font-black tabular-nums shadow-sm">
                                                <span x-text="formatNumber(sumKeys(@js($section['keys'])))">0</span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                            @else
                                {{-- Non-SIM Material (STNK, BPKB, TNKB, etc.) --}}
                                <tr class="bg-gradient-to-r from-slate-100 to-gray-200 text-gray-800 border-b border-gray-300">
                                    <th colspan="3" class="px-5 py-3 text-xs font-bold tracking-wider uppercase text-gray-800">
                                        Material: {{ $section['name'] }}
                                    </th>
                                    <th class="px-5 py-3 text-right text-xs uppercase tracking-wider text-gray-500">
                                        Jumlah (Unit)
                                    </th>
                                </tr>

                                @foreach($section['items'] as $key => $row)
                                    <tr wire:key="usage-{{ $key }}" class="transition-colors hover:bg-gray-50/70">
                                        <td class="px-5 py-2.5 text-center text-xs text-gray-400 font-medium">{{ $rowNumber++ }}</td>
                                        <td class="px-5 py-2.5 font-medium text-gray-800">
                                            <label for="quantity-{{ $key }}" class="cursor-pointer">
                                                {{ $row['label'] }}
                                            </label>
                                        </td>
                                        <td class="px-4 py-2.5 text-center text-xs text-gray-500 font-medium">{{ $row['unit'] }}</td>
                                        <td class="px-5 py-2.5 text-right">
                                            <input id="quantity-{{ $key }}" type="number" inputmode="numeric" min="0" max="999999999" step="1" required
                                                x-model="values['{{ $key }}']"
                                                class="w-full rounded-lg border px-3 py-1.5 text-right text-sm font-semibold tabular-nums focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 {{ $errors->has('quantities.'.$key) ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                            @error('quantities.'.$key)<p role="alert" class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Subtotal Row for Non-SIM Material --}}
                                <tr class="bg-gray-100 font-bold border-y-2 border-gray-300 text-gray-900">
                                    <td colspan="3" class="px-5 py-3 text-right text-xs uppercase tracking-wider">
                                        Jumlah Penggunaan {{ $section['name'] }}:
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="text-xs font-normal text-gray-500">Subtotal:</span>
                                            <span class="inline-block px-3 py-1 rounded-lg bg-gray-800 text-white text-xs font-black tabular-nums shadow-xs">
                                                <span x-text="formatNumber(sumKeys(@js($section['keys'])))">0</span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>

                    {{-- Grand Total Footer --}}
                    <tfoot class="border-t-4 border-blue-600 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white font-bold">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-xs uppercase tracking-wider text-blue-100">
                                TOTAL KESELURUHAN PENGGUNAAN MATERIAL:
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="text-xs font-normal text-blue-200">Grand Total:</span>
                                    <span class="inline-block px-4 py-1.5 rounded-xl bg-white text-blue-900 text-base font-extrabold tabular-nums shadow-md">
                                        <span x-text="formatNumber(grandTotal)">0</span>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <label for="usage-note" class="mb-2 block text-sm font-semibold text-gray-700">Catatan <span class="font-normal text-gray-400">(opsional)</span></label>
            <textarea id="usage-note" wire:model="description" rows="3" maxlength="1000" placeholder="Tambahkan keterangan laporan jika diperlukan…" class="block w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"></textarea>
        </section>

        <!-- Sticky Bottom Bar with Real-time Count & Submit -->
        <div class="sticky bottom-0 z-20 flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white/95 backdrop-blur-md p-4 shadow-xl sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4 text-sm text-gray-700">
                <div>
                    <span class="text-xs text-gray-500 uppercase font-semibold">Total Unit Terinput:</span>
                    <div class="text-xl font-black text-blue-600 tabular-nums">
                        <span x-text="formatNumber(grandTotal)">0</span> <span class="text-xs font-semibold text-gray-500">Unit</span>
                    </div>
                </div>
                <div class="h-8 border-r border-gray-200"></div>
                <div>
                    <span class="text-xs text-gray-500 uppercase font-semibold">Kelengkapan Input:</span>
                    <div class="text-sm font-bold text-gray-800">
                        <span x-text="filled"></span> dari {{ count($rows) }} baris terisi
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('menu-polres.material-usage') }}" class="rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 disabled:opacity-50 transition-all hover:scale-105">
                    <span wire:loading.remove wire:target="save">Simpan Penggunaan</span>
                    <span wire:loading wire:target="save">Menyimpan…</span>
                </button>
            </div>
        </div>
    </form>
</div>
