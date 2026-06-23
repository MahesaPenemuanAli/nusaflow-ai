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

```json
{
  "success": true,
  "message": "Deskripsi hasil",
  "data": { ... }
}
```

## Rencana Endpoint

### Destinasi Wisata

#### `GET /api/destinations`

Mengambil daftar semua destinasi wisata yang aktif.

- **Auth**: Tidak wajib
- **Query Params**:
  - `category` — Filter berdasarkan kategori (slug)
  - `search` — Pencarian berdasarkan nama
  - `lat`, `lng`, `radius` — Filter berdasarkan lokasi
  - `page`, `per_page` — Pagination
- **Response**: Array destinasi dengan pagination

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Pantai Kuta",
      "category": "alam",
      "address": "Kuta, Bali",
      "latitude": -8.7180,
      "longitude": 115.1690,
      "crowd_level": "moderate",
      "image": "https://..."
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 50,
    "per_page": 15
  }
}
```

---

#### `GET /api/destinations/{id}`

Mengambil detail satu destinasi wisata.

- **Auth**: Tidak wajib
- **Response**: Detail lengkap destinasi termasuk rating rata-rata dan jam operasional

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Pantai Kuta",
    "category": { "id": 1, "name": "Alam", "slug": "alam" },
    "description": "Pantai terkenal di Bali...",
    "address": "Kuta, Bali",
    "latitude": -8.7180,
    "longitude": 115.1690,
    "max_capacity": 5000,
    "opening_hour": "06:00",
    "closing_hour": "18:00",
    "ticket_price": 10000,
    "average_rating": 4.2,
    "total_reviews": 128
  }
}
```

---

#### `GET /api/destinations/{id}/crowd-status`

Mengambil status keramaian terkini dan prediksi untuk destinasi tertentu.

- **Auth**: Tidak wajib
- **Response**: Status keramaian saat ini dan prediksi beberapa hari ke depan

```json
{
  "success": true,
  "data": {
    "destination_id": 1,
    "current": {
      "crowd_level": "high",
      "predicted_count": 3500,
      "max_capacity": 5000,
      "percentage": 70
    },
    "forecast": [
      { "date": "2026-06-24", "crowd_level": "moderate", "predicted_count": 2500 },
      { "date": "2026-06-25", "crowd_level": "low", "predicted_count": 1200 }
    ],
    "model_version": "rule-based-v1",
    "confidence_score": 72.5
  }
}
```

---

#### `GET /api/destinations/{id}/recommendations`

Mengambil rekomendasi destinasi alternatif yang lebih sepi.

- **Auth**: Tidak wajib
- **Query Params**:
  - `limit` — Jumlah rekomendasi (default: 5)
  - `radius` — Radius pencarian dalam km (default: 20)
- **Response**: Array destinasi alternatif beserta prediksi keramaian

```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "name": "Pantai Dreamland",
      "distance_km": 8.3,
      "crowd_level": "low",
      "predicted_count": 450,
      "category": "alam",
      "similarity_score": 0.85
    }
  ]
}
```

---

### Admin Endpoints

#### `POST /api/admin/visitor-logs`

Menambahkan catatan log pengunjung harian (khusus admin).

- **Auth**: Wajib (role: admin)
- **Request Body**:

```json
{
  "destination_id": 1,
  "date": "2026-06-23",
  "visitor_count": 3200,
  "weather": "cerah",
  "notes": "Hari libur nasional"
}
```

- **Response**: Data visitor log yang baru dibuat

---

#### `POST /api/admin/events`

Menambahkan event yang mempengaruhi keramaian (khusus admin).

- **Auth**: Wajib (role: admin)
- **Request Body**:

```json
{
  "destination_id": 1,
  "name": "Festival Kuta Beach",
  "description": "Festival tahunan di Pantai Kuta",
  "start_date": "2026-07-01",
  "end_date": "2026-07-03",
  "expected_impact": "high"
}
```

- **Response**: Data event yang baru dibuat

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

## Catatan

- Autentikasi menggunakan Laravel Sanctum (Bearer Token).
- Rate limiting diterapkan untuk endpoint publik.
- Endpoint admin dilindungi middleware `auth` dan `role:admin`.
- Format tanggal menggunakan ISO 8601 (`YYYY-MM-DD`).

---

*Terakhir diperbarui: Juni 2026*
