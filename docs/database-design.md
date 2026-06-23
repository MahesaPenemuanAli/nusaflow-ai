# 🗄️ Database Design — NusaFlow AI

## Gambaran Umum

Database NusaFlow AI dirancang untuk mendukung pengelolaan destinasi wisata, pencatatan kunjungan, prediksi keramaian, dan perencanaan itinerary. Semua tabel dikelola melalui Laravel Migration menggunakan SQLite (development) dengan kompatibilitas MySQL/PostgreSQL.

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

## Tabel Database

### 1. `users` (Laravel default + extension)

Tabel bawaan Laravel, ditambah kolom `role` dan `avatar` via migration terpisah.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| name | VARCHAR(255) | Nama lengkap |
| email | VARCHAR(255) | Email (unique) |
| email_verified_at | TIMESTAMP | Waktu verifikasi email (nullable) |
| password | VARCHAR(255) | Password (hashed) |
| role | VARCHAR(255) | `tourist`, `admin`, `super_admin` (default: `tourist`) |
| avatar | VARCHAR(255) | URL foto profil (nullable) |
| remember_token | VARCHAR(100) | Token remember me (nullable) |
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
| is_active | BOOLEAN | Status aktif (default: true) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 3. `destinations`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| destination_category_id | BIGINT (FK) | Relasi ke `destination_categories` (nullable, nullOnDelete) |
| name | VARCHAR(255) | Nama destinasi |
| slug | VARCHAR(255) | Slug URL-friendly (unique) |
| description | TEXT | Deskripsi destinasi (nullable) |
| address | TEXT | Alamat lengkap (nullable) |
| latitude | DECIMAL(10,8) | Koordinat latitude (nullable) |
| longitude | DECIMAL(11,8) | Koordinat longitude (nullable) |
| max_capacity | UNSIGNED INT | Kapasitas maksimal pengunjung (default: 0) |
| opening_hour | TIME | Jam buka (nullable) |
| closing_hour | TIME | Jam tutup (nullable) |
| ticket_price | DECIMAL(12,2) | Harga tiket (nullable) |
| image | VARCHAR(255) | URL gambar utama (nullable) |
| is_active | BOOLEAN | Status aktif (default: true) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 4. `visitor_logs`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| destination_id | BIGINT (FK) | Relasi ke `destinations` (cascadeOnDelete) |
| visit_date | DATE | Tanggal pencatatan |
| visit_hour | TINYINT | Jam kunjungan 0-23 (nullable) |
| visitor_count | UNSIGNED INT | Jumlah pengunjung (default: 0) |
| weather | VARCHAR(50) | Kondisi cuaca (nullable) |
| source | VARCHAR(255) | Sumber data (default: `admin_input`) |
| notes | TEXT | Catatan tambahan (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

**Index**: `(destination_id, visit_date, visit_hour)` — composite index untuk query prediksi per jam.

### 5. `events`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| destination_id | BIGINT (FK) | Relasi ke `destinations` (cascadeOnDelete) |
| name | VARCHAR(255) | Nama event |
| description | TEXT | Deskripsi event (nullable) |
| start_date | DATE | Tanggal mulai |
| end_date | DATE | Tanggal selesai |
| expected_impact | VARCHAR(255) | `low`, `medium`, `high` (default: `medium`) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 6. `reviews`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| user_id | BIGINT (FK) | Relasi ke `users` (cascadeOnDelete) |
| destination_id | BIGINT (FK) | Relasi ke `destinations` (cascadeOnDelete) |
| rating | TINYINT | Rating 1-5 |
| comment | TEXT | Komentar ulasan (nullable) |
| sentiment | VARCHAR(255) | `positive`, `neutral`, `negative` (nullable) |
| visited_at | DATE | Tanggal kunjungan (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 7. `crowd_predictions`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| destination_id | BIGINT (FK) | Relasi ke `destinations` (cascadeOnDelete) |
| prediction_date | DATE | Tanggal prediksi |
| prediction_hour | TINYINT | Jam prediksi (nullable, untuk prediksi per jam) |
| predicted_count | UNSIGNED INT | Jumlah pengunjung yang diprediksi (default: 0) |
| crowd_score | DECIMAL(5,2) | Persentase keramaian terhadap kapasitas (nullable) |
| crowd_level | VARCHAR(255) | `low`, `moderate`, `high`, `packed` (default: `low`) |
| confidence_score | DECIMAL(5,2) | Skor kepercayaan 0-100 (nullable) |
| method | VARCHAR(255) | `rule_based`, `machine_learning` (default: `rule_based`) |
| model_version | VARCHAR(50) | Versi model (default: `rule-based-v1`) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 8. `checkins`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| user_id | BIGINT (FK) | Relasi ke `users` (cascadeOnDelete) |
| destination_id | BIGINT (FK) | Relasi ke `destinations` (cascadeOnDelete) |
| checked_in_at | TIMESTAMP | Waktu check-in |
| checked_out_at | TIMESTAMP | Waktu check-out (nullable) |
| latitude | DECIMAL(10,8) | Koordinat latitude saat check-in (nullable) |
| longitude | DECIMAL(11,8) | Koordinat longitude saat check-in (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 9. `itineraries`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| user_id | BIGINT (FK) | Relasi ke `users` (cascadeOnDelete) |
| title | VARCHAR(255) | Judul itinerary |
| start_date | DATE | Tanggal mulai perjalanan (nullable) |
| end_date | DATE | Tanggal selesai perjalanan (nullable) |
| notes | TEXT | Catatan perjalanan (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 10. `itinerary_items`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT (PK) | Primary key |
| itinerary_id | BIGINT (FK) | Relasi ke `itineraries` (cascadeOnDelete) |
| destination_id | BIGINT (FK) | Relasi ke `destinations` (cascadeOnDelete) |
| visit_date | DATE | Tanggal kunjungan (nullable) |
| visit_order | UNSIGNED INT | Urutan kunjungan (default: 1) |
| start_time | TIME | Waktu mulai (nullable) |
| end_time | TIME | Waktu selesai (nullable) |
| notes | TEXT | Catatan item (nullable) |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

## Catatan Teknis

- **Enum vs String**: Kolom yang secara logis merupakan enum (`role`, `expected_impact`, `crowd_level`, `method`, `sentiment`) disimpan sebagai `VARCHAR` di migration untuk kompatibilitas SQLite. Validasi nilai dilakukan di level aplikasi (model/controller).
- **Foreign Key Strategy**: `destinations` menggunakan `nullOnDelete` untuk FK ke `destination_categories` agar data destinasi tidak hilang saat kategori dihapus. Semua FK lainnya menggunakan `cascadeOnDelete`.
- **Composite Index**: Tabel `visitor_logs` memiliki index gabungan `(destination_id, visit_date, visit_hour)` untuk optimasi query prediksi.

---

*Terakhir diperbarui: Juni 2026 — Tahap 2 selesai*
