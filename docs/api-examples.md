# 📡 API Examples — NusaFlow AI

Dokumentasi contoh request dan response untuk API publik NusaFlow AI.

**Base URL:** `http://localhost:8000/api`

---

## 1. GET /api/destinations

Menampilkan daftar destinasi wisata yang aktif.

### Query Parameters

| Parameter       | Type    | Default | Keterangan                           |
| --------------- | ------- | ------- | ------------------------------------ |
| `search`        | string  | –       | Cari berdasarkan nama, slug, address |
| `category_id`   | integer | –       | Filter berdasarkan ID kategori       |
| `category_slug` | string  | –       | Filter berdasarkan slug kategori     |
| `crowd_level`   | string  | –       | Filter: low, moderate, high, packed  |
| `limit`         | integer | 15      | Jumlah per halaman (max 50)          |

### Contoh Request

```
GET /api/destinations?search=pantai&limit=5
```

### Contoh Response

```json
{
  "success": true,
  "message": "Destinations retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Pantai Merdeka",
      "slug": "pantai-merdeka",
      "category": {
        "id": 1,
        "name": "Pantai",
        "slug": "pantai"
      },
      "description": "Pantai berpasir putih dengan pemandangan sunset yang indah.",
      "address": "Jl. Pantai Merdeka No. 1, Kabupaten Pesisir",
      "latitude": "-8.72340000",
      "longitude": "115.17250000",
      "max_capacity": 5000,
      "opening_hour": "06:00",
      "closing_hour": "18:00",
      "ticket_price": "15000.00",
      "image": null,
      "is_active": true
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 5,
    "total": 2
  }
}
```

---

## 2. GET /api/destinations/{id}

Menampilkan detail destinasi termasuk event, review terbaru, dan status keramaian.

### Contoh Request

```
GET /api/destinations/1
```

### Contoh Response

```json
{
  "success": true,
  "message": "Destination detail retrieved successfully",
  "data": {
    "id": 1,
    "name": "Pantai Merdeka",
    "slug": "pantai-merdeka",
    "category": {
      "id": 1,
      "name": "Pantai",
      "slug": "pantai"
    },
    "description": "Pantai berpasir putih dengan pemandangan sunset yang indah.",
    "address": "Jl. Pantai Merdeka No. 1, Kabupaten Pesisir",
    "latitude": "-8.72340000",
    "longitude": "115.17250000",
    "max_capacity": 5000,
    "opening_hour": "06:00",
    "closing_hour": "18:00",
    "ticket_price": "15000.00",
    "image": null,
    "is_active": true,
    "events": [
      {
        "id": 1,
        "name": "Festival Pantai Merdeka",
        "description": "Festival tahunan dengan pertunjukan seni dan kuliner.",
        "start_date": "2026-06-20",
        "end_date": "2026-06-27",
        "expected_impact": "high"
      }
    ],
    "latest_reviews": [],
    "crowd_status": {
      "destination_id": 1,
      "prediction_date": "2026-06-24",
      "prediction_hour": 10,
      "visitor_count": 118,
      "max_capacity": 5000,
      "crowd_score": 0.02,
      "crowd_level": "low",
      "crowd_label": "Sepi",
      "method": "rule_based",
      "factors": {
        "is_weekend": false,
        "has_event": false,
        "event_impact": null
      }
    }
  }
}
```

---

## 3. GET /api/destinations/{id}/crowd-status

Menampilkan prediksi keramaian destinasi berdasarkan rule-based analysis.

### Query Parameters

| Parameter | Type    | Default      | Keterangan            |
| --------- | ------- | ------------ | --------------------- |
| `date`    | date    | Hari ini     | Tanggal prediksi      |
| `hour`    | integer | Jam sekarang | Jam prediksi (0 – 23) |

### Contoh Request

```
GET /api/destinations/1/crowd-status?date=2026-06-24&hour=10
```

### Contoh Response

```json
{
  "success": true,
  "message": "Crowd status generated successfully",
  "data": {
    "destination_id": 1,
    "prediction_date": "2026-06-24",
    "prediction_hour": 10,
    "visitor_count": 118,
    "max_capacity": 5000,
    "crowd_score": 0.02,
    "crowd_level": "low",
    "crowd_label": "Sepi",
    "method": "rule_based",
    "factors": {
      "is_weekend": false,
      "has_event": false,
      "event_impact": null
    }
  }
}
```

### Crowd Level Reference

| Score       | Level    | Label         |
| ----------- | -------- | ------------- |
| 0.00 – 0.30 | low      | Sepi          |
| 0.31 – 0.60 | moderate | Normal        |
| 0.61 – 0.85 | high     | Ramai         |
| > 0.85      | packed   | Sangat Ramai  |

### Adjustment Factors

| Factor          | Adjustment |
| --------------- | ---------- |
| Weekend (Sab/Min) | +0.10    |
| Event low       | +0.05      |
| Event medium    | +0.10      |
| Event high      | +0.20      |

---

## 4. GET /api/destinations/{id}/recommendations

Memberikan rekomendasi destinasi alternatif yang tidak sedang ramai.

### Query Parameters

| Parameter | Type    | Default      | Keterangan                   |
| --------- | ------- | ------------ | ---------------------------- |
| `date`    | date    | Hari ini     | Tanggal untuk cek keramaian  |
| `hour`    | integer | Jam sekarang | Jam untuk cek keramaian      |
| `limit`   | integer | 5            | Jumlah rekomendasi (max 20)  |

### Contoh Request

```
GET /api/destinations/1/recommendations?limit=3
```

### Contoh Response

```json
{
  "success": true,
  "message": "Alternative destinations retrieved successfully",
  "data": [
    {
      "destination": {
        "id": 2,
        "name": "Teluk Biru",
        "slug": "teluk-biru",
        "category": {
          "id": 1,
          "name": "Pantai",
          "slug": "pantai"
        },
        "max_capacity": 2000,
        "is_active": true
      },
      "crowd_status": {
        "crowd_score": 0.07,
        "crowd_level": "low",
        "crowd_label": "Sepi"
      },
      "relevance_score": 90
    }
  ]
}
```

### Algoritma Scoring Rekomendasi

| Kriteria            | Skor Maks |
| ------------------- | --------- |
| Kategori sama       | +30       |
| Crowd rendah (low)  | +50       |
| Capacity > 0        | +10       |
| Jumlah review       | +10       |

Destinasi dengan status `packed` otomatis difilter dari hasil.

---

## Error Response

Semua endpoint mengembalikan format error yang konsisten:

```json
{
  "success": false,
  "message": "The hour field must not be greater than 23.",
  "errors": {
    "hour": ["The hour field must not be greater than 23."]
  }
}
```

---

*Terakhir diperbarui: Juni 2026*
