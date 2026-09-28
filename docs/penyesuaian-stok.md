# Penyesuaian Stok Polda dan Polres

Menu tersedia di:

- Menu Polda → Penyesuaian Stok (`/menu-polda/stock-adjustment`).
- Menu Polres → Penyesuaian Stok (`/menu-polres/stock-adjustment`).

Pengguna Polda/Polres hanya dapat memuat dan mengubah stok wilayahnya sendiri. Admin dapat memilih wilayah pada menu terkait. Batas pemilik diperiksa ulang pada server saat membaca maupun menyimpan stok, bukan hanya melalui dropdown.

## Cara memakai

1. Pilih wilayah jika menggunakan akun Admin.
2. Pilih material/batch. Label memuat jenis, layanan, kode, nomor seri, rak, dan saldo.
3. Masukkan saldo akhir yang benar. Ini bukan jumlah tambahan/pengurangan. Saldo 0 dan negatif diterima, maksimal dua angka desimal.
4. Isi alasan penyesuaian minimal 5 karakter.
5. Klik Simpan Penyesuaian. Stok detail dan total stok langsung diperbarui sebesar selisihnya.

Material yang belum memiliki stok aktif perlu ditambahkan melalui stok awal/penerimaan. Saldo akhir yang sama dengan saldo saat ini ditolak karena tidak ada perubahan. Jika stok telah berubah sejak dipilih, formulir ditolak; kosongkan pilihan lalu pilih kembali batch untuk mengambil saldo terbaru.

## Riwayat dan pengamanan

Dokumen penyesuaian memakai tabel stok opname yang sudah ada, dengan kode `ADJ-…`, status approved, serta pencatat dan penanggung jawab dari pengguna yang menyimpan. Ini koreksi saldo langsung, tidak melalui tahap persetujuan kedua. Alasan, saldo sebelum/sesudah, selisih, material/batch, dan waktu disimpan. Dokumen approved tidak mengikuti alur edit/hapus draft.

Riwayat stok menerima catatan masuk/keluar sebesar selisih. Halaman menampilkan 20 penyesuaian terbaru; dokumen lebih lama tetap tersimpan di stok opname dan riwayat. Anev yang membaca stok opname ikut menghitung penyesuaian ini dalam kategori tersebut.

Penyimpanan memakai transaksi database dan lock baris. Token formulir yang sama mengembalikan dokumen sebelumnya tanpa mengubah stok lagi. Saldo awal yang dibaca dan token dilindungi dengan properti Livewire Locked.

## Pemeriksaan

- 19 pemeriksaan integrasi PostgreSQL lulus: koreksi ke 0, negatif, positif, simpan ulang, saldo basi, pemilik berbeda, tanpa perubahan, audit petugas, serta render formulir/riwayat untuk Polda dan Polres.
- Pengujian menggunakan tabel PostgreSQL TEMP yang menutupi nama tabel stok hanya pada koneksi pengujian, dan transaksi dibatalkan pada akhir. Tidak ada saldo operasional yang diubah.
- 24 tes unit aplikasi lulus (65 assertions).
- Kedua route terdaftar; kompilasi Blade, PHP lint, dan build frontend berhasil.
- Belum diuji melalui interaksi browser langsung.
- Tidak memerlukan migrasi database baru.

Pengujian integrasi: `php tests/Integration/stock-adjustment.php` (PostgreSQL, hak membuat temporary table).

## Tampilan formulir

Kedua halaman memakai template bersama yang mengikuti halaman detail penerimaan: header dan tombol Kembali, kartu Informasi Utama, kartu Detail Penyesuaian, indikator field wajib, tampilan input/fokus, serta tombol Batal dan Simpan di bawah formulir. Tabel riwayat mengikuti gaya menu rak, termasuk header, hover, dan keadaan kosong. Navigasi Kembali/Batal menuju stok sesuai lingkup Polda/Polres. Setelah perubahan tampilan, 19 pemeriksaan integrasi termasuk render kedua lingkup kembali lulus; kompilasi Blade dan build frontend berhasil. Verifikasi visual browser langsung belum dilakukan.
