# Perbaikan input material — 14 September 2026

## Perubahan

- Tombol aksi Livewire mendapat status disabled saat permintaan berjalan. Pengunci JavaScript menangkap klik berulang sejak event pertama, membuka kembali setelah respons/validasi gagal, dan mempertahankan pengunci sampai navigasi setelah simpan berhasil. Tombol konfirmasi memakai mekanisme loading Livewire agar pembatalan dialog tidak mengunci halaman.
- Middleware web mengunci dan mengulang respons untuk permintaan tulis Livewire yang identik, terikat pengguna, sesi, snapshot formulir, perubahan field, dan pemanggilan aksi. Respons sukses disimpan 24 jam pada cache aplikasi; validasi gagal dan flash error tidak disimpan. Ini perlindungan retry berbasis cache, bukan batasan unik transaksi permanen. Cache produksi harus persisten, mendukung atomic lock, dan dibagi antar instance jika memakai beberapa server. Penghapusan cache menghapus rekam retry.
- Total rincian layanan bernilai 0 tidak lagi berubah menjadi string kosong. Item layanan dengan nilai 0 disimpan dan ditampilkan sebagai 0, bukan tanda kosong.
- Input stok awal Polda menerima saldo negatif sesuai validasi server yang sudah ada. Saldo stok awal Polres sudah mendukung negatif.
- Daftar stok Polres dan laporan stok menyertakan saldo 0/minus dalam tampilan dan penjumlahan.
- Perubahan layanan/subjenis/Polres mengatur ulang pilihan batch agar tidak memakai batch lama. Inisialisasi batch kosong menyertakan identitas subjenis dan layanan.
- Pemotongan stok pemakaian mengunci baris stok, memeriksa sisa stok saat transaksi berjalan, dan menggunakan decrement atomik untuk total stok.
- Observer dropdown tidak lagi mengamati perubahan style yang dibuatnya sendiri. Input kuantitas utama pemakaian/stok awal Polres dikirim bersama aksi simpan, mengurangi render saat mengetik.

Pemakaian tetap harus >= 0. Saldo awal boleh negatif. Pemakaian positif tetap tidak boleh melampaui persediaan.

## Hasil audit data (baca-saja)

Database diperiksa dengan transaksi PostgreSQL READ ONLY; tidak ada transaksi lama yang dihapus atau saldo yang dikoreksi.

| Unit | Baris stok | Rincian saldo minus | Selisih total stok vs rincian |
| --- | ---: | ---: | ---: |
| Probolinggo | 23 | 0 | 0 |
| Lumajang | 23 | 1 | 0 |
| Jember | 18 | 0 | 0 |
| Pacitan | 23 | 0 | 0 |
| Sumenep | 23 | 0 | 0 |
| Sidoarjo | 21 | 0 | 0 |
| Probolinggo Kota | 23 | 0 | 0 |

Kecocokan total dan rincian belum membuktikan kesesuaian dengan dokumen penginputan awal BPKB Jember. Pembandingan dengan dokumen sumber masih diperlukan.

Kandidat transaksi Sidoarjo dengan tanggal dan rincian material sama:

- `MU-20260907-0177` — dibuat 18:53:55
- `MU-20260907-0178` — dibuat 18:55:37
- `MU-20260907-0183` — dibuat 19:05:36
- `MU-20260907-0184` — dibuat 19:06:19

Semua bertanggal 7 September 2026. Waktu ditulis sebagaimana nilai database. Kandidat didasarkan pada batch, jenis/subjenis, kuantitas, dan jenis penggunaan; identitas rincian layanan dan dokumen operasional harus diperiksa sebelum menyatakan transaksi duplikat. Penghapusan harus melalui alur yang mengembalikan stok, bukan menghapus baris secara langsung.

## Validasi

- `php artisan test --compact tests/Unit`: 6 tes lulus (10 assertions).
- `node --test tests/js/action-guard.test.mjs`: 4 tes lulus; klik ganda, retry setelah error, pengunci selama redirect, dan helper refresh.
- `npm run build`: berhasil.
- `php artisan view:cache`: berhasil.
- Suite lengkap: 33 tes fitur terhambat `could not find driver` untuk SQLite; 1 tes unit bawaan lulus. Database operasional tidak digunakan sebagai database pengujian.
- Belum ada pengujian interaksi browser end-to-end atau pengukuran latensi menu sesudah perubahan.
