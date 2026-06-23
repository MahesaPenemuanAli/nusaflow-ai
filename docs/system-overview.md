# 🏗️ System Overview — NusaFlow AI

## Gambaran Umum

NusaFlow AI adalah sistem prediksi kepadatan destinasi wisata dan rekomendasi destinasi alternatif. Sistem ini terdiri dari tiga komponen utama yang saling terhubung melalui API.

## Arsitektur Sistem

```
┌─────────────────────┐     ┌─────────────────────┐
│   Flutter App        │────▶│   Laravel Backend    │
│   (Mobile & Web)     │◀────│   (REST API)         │
│                     │     │                     │
│  - Peta destinasi   │     │  - Autentikasi      │
│  - Prediksi crowd   │     │  - CRUD destinasi   │
│  - Rekomendasi      │     │  - Dashboard admin  │
│  - Itinerary        │     │  - API gateway      │
└─────────────────────┘     └──────────┬──────────┘
                                       │
                                       │ HTTP Request
                                       ▼
                            ┌─────────────────────┐
                            │   AI Service         │
                            │   (FastAPI + ML)     │
                            │                     │
                            │  - Prediksi crowd   │
                            │  - Rekomendasi AI   │
                            │  - Model training   │
                            └─────────────────────┘
```

## Komponen Utama

### 1. Backend Laravel (`backend-laravel/`)

Backend utama yang menangani:

- **Autentikasi** — Login dan registrasi user (wisatawan dan admin).
- **Manajemen Data** — CRUD destinasi wisata, kategori, event, dan log pengunjung.
- **Dashboard Admin** — Panel admin berbasis Filament untuk pengelolaan data.
- **API Gateway** — Menyediakan REST API untuk frontend Flutter dan meneruskan request prediksi ke AI service.
- **Database** — Mengelola semua data menggunakan MySQL/PostgreSQL via Eloquent ORM.

### 2. Frontend Flutter (`frontend_flutter/`)

Aplikasi wisatawan yang tersedia untuk mobile (Android/iOS) dan web:

- **Peta Destinasi** — Menampilkan peta interaktif destinasi wisata.
- **Status Keramaian** — Menampilkan prediksi tingkat kepadatan pengunjung.
- **Rekomendasi** — Menyarankan destinasi alternatif berdasarkan preferensi dan keramaian.
- **Itinerary** — Membuat rencana perjalanan berdasarkan rekomendasi.

### 3. AI Service (`ai-service/`)

Layanan prediksi berbasis Python FastAPI:

- **Prediksi Rule-based** — Tahap awal menggunakan aturan sederhana berdasarkan data historis.
- **Machine Learning** — Tahap lanjutan menggunakan model ML untuk prediksi yang lebih akurat.
- **Rekomendasi** — Algoritma rekomendasi destinasi alternatif berdasarkan lokasi, kategori, dan keramaian.

## Alur Data Utama

1. **Admin** menginput data destinasi, event, dan log pengunjung melalui dashboard Filament.
2. **Laravel** menyimpan data ke database dan menyediakan API.
3. **Flutter App** memanggil API Laravel untuk mendapatkan data destinasi.
4. **Laravel** meneruskan permintaan prediksi ke **AI Service**.
5. **AI Service** memproses data dan mengembalikan hasil prediksi.
6. **Flutter App** menampilkan hasil prediksi dan rekomendasi kepada wisatawan.

## Teknologi Stack

| Layer       | Teknologi                    |
| ----------- | ---------------------------- |
| Frontend    | Flutter (Dart), SDK ^3.10.1  |
| Backend     | Laravel 13, PHP ^8.3         |
| Admin Panel | Filament v5.x                |
| AI Service  | FastAPI, Python 3.10+        |
| Database    | MySQL / PostgreSQL           |
| ML          | scikit-learn / TensorFlow    |

---

*Terakhir diperbarui: Juni 2026*
