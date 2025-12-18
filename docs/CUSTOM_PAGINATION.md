# Custom Pagination untuk Livewire

## Deskripsi
Custom pagination view yang menyediakan tampilan pagination yang lebih detail dengan informasi jumlah data dan navigasi halaman yang user-friendly.

## Fitur
- ✅ Informasi detail: "Menampilkan 1 sampai 10 dari 100 hasil"
- ✅ Navigasi halaman dengan nomor halaman
- ✅ Tombol Previous dan Next dengan icon
- ✅ Ellipsis (...) untuk range halaman yang panjang
- ✅ Responsive design (mobile-friendly)
- ✅ Loading state dengan `wire:loading.attr="disabled"`
- ✅ Styling modern dengan Tailwind CSS

## Cara Penggunaan

### Opsi 1: Menggunakan Trait (Recommended) ⭐

1. **Tambahkan trait ke Livewire component:**
```php
<?php

namespace App\Livewire\Admin\Stock\History;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\HasCustomPagination; // Import trait

class AdminStockHistoryIndex extends Component
{
    use WithPagination, HasCustomPagination; // Gunakan trait

    // ... rest of your code
}
```

2. **Di view Blade, tetap gunakan method `links()` seperti biasa:**
```blade
{{ $historyStocks->links() }}
```

### Opsi 2: Manual per Component

Tambahkan method `paginationView()` di component Anda:

```php
<?php

namespace App\Livewire\Admin\Stock\History;

use Livewire\Component;
use Livewire\WithPagination;

class AdminStockHistoryIndex extends Component
{
    use WithPagination;

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    // ... rest of your code
}
```

## File yang Dibuat

1. **Custom Pagination View:**
   - `/resources/views/vendor/livewire/custom-pagination.blade.php`

2. **Trait untuk otomatis menggunakan custom pagination:**
   - `/app/Traits/HasCustomPagination.php`

## Contoh Output

```
Menampilkan 1 sampai 10 dari 156 hasil        [<] 1 2 3 ... 16 [>]
```

## Customization

Untuk mengubah styling, edit file:
`/resources/views/vendor/livewire/custom-pagination.blade.php`

Anda bisa mengubah:
- Warna background
- Warna text
- Border
- Spacing
- Icon

## Migrasi dari Pagination Lama

### Sebelum:
```blade
<div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
    {{ $stocks->links() }}
</div>
```

### Sesudah (dengan trait):
1. Tambahkan `use App\Traits\HasCustomPagination;` di component
2. Tambahkan `, HasCustomPagination` di trait component
3. Tetap gunakan `{{ $stocks->links() }}` di view - tidak perlu ubah!

### Sesudah (tanpa trait):
```blade
<!-- Tidak perlu wrapper div, sudah ada di pagination view -->
{{ $stocks->links() }}
```

## Tips
- **Gunakan Trait** `HasCustomPagination` untuk kemudahan - tidak perlu menulis method `paginationView()` berulang kali
- Custom pagination ini kompatibel dengan semua fitur Livewire pagination (search, filter, per page, dll)
- Styling sudah responsive untuk mobile dan desktop
- Di Livewire v3, **tidak ada cara untuk set global pagination theme** - harus per component

## Daftar Component yang Menggunakan WithPagination

Berikut adalah component yang perlu ditambahkan trait `HasCustomPagination`:

```
✅ AdminStockHistoryIndex (sudah ditambahkan sebagai contoh)
⬜ AdminMasterPoliceStationIndex
⬜ AdminMasterUserTypeIndex
⬜ AdminMasterTypeDetailIndex
⬜ AdminMasterRackIndex
⬜ AdminMasterUserIndex
⬜ AdminMasterRegionalPoliceIndex
⬜ AdminMasterTypeIndex
⬜ AdminStockOpnameIndex
⬜ AdminReportReceptionIndex
⬜ AdminReportDeliveryIndex
⬜ AdminReportStockIndex
⬜ AdminReportStockOpnameIndex
⬜ AdminReportMaterialDamageIndex
⬜ AdminReportStockOutIndex
⬜ AdminReportMaterialUsageIndex
⬜ AdminReportStockInIndex
⬜ AdminReportMutationIndex
⬜ AdminStockPolresIndex
⬜ AdminReportReceptionRegionalPoliceIndex
⬜ AdminStockPoldaIndex
... dan banyak lagi
```

Untuk apply ke semua component, tambahkan trait satu per satu sesuai kebutuhan.
