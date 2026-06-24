# 🤖 AI Service (FastAPI)

Dokumentasi ini menjelaskan peran dan spesifikasi AI service dalam arsitektur NusaFlow AI.

## Tujuan AI Service
AI Service adalah layanan backend terpisah (microservice) berbasis Python FastAPI yang bertanggung jawab menangani:
1. **Prediksi Kepadatan (Crowd Prediction)**: Menganalisis data pengunjung untuk memprediksi seberapa ramai suatu destinasi di hari dan jam tertentu.
2. **Rekomendasi Destinasi**: Memberikan saran destinasi wisata alternatif berdasarkan kategori yang disukai, jarak, dan tingkat keramaian terkini.

Service ini dipisah dari backend Laravel untuk:
- Memudahkan integrasi library machine learning (seperti Scikit-Learn, TensorFlow, atau PyTorch).
- Meringankan beban pemrosesan pada backend utama.
- Memungkinkan *scaling* independen.

## Endpoint AI Service

Service berjalan pada port `8001` (default: `http://127.0.0.1:8001`).

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/` | Mengecek status root |
| GET | `/health` | Health check service |
| POST | `/predict-crowd` | Menghitung prediksi keramaian |
| POST | `/recommend-destinations` | Mengkalkulasi daftar rekomendasi alternatif |

Dokumentasi interaktif OpenAPI/Swagger dapat diakses di `/docs`.

## Rule-Based vs Machine Learning

Pengembangan AI Service dibagi menjadi dua tahap:

### Tahap 6 (Saat ini): Rule-Based
- Algoritma prediksi menggunakan kalkulasi matematika linier: `base_score = visitor_count / max_capacity`.
- Faktor seperti akhir pekan (*weekend*), acara (*events*), dan cuaca memodifikasi *base_score* dengan bobot tertentu (misal: weekend +0.10).
- Tidak ada data training. Cepat dan sangat ringan.

### Tahap 7 (Masa depan): Machine Learning
- Menggunakan model terprediksi (misal: XGBoost, Random Forest, atau LSTM).
- Model akan di-*training* menggunakan data historis (`visitor_logs`).
- Model file (seperti `.pkl` atau `.onnx`) akan disimpan di folder `/models` di dalam `ai-service` dan di-*load* ke dalam memori saat service menyala.
- Confidence score akan di-generate langsung dari probabilitas model, bukan di-*hardcode*.

## Alur Komunikasi (Laravel <-> FastAPI)
Di masa depan, Laravel akan berkomunikasi dengan FastAPI secara internal:
1. Wisatawan membuka detail destinasi di Flutter.
2. Flutter menembak endpoint Laravel `GET /api/destinations/{id}/crowd-status`.
3. Laravel mengambil data historis dari database MySQL, lalu mengirim JSON request ke `POST http://127.0.0.1:8001/predict-crowd`.
4. FastAPI memproses prediksi (dengan rule-based atau ML).
5. FastAPI mengembalikan hasil ke Laravel.
6. Laravel mem-format data dan menyampaikannya kembali ke Flutter.
