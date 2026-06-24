# 📱 Integrasi Flutter (NusaFlow AI)

Dokumentasi ini menjelaskan integrasi antara aplikasi Flutter untuk wisatawan dengan backend API Laravel.

## Tujuan Integrasi
Aplikasi Flutter ini bertujuan untuk memberikan pengalaman mobile-first bagi wisatawan dalam:
- Menjelajahi destinasi wisata yang tersedia.
- Melihat detail destinasi beserta lokasi, kapasitas, dan harga tiket.
- Mengecek status keramaian secara real-time berdasarkan prediksi *rule-based* backend.
- Mendapatkan rekomendasi alternatif jika destinasi pilihan terlalu ramai.

## Struktur Utama Flutter

```
lib/
├── main.dart (Entry point)
├── app.dart (MaterialApp config)
├── config/
│   └── api_config.dart (Base URL API)
├── models/
│   ├── destination_model.dart
│   ├── crowd_status_model.dart
│   └── recommendation_model.dart
├── services/
│   └── api_service.dart (Pemanggilan HTTP ke Laravel)
├── screens/
│   ├── home_screen.dart
│   ├── destination_detail_screen.dart
│   └── recommendations_screen.dart
├── widgets/
│   ├── destination_card.dart
│   ├── crowd_status_badge.dart
│   ├── loading_view.dart
│   ├── error_view.dart
│   └── empty_view.dart
└── utils/
    └── formatters.dart
```

## Konfigurasi Base URL

Endpoint dikonfigurasi di `lib/config/api_config.dart`.
Gunakan URL yang sesuai dengan lingkungan pengembangan Anda:

- **Web (Chrome):** `http://127.0.0.1:8000/api`
- **Android Emulator:** `http://10.0.2.2:8000/api`
- **Real Device:** Gunakan IP lokal komputer (contoh: `http://192.168.1.5:8000/api`)

*Catatan: Saat ini, Base URL di-*hardcode* untuk keperluan Web lokal. Ubah baris ini saat menguji di emulator Android.*

## Endpoint yang Digunakan

1. `GET /api/destinations` -> Menampilkan daftar destinasi di `HomeScreen`.
2. `GET /api/destinations/{id}` -> Menampilkan detail destinasi di `DestinationDetailScreen`.
3. `GET /api/destinations/{id}/crowd-status` -> Mendapatkan status keramaian dan ditampilkan menggunakan `CrowdStatusBadge`.
4. `GET /api/destinations/{id}/recommendations` -> Mendapatkan destinasi alternatif di `RecommendationsScreen`.

## Cara Menjalankan

### 1. Jalankan Backend Laravel
Buka terminal baru, masuk ke folder `backend-laravel`:
```bash
cd backend-laravel
php artisan serve
```

### 2. Jalankan Aplikasi Flutter
Buka terminal baru, masuk ke folder `frontend_flutter`:
```bash
cd frontend_flutter
flutter pub get
flutter run -d chrome
```
*(Ganti `chrome` dengan ID emulator jika ingin menjalankan di emulator)*

## Penanganan State (Loading, Error, Empty)
Aplikasi telah dirancang dengan penanganan *state* yang rapi:
- `LoadingView`: Menampilkan indikator putar saat memanggil API.
- `ErrorView`: Menampilkan pesan kesalahan beserta tombol "Coba Lagi" jika API gagal (misalnya karena server mati).
- `EmptyView`: Menampilkan pesan ketika data list kosong (misalnya tidak ada rekomendasi alternatif).
