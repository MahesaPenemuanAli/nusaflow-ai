# 🔌 API Plan — NusaFlow AI

## Gambaran Umum

API NusaFlow AI menggunakan arsitektur RESTful yang disediakan oleh Laravel. Semua endpoint menggunakan prefix `/api` dan mengembalikan response dalam format JSON.

## Base URL

```
Development : http://localhost:8000/api
AI Service  : http://localhost:8001
```

## Format Response

Semua response mengikuti format standar:

### Sukses

```json
{
  "success": true,
  "message": "Deskripsi hasil",
  "data": { ... }
}
```

### Error

```json
{
  "success": false,
  "message": "Deskripsi error",
  "errors": { ... }
}
```

---

## Endpoint Aktif (Tahap 4) ✅

### Destinasi Wisata

#### `GET /api/destinations`

Mengambil daftar semua destinasi wisata yang aktif.

- **Auth**: Tidak wajib (public)
- **Status**: ✅ Aktif
- **Query Params**:
  - `search` — Pencarian berdasarkan nama, slug, atau alamat
  - `category_id` — Filter berdasarkan ID kategori
  - `category_slug` — Filter berdasarkan slug kategori
  - `crowd_level` — Filter berdasarkan level keramaian (low, moderate, high, packed)
  - `limit` — Jumlah per halaman (default: 15, max: 50)
- **Response**: Array destinasi dengan pagination

---

#### `GET /api/destinations/{id}`

Mengambil detail satu destinasi wisata.

- **Auth**: Tidak wajib (public)
- **Status**: ✅ Aktif
- **Response**: Detail destinasi termasuk:
  - Informasi kategori
  - Daftar event aktif
  - 5 review terbaru
  - Status keramaian rule-based otomatis

---

#### `GET /api/destinations/{id}/crowd-status`

Mengambil prediksi keramaian destinasi berdasarkan rule-based analysis.

- **Auth**: Tidak wajib (public)
- **Status**: ✅ Aktif
- **Query Params**:
  - `date` — Tanggal prediksi (default: hari ini, format: YYYY-MM-DD)
  - `hour` — Jam prediksi (default: jam sekarang, range: 0–23)
- **Logika Prediksi**:
  1. Hitung `crowd_score = visitor_count / max_capacity`
  2. Jika weekend (Sabtu/Minggu): `+0.10`
  3. Jika ada event aktif: `+0.05` (low), `+0.10` (medium), `+0.20` (high)
  4. Score dibatasi maksimal `1.00`
  5. Hasil disimpan ke tabel `crowd_predictions` via `updateOrCreate`
- **Crowd Level**:

  | Score        | Level    | Label        |
  | ------------ | -------- | ------------ |
  | 0.00 – 0.30  | low      | Sepi         |
  | 0.31 – 0.60  | moderate | Normal       |
  | 0.61 – 0.85  | high     | Ramai        |
  | > 0.85       | packed   | Sangat Ramai |

---

#### `GET /api/destinations/{id}/recommendations`

Mengambil rekomendasi destinasi alternatif yang lebih sepi.

- **Auth**: Tidak wajib (public)
- **Status**: ✅ Aktif
- **Query Params**:
  - `date` — Tanggal untuk cek keramaian (default: hari ini)
  - `hour` — Jam untuk cek keramaian (default: jam sekarang)
  - `limit` — Jumlah rekomendasi (default: 5, max: 20)
- **Algoritma Scoring**:

  | Kriteria           | Skor |
  | ------------------ | ---- |
  | Kategori sama      | +30  |
  | Crowd rendah (low) | +50  |
  | Capacity > 0       | +10  |
  | Jumlah review      | +10  |

  Destinasi dengan status `packed` difilter dari hasil.

---

### Admin Endpoints

Input data visitor log dan event dilakukan melalui **Filament Admin Dashboard** (`/admin`), bukan melalui API endpoint langsung. Ini memberikan keamanan yang lebih baik dan UI yang nyaman untuk operator admin.

---

## Endpoint Masa Depan (Direncanakan)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------| 
| POST | `/api/auth/register` | Registrasi wisatawan |
| POST | `/api/auth/login` | Login dan dapatkan token |
| POST | `/api/checkins` | Check-in di destinasi |
| GET | `/api/itineraries` | Daftar itinerary user |
| POST | `/api/itineraries` | Buat itinerary baru |
| POST | `/api/reviews` | Tulis ulasan destinasi |
| GET | `/api/ai/predict/{destination_id}` | Prediksi langsung dari AI service |

## Catatan Teknis

- Endpoint publik tidak memerlukan autentikasi.
- Autentikasi (jika ditambahkan nanti) akan menggunakan Laravel Sanctum (Bearer Token).
- Prediksi rule-based menggunakan `CrowdPredictionService` dengan method `rule_based` dan model version `rule-based-v1`.
- Format tanggal menggunakan ISO 8601 (`YYYY-MM-DD`).
- Contoh request/response lengkap tersedia di [`docs/api-examples.md`](api-examples.md).

---

*Terakhir diperbarui: Juni 2026*
