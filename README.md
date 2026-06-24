# 🌴 NusaFlow AI

**Sistem Prediksi Kepadatan Destinasi Wisata dan Rekomendasi Alternatif Berbasis AI**

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Flutter](https://img.shields.io/badge/Flutter-3.x-02569B?logo=flutter&logoColor=white)](https://flutter.dev)
[![FastAPI](https://img.shields.io/badge/FastAPI-Python-009688?logo=fastapi&logoColor=white)](https://fastapi.tiangolo.com)

---

## 📖 Deskripsi

NusaFlow AI adalah platform cerdas yang membantu wisatawan merencanakan kunjungan ke destinasi wisata di Indonesia dengan lebih efisien. Sistem ini memprediksi tingkat keramaian destinasi wisata berdasarkan data historis, event, cuaca, dan faktor lainnya, kemudian merekomendasikan destinasi alternatif yang lebih sepi jika destinasi yang dituju sedang ramai.

## 🎯 Tujuan Project

1. **Prediksi Keramaian** — Memberikan prediksi tingkat kepadatan pengunjung di destinasi wisata secara real-time dan forecasting.
2. **Rekomendasi Alternatif** — Menyarankan destinasi wisata alternatif terdekat yang sedang tidak ramai.
3. **Dashboard Admin** — Menyediakan dashboard bagi pengelola wisata untuk mengelola data destinasi, event, dan log pengunjung.
4. **Aplikasi Wisatawan** — Aplikasi mobile dan web yang mudah digunakan oleh wisatawan untuk mengecek keramaian dan mendapatkan rekomendasi.
5. **AI Service** — Layanan machine learning untuk meningkatkan akurasi prediksi secara bertahap.

## 📁 Struktur Folder

```txt
nusaflow-ai/
├── backend-laravel/      # Laravel API, database, autentikasi, dan dashboard admin
├── frontend_flutter/     # Aplikasi wisatawan (Flutter) untuk mobile dan web
├── ai-service/           # Service AI berbasis Python FastAPI untuk prediksi keramaian
├── docs/                 # Dokumentasi project (proposal, ERD, API, roadmap)
├── .gitignore            # Git ignore rules untuk mono-repo
└── README.md             # Dokumentasi utama project
```

## 🛠️ Teknologi yang Digunakan

| Komponen        | Teknologi                        | Versi / Keterangan                |
| --------------- | -------------------------------- | --------------------------------- |
| Backend API     | Laravel (PHP)                    | v13.x, PHP ^8.3                   |
| Admin Dashboard | Filament (Laravel)               | Direncanakan                      |
| Frontend Mobile | Flutter (Dart)                   | SDK ^3.10.1                       |
| AI Service      | FastAPI (Python)                 | Direncanakan                      |
| Database        | MySQL / PostgreSQL               | Dikelola via Laravel Migration    |
| ML Framework    | scikit-learn / TensorFlow        | Direncanakan                      |
| Version Control | Git + GitHub                     | Mono-repo                         |

## 🗺️ Rencana Pengembangan Bertahap

| Tahap | Deskripsi                               | Status         |
| ----- | --------------------------------------- | -------------- |
| 1     | Fondasi repo dan dokumentasi            | ✅ Selesai      |
| 2     | Database dan migration Laravel          | ✅ Selesai      |
| 3     | Dashboard admin Filament                | ✅ Selesai      |
| 4     | API destinasi dan prediksi rule-based   | ✅ Selesai      |
| 5     | Integrasi Flutter                       | ✅ Selesai      |
| 6     | AI service FastAPI                      | ✅ Selesai      |
| 7     | Machine learning                        | 🔲 Belum mulai |
| 8     | Deployment MVP                          | 🔲 Belum mulai |

> Detail lengkap ada di [`docs/development-roadmap.md`](docs/development-roadmap.md)

## 🚀 Cara Menjalankan

### Backend Laravel

```bash
# Masuk ke folder backend
cd backend-laravel

# Install dependencies PHP
composer install

# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migration database
php artisan migrate

# Install dependencies Node.js (untuk Vite)
npm install

# Jalankan development server
composer dev
# Atau jalankan secara manual:
# php artisan serve
```

Backend akan berjalan di `http://localhost:8000`.

### Frontend Flutter

```bash
# Masuk ke folder frontend
cd frontend_flutter

# Install dependencies Dart
flutter pub get

# Jalankan di mode debug (Chrome/Web)
flutter run -d chrome

# Atau jalankan di emulator/device
flutter run
```

### AI Service (FastAPI)

> ⚠️ AI service belum dikembangkan penuh. Berikut adalah panduan umum untuk menjalankannya nanti.

```bash
# Masuk ke folder AI service
cd ai-service

# Buat virtual environment
python -m venv .venv

# Aktivasi virtual environment
# Windows:
.venv\Scripts\activate
# macOS/Linux:
source .venv/bin/activate

# Install dependencies
pip install -r requirements.txt

# Jalankan server FastAPI
uvicorn main:app --reload --port 8001
```

AI service akan berjalan di `http://localhost:8001`.

## 📚 Dokumentasi

Dokumentasi lengkap tersedia di folder [`docs/`](docs/):

- [`docs/README.md`](docs/README.md) — Indeks dokumentasi
- [`docs/system-overview.md`](docs/system-overview.md) — Gambaran umum sistem
- [`docs/database-design.md`](docs/database-design.md) — Rancangan database
- [`docs/api-plan.md`](docs/api-plan.md) — Rencana endpoint API
- [`docs/development-roadmap.md`](docs/development-roadmap.md) — Roadmap pengembangan

## 📄 Lisensi

Project ini dikembangkan untuk keperluan akademik dan pengembangan pribadi.

---

**NusaFlow AI** — *Jelajahi Indonesia dengan lebih cerdas.* 🇮🇩
