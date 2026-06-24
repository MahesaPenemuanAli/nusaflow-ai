# 🗺️ Development Roadmap — NusaFlow AI

## Gambaran Umum

Pengembangan NusaFlow AI dibagi menjadi 8 tahap bertahap, dari fondasi hingga deployment MVP. Setiap tahap memiliki deliverable yang jelas dan dapat di-review sebelum melanjutkan ke tahap berikutnya.

---

## Tahap 1: Fondasi Repo dan Dokumentasi ✅

**Status**: Selesai

**Tujuan**: Merapikan struktur repository dan menyiapkan dokumentasi dasar.

**Deliverable**:
- [x] Struktur folder mono-repo (`backend-laravel/`, `frontend_flutter/`, `ai-service/`, `docs/`)
- [x] `README.md` root dengan penjelasan lengkap project
- [x] `.gitignore` untuk mono-repo (Laravel + Flutter + Python)
- [x] Dokumentasi: system overview, database design, API plan, development roadmap

---

## Tahap 2: Database dan Migration Laravel ✅

**Status**: Selesai

**Tujuan**: Membuat migration, model, dan seeder untuk semua tabel database.

**Deliverable**:
- [x] Migration untuk 10 tabel: `users` (extension), `destination_categories`, `destinations`, `visitor_logs`, `events`, `reviews`, `crowd_predictions`, `checkins`, `itineraries`, `itinerary_items`
- [x] Eloquent Model dengan relasi yang tepat (9 model baru + update User)
- [x] Factory untuk `User`, `DestinationCategory`, `Destination`, `VisitorLog`, `Review`
- [x] Seeder: `DestinationCategorySeeder`, `DestinationSeeder`, `VisitorLogSeeder`, `EventSeeder`, `CrowdPredictionSeeder`
- [x] `php artisan migrate:fresh --seed` berhasil tanpa error
- [x] `php artisan test` berhasil (2 tests passed)

---

## Tahap 3: Dashboard Admin Filament ✅

**Status**: Selesai

**Tujuan**: Membuat dashboard admin menggunakan Filament untuk mengelola data destinasi.

**Deliverable**:
- [x] Install dan konfigurasi Filament
- [x] Resource: DestinationCategory, Destination, Event, VisitorLog
- [x] Widget dashboard: statistik destinasi, grafik pengunjung
- [x] User management (admin)

---

## Tahap 4: API Destinasi dan Prediksi Rule-based ✅

**Status**: Selesai

**Tujuan**: Membuat REST API untuk destinasi dan prediksi keramaian sederhana.

**Deliverable**:
- [x] `GET /api/destinations` — Daftar destinasi dengan filter dan pagination
- [x] `GET /api/destinations/{id}` — Detail destinasi
- [x] `GET /api/destinations/{id}/crowd-status` — Status keramaian (rule-based)
- [x] `GET /api/destinations/{id}/recommendations` — Rekomendasi alternatif
- [x] Admin input log pengunjung dan event via Filament Dashboard
- [x] Logika prediksi rule-based (`CrowdPredictionService`) berdasarkan: visitor_logs, weekend, event impact
- [x] API testing (7 test, 55 assertions) dan dokumentasi endpoint

---

## Tahap 5: Integrasi Flutter 🔲

**Status**: Belum mulai

**Tujuan**: Menghubungkan aplikasi Flutter dengan API Laravel.

**Deliverable**:
- [ ] Setup HTTP client (Dio/http package)
- [ ] Halaman daftar destinasi
- [ ] Halaman detail destinasi dengan status keramaian
- [ ] Halaman rekomendasi alternatif
- [ ] UI/UX yang responsif dan menarik
- [ ] State management (Provider/Riverpod/Bloc)

---

## Tahap 6: AI Service FastAPI 🔲

**Status**: Belum mulai

**Tujuan**: Membangun service AI terpisah menggunakan FastAPI.

**Deliverable**:
- [ ] Setup project FastAPI di `ai-service/`
- [ ] Endpoint prediksi keramaian
- [ ] Koneksi ke database untuk mengambil data historis
- [ ] Logika prediksi yang lebih canggih dari rule-based
- [ ] Integrasi dengan Laravel backend via HTTP
- [ ] Dokumentasi API (Swagger/OpenAPI otomatis dari FastAPI)

---

## Tahap 7: Machine Learning 🔲

**Status**: Belum mulai

**Tujuan**: Mengembangkan model ML untuk prediksi yang lebih akurat.

**Deliverable**:
- [ ] Pengumpulan dan preprocessing data historis
- [ ] Eksplorasi data (EDA) dan feature engineering
- [ ] Training model (scikit-learn / TensorFlow)
- [ ] Evaluasi model (MAE, RMSE, accuracy)
- [ ] Integrasi model ke FastAPI service
- [ ] A/B testing: rule-based vs ML
- [ ] Versioning model

---

## Tahap 8: Deployment MVP 🔲

**Status**: Belum mulai

**Tujuan**: Deploy minimum viable product ke environment produksi.

**Deliverable**:
- [ ] Setup environment produksi (VPS/Cloud)
- [ ] Deploy Laravel backend
- [ ] Deploy FastAPI AI service
- [ ] Build dan deploy Flutter web
- [ ] Build APK Flutter untuk Android
- [ ] SSL/HTTPS configuration
- [ ] Monitoring dan logging dasar
- [ ] User acceptance testing (UAT)

---

## Timeline Estimasi

| Tahap | Durasi Estimasi | Kumulatif |
|-------|-----------------|-----------|
| 1 | 1 hari | 1 hari |
| 2 | 3-5 hari | 1 minggu |
| 3 | 5-7 hari | 2 minggu |
| 4 | 5-7 hari | 3 minggu |
| 5 | 7-10 hari | 4-5 minggu |
| 6 | 5-7 hari | 5-6 minggu |
| 7 | 7-14 hari | 7-8 minggu |
| 8 | 3-5 hari | 8-9 minggu |

> Timeline bersifat estimasi dan dapat berubah sesuai kondisi pengembangan.

---

*Terakhir diperbarui: Juni 2026*
