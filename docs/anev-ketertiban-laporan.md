# Anev Ketertiban Laporan

Menu: **Laporan → Anev Ketertiban Laporan**, alamat `/report/anev`.

Akses hanya untuk role **Admin**, selain login dan verifikasi aplikasi. Menu sidebar disembunyikan untuk non-admin; akses URL langsung dan aksi Livewire/export juga ditolak. Admin melihat rekap seluruh wilayah aktif. Halaman hanya menampilkan rekap ketertiban, bukan rincian transaksi wilayah lain.

Pilih tanggal laporan dan jenis input (default: semua input operasional), cari nama wilayah, atau pilih status **Belum input** untuk melihat wilayah yang belum melapor pada tanggal tersebut.

## Perhitungan

Sumber adalah sembilan jenis dokumen operasional aktif yang memiliki rincian dan belum dihapus: pemakaian, stok awal, penerimaan, material rusak, subsidi, mutasi keluar, pengiriman, stok opname, dan penempatan rak. Pemakaian 0 tetap dihitung; rincian pemakaian harus aktif. Dokumen draft yang sudah tersimpan dan memiliki rincian ikut dihitung sebagai aktivitas input, bukan bukti transaksi final.

Unit berasal dari master Polda dan Polres aktif; Samsat mengikuti unit yang tercatat dalam master tersebut. Pemilik Polres didahulukan apabila dokumen juga mencantumkan Polda induknya. Pengiriman dan mutasi dikreditkan kepada pengirim, bukan penerima. Konfirmasi penerimaan tidak dihitung sebagai dokumen input terpisah. Master, pesan, target tahunan, stok hasil perhitungan, dan riwayat stok otomatis tidak masuk penilaian harian.

Filter jenis input menampilkan satu jenis; mode gabungan menjumlahkan dokumen dan memakai waktu input paling akhir (keterlambatan terbesar) di antara semua jenis. Kolom rincian menampilkan jumlah per jenis. Ketiadaan jenis transaksi insidental pada filter khusus diberi abu-abu “Tidak ada input”, bukan pelanggaran kewajiban laporan harian.

- Hijau: input terakhir pada hari yang sama dengan tanggal laporan.
- Kuning: input terakhir selisih 1–2 hari (asumsi untuk selisih 2 hari yang belum ditentukan).
- Merah: input terakhir selisih 3 hari atau lebih.
- Belum input: belum ada laporan pada tanggal terpilih; selisih dihitung sampai hari ini. Untuk hari ini berwarna abu-abu, selisih 1–2 hari kuning, dan mulai 3 hari merah.

Tanggal transaksi dibandingkan dengan waktu pembuatan input terakhir, dikonversi dari zona waktu aplikasi ke WIB. Perubahan transaksi tidak mengganti waktu input awalnya. Hari kalender termasuk akhir pekan/libur tetap dihitung. Penilaian tanggal lampau memakai data yang tersedia sekarang, termasuk laporan yang baru diinput kemudian; ini bukan snapshot historis keadaan database pada tanggal lampau.

Status ini menunjukkan keberadaan dan ketepatan waktu input, belum menilai kelengkapan semua material atau menetapkan peringkat ketertiban bulanan. Jika satu wilayah memiliki beberapa input dari jenis apa pun pada tanggal yang sama, waktu input terakhir menentukan warna agar input awal tidak menutupi input tambahan yang terlambat.

## Pemeriksaan

- 14 tes terarah lulus (44 assertions): batas warna, batas tengah malam WIB, wilayah belum input, filter, tanggal tidak valid/masa depan, serta route yang wajib login tanpa pembatasan admin.
- Build frontend dan kompilasi Blade berhasil.
- Pemeriksaan baca-saja pada database untuk 10 September 2026: 40 wilayah aktif, 23 sudah input, 17 belum input; query rekap sekitar 134 ms pada satu kali pemeriksaan. Tampilan berhasil dirender. Waktu ini bukan benchmark interaksi browser.
- Tidak ada perubahan data transaksi atau migrasi database.

## Perluasan semua input

- 18 tes terarah lulus, meliputi gabungan lintas sumber, keterlambatan terbesar, filter sumber dan sumber tidak valid.
- Query semua sumber serta render tampilan diperiksa secara baca-saja; mencakup 42 wilayah aktif (Polda dan Polres).
- Halaman URL eksternal tidak dapat dibuka oleh alat web; verifikasi dilakukan melalui kode, render Blade, dan database aplikasi. Belum ada uji interaksi browser setelah perluasan ini.

## Penyesuaian tampilan

Tampilan mengikuti template laporan penerimaan/pengiriman: judul biru ukuran 3xl, kartu ringkasan gradasi dengan ikon, filter dan tabel dalam satu kartu putih rounded-2xl, header tabel uppercase, efek hover baris, badge status, nomor urut, dan footer jumlah wilayah. Panduan perhitungan dapat dibuka melalui “Panduan penilaian Anev”. Layout filter dan kartu menyesuaikan lebar layar. Kompilasi Blade, render dengan data aplikasi, dan build frontend berhasil; kesamaan visual belum diverifikasi melalui screenshot browser.

## Export Excel dan akses Admin

Tombol **Export Excel** mengunduh berkas .xlsx sesuai filter tanggal, jenis input, pencarian wilayah, dan status saat ini. Kolom berisi nomor urut, tanggal laporan, jenis input, wilayah, status, selisih hari, input terakhir (WIB), jumlah input, dan rincian per jenis. Filter tidak valid ditolak. Nilai nol dipertahankan dan teks ditulis sebagai teks Excel.

Verifikasi perubahan akses/export: 27 tes lulus (81 assertions), termasuk penolakan role non-admin, filter export, validasi, dan pembacaan ulang berkas XLSX untuk memeriksa nilai nol serta teks literal. Build frontend dan kompilasi Blade berhasil. Pemeriksaan browser situs langsung belum dilakukan.
