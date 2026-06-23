# 🗄️ Database Design — NusaFlow AI

## Gambaran Umum

Database NusaFlow AI dirancang untuk mendukung pengelolaan destinasi wisata, pencatatan kunjungan, prediksi keramaian, dan perencanaan itinerary. Semua tabel dikelola melalui Laravel Migration.

## Diagram Relasi

```
users ─────────┬──── reviews
               ├──── checkins
               └──── itineraries ──── itinerary_items
                                           │
destination_categories ── destinations ────┤
                              │            │
                              ├── visitor_logs
                              ├── events
                              ├── crowd_predictions
                              └── reviews
```

## Rencana Tabel

### 1. `users`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| name | VARCHAR(255) | Nama lengkap |
| email | VARCHAR(255) | Email (unique) |
| password | VARCHAR(255) | Password (hashed) |
| role | ENUM | `admin`, `tourist` |
| avatar | VARCHAR(255) | URL foto profil (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 2. `destination_categories`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| name | VARCHAR(255) | Nama kategori |
| slug | VARCHAR(255) | Slug URL-friendly (unique) |
| description | TEXT | Deskripsi kategori (nullable) |
| icon | VARCHAR(100) | Nama icon (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 3. `destinations`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| category_id | BIGINT (FK) | Relasi ke `destination_categories` |
| name | VARCHAR(255) | Nama destinasi |
| slug | VARCHAR(255) | Slug URL-friendly (unique) |
| description | TEXT | Deskripsi destinasi |
| address | VARCHAR(500) | Alamat lengkap |
| latitude | DECIMAL(10,8) | Koordinat latitude |
| longitude | DECIMAL(11,8) | Koordinat longitude |
| max_capacity | INTEGER | Kapasitas maksimal pengunjung |
| opening_hour | TIME | Jam buka |
| closing_hour | TIME | Jam tutup |
| ticket_price | DECIMAL(12,2) | Harga tiket (nullable) |
| image | VARCHAR(255) | URL gambar utama (nullable) |
| is_active | BOOLEAN | Status aktif |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 4. `visitor_logs`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| destination_id | BIGINT (FK) | Relasi ke `destinations` |
| date | DATE | Tanggal pencatatan |
| visitor_count | INTEGER | Jumlah pengunjung |
| weather | VARCHAR(50) | Kondisi cuaca (nullable) |
| notes | TEXT | Catatan tambahan (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 5. `events`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| destination_id | BIGINT (FK) | Relasi ke `destinations` |
| name | VARCHAR(255) | Nama event |
| description | TEXT | Deskripsi event (nullable) |
| start_date | DATE | Tanggal mulai |
| end_date | DATE | Tanggal selesai |
| expected_impact | ENUM | `low`, `medium`, `high` |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 6. `reviews`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| user_id | BIGINT (FK) | Relasi ke `users` |
| destination_id | BIGINT (FK) | Relasi ke `destinations` |
| rating | TINYINT | Rating 1-5 |
| comment | TEXT | Komentar ulasan (nullable) |
| visited_at | DATE | Tanggal kunjungan (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 7. `crowd_predictions`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| destination_id | BIGINT (FK) | Relasi ke `destinations` |
| prediction_date | DATE | Tanggal prediksi |
| predicted_count | INTEGER | Jumlah pengunjung yang diprediksi |
| crowd_level | ENUM | `low`, `moderate`, `high`, `packed` |
| confidence_score | DECIMAL(5,2) | Skor kepercayaan (0-100) |
| model_version | VARCHAR(50) | Versi model yang digunakan |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 8. `checkins`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| user_id | BIGINT (FK) | Relasi ke `users` |
| destination_id | BIGINT (FK) | Relasi ke `destinations` |
| checked_in_at | TIMESTAMP | Waktu check-in |
| checked_out_at | TIMESTAMP | Waktu check-out (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 9. `itineraries`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| user_id | BIGINT (FK) | Relasi ke `users` |
| title | VARCHAR(255) | Judul itinerary |
| start_date | DATE | Tanggal mulai perjalanan |
| end_date | DATE | Tanggal selesai perjalanan |
| notes | TEXT | Catatan perjalanan (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 10. `itinerary_items`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| itinerary_id | BIGINT (FK) | Relasi ke `itineraries` |
| destination_id | BIGINT (FK) | Relasi ke `destinations` |
| visit_date | DATE | Tanggal kunjungan |
| visit_order | INTEGER | Urutan kunjungan |
| start_time | TIME | Waktu mulai (nullable) |
| end_time | TIME | Waktu selesai (nullable) |
| notes | TEXT | Catatan item (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

## Catatan

- Foreign key menggunakan `onDelete('cascade')` atau `onDelete('set null')` sesuai kebutuhan.
- Index ditambahkan pada kolom yang sering di-query (`destination_id`, `date`, `user_id`).
- Rancangan ini bersifat awal dan dapat berubah seiring pengembangan.

---

*Terakhir diperbarui: Juni 2026*
