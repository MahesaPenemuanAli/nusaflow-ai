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

## Tahap 2: Database dan Migration Laravel 🔲

**Status**: Belum mulai

**Tujuan**: Membuat migration, model, dan seeder untuk semua tabel database.

**Deliverable**:
- [ ] Migration untuk 10 tabel: `users`, `destination_categories`, `destinations`, `visitor_logs`, `events`, `reviews`, `crowd_predictions`, `checkins`, `itineraries`, `itinerary_items`
- [ ] Eloquent Model dengan relasi yang tepat
- [ ] Factory dan Seeder untuk data dummy
- [ ] Menjalankan `php artisan migrate` berhasil tanpa error

---

## Tahap 3: Dashboard Admin Filament 🔲

**Status**: Belum mulai

**Tujuan**: Membuat dashboard admin menggunakan Filament untuk mengelola data destinasi.

**Deliverable**:
- [ ] Install dan konfigurasi Filament
- [ ] Resource: DestinationCategory, Destination, Event, VisitorLog
- [ ] Widget dashboard: statistik destinasi, grafik pengunjung
- [ ] User management (admin)

---

## Tahap 4: API Destinasi dan Prediksi Rule-based 🔲

**Status**: Belum mulai

**Tujuan**: Membuat REST API untuk destinasi dan prediksi keramaian sederhana.

**Deliverable**:
- [ ] `GET /api/destinations` — Daftar destinasi dengan filter dan pagination
- [ ] `GET /api/destinations/{id}` — Detail destinasi
- [ ] `GET /api/destinations/{id}/crowd-status` — Status keramaian (rule-based)
- [ ] `GET /api/destinations/{id}/recommendations` — Rekomendasi alternatif
- [ ] `POST /api/admin/visitor-logs` — Input log pengunjung
- [ ] `POST /api/admin/events` — Input event
- [ ] Logika prediksi rule-based berdasarkan: rata-rata visitor_logs, hari (weekday/weekend), ada tidaknya event
- [ ] API testing dan dokumentasi endpoint

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
