# Flutter App Feature Update

Dokumen ini merangkum perubahan fitur aplikasi wisatawan pada `frontend_flutter`.

## Tujuan

Membuat aplikasi wisatawan lebih masuk akal untuk MVP dengan navigasi yang lebih lengkap, fitur eksplorasi, prediksi, favorit, dan rencana perjalanan tanpa menambah endpoint backend baru.

## File Baru

- `frontend_flutter/lib/screens/main_navigation_screen.dart`
  - Shell navigasi utama.
  - Menampilkan bottom navigation di mobile dan navigation rail di layar lebar.
  - Menu: Jelajah, Prediksi, Rencana, Favorit, Info.

- `frontend_flutter/lib/screens/crowd_planner_screen.dart`
  - Menu Prediksi.
  - Pengguna memilih destinasi, tanggal, dan jam.
  - Mengambil hasil prediksi dari endpoint Laravel `crowd-status`.

- `frontend_flutter/lib/screens/trip_plan_screen.dart`
  - Menu Rencana.
  - Menyimpan daftar destinasi yang ingin dikunjungi selama aplikasi berjalan.
  - Mendukung hapus, kosongkan rencana, dan ubah urutan destinasi.

- `frontend_flutter/lib/screens/favorites_screen.dart`
  - Menu Favorit.
  - Menampilkan destinasi yang ditandai favorit dari menu Jelajah/detail.

- `frontend_flutter/lib/screens/profile_screen.dart`
  - Menu Info.
  - Menampilkan ringkasan aplikasi, jumlah favorit, jumlah rencana, dan API base URL aktif.

## File Diubah

- `frontend_flutter/lib/app.dart`
  - Home aplikasi diarahkan ke `MainNavigationScreen`.

- `frontend_flutter/lib/screens/home_screen.dart`
  - Menambahkan pencarian destinasi.
  - Menambahkan filter kategori dan tingkat keramaian.
  - Menambahkan action favorit dan tambah ke rencana.

- `frontend_flutter/lib/screens/destination_detail_screen.dart`
  - Menambahkan action favorit di app bar.
  - Menyambungkan detail dan rekomendasi ke state favorit/rencana dari navigasi utama.

- `frontend_flutter/lib/screens/recommendations_screen.dart`
  - Rekomendasi alternatif tetap dapat membuka detail dengan callback favorit/rencana.

- `frontend_flutter/lib/widgets/destination_card.dart`
  - Card destinasi sekarang mendukung tombol favorit dan tambah ke rencana.

- `frontend_flutter/lib/services/api_service.dart`
  - Merapikan query parameter agar `flutter analyze` bersih.

## Catatan MVP

Favorit dan rencana perjalanan masih tersimpan sebagai state lokal aplikasi. Data akan hilang ketika aplikasi direstart. Untuk tahap berikutnya, fitur ini bisa dipindahkan ke backend dengan tabel favorit dan itinerary per user.

## Verifikasi

Perintah yang sudah dijalankan:

```bash
flutter analyze
flutter test
flutter build web --release --dart-define=API_BASE_URL=http://127.0.0.1:8000/api
```

Hasil:

- `flutter analyze`: no issues found
- `flutter test`: all tests passed
- `flutter build web`: build berhasil
