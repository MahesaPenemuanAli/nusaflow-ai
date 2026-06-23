# Dashboard Admin NusaFlow AI

Dokumentasi ini menjelaskan penggunaan dan struktur dari Dashboard Admin Filament yang dibangun untuk mengelola data inti aplikasi NusaFlow AI.

## Tujuan Dashboard
Dashboard ini difungsikan khusus bagi pengelola pariwisata (Admin) untuk memantau aktivitas destinasi, mengelola data referensi seperti kategori dan daftar destinasi, serta menginput data penting seperti event dan catatan pengunjung (visitor logs). 

Akses ke dashboard dibatasi hanya untuk pengguna dengan role `admin` atau `super_admin`.

## Akun Admin Development
Secara default, saat sistem di-seed, sebuah akun admin telah disediakan untuk keperluan pengembangan:
- **Email:** `admin@nusaflow.test`
- **Password:** `password`
- **Role:** `super_admin`

## Cara Mengakses Dashboard
1. Pastikan server lokal berjalan: `php artisan serve`
2. Buka browser dan arahkan ke: `http://localhost:8000/admin`
3. Gunakan kredensial di atas untuk login.

## Menu Dashboard & Resource

Navigasi dashboard dikelompokkan menjadi dua bagian utama untuk kemudahan manajemen operasional:

### Master Data
Kelompok ini berisi data referensi yang relatif statis atau jarang berubah namun esensial.
- **Destination Categories:** Mengelola kategori destinasi wisata (contoh: Pantai, Pegunungan, Museum).
- **Destinations:** Mengelola detail destinasi termasuk lokasi (latitude/longitude), kapasitas maksimal, harga tiket, dan status aktif.

### Tourism Operations
Kelompok ini berisi data operasional harian yang dinamis dan terus bertambah.
- **Events:** Mengelola acara atau kegiatan yang akan diselenggarakan di sebuah destinasi. Admin dapat menetapkan ekspektasi dampak keramaian (low, medium, high).
- **Visitor Logs:** Mencatat jumlah kunjungan riil per destinasi berdasarkan tanggal dan jam tertentu. Dilengkapi dengan filter sumber data (manual input, QR, dsb.) dan cuaca.

## Widgets
Halaman awal (Dashboard) menyediakan ringkasan singkat berupa statistik:
- Total destinasi terdaftar
- Jumlah destinasi yang sedang aktif
- Total kategori yang tersedia
- Jumlah event terdaftar
- Total catatan log pengunjung
- Jumlah pengunjung hari ini
- Grafik garis (Line chart) tren pengunjung dalam 7 hari terakhir.
