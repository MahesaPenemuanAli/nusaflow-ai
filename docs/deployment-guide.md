# Deployment Guide - NusaFlow AI MVP

## Deployment Architecture

```txt
Flutter Web
   |
   v
Laravel API + Filament Admin
   |
   v
PostgreSQL Cloud Database

Laravel API
   |
   v
FastAPI ML Service
```

Laravel menjadi API server dan admin panel. FastAPI berdiri sebagai service ML prediction. Flutter Web dideploy sebagai static web app yang memanggil Laravel API.

## Environment Variables

### Laravel

```env
APP_NAME="NusaFlow AI"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-laravel-api-domain.com

DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

AI_SERVICE_BASE_URL=https://your-fastapi-domain.com
AI_SERVICE_TIMEOUT=10
AI_SERVICE_ENABLED=true
AI_SERVICE_USE_ML=true

SANCTUM_STATEFUL_DOMAINS=
SESSION_DOMAIN=
FRONTEND_URL=https://your-flutter-web-domain.com
CORS_ALLOWED_ORIGINS=https://your-flutter-web-domain.com
CORS_SUPPORTS_CREDENTIALS=false
```

Isi `APP_KEY`, database, dan domain production di panel hosting atau secret manager. Jangan commit file `.env`.

### FastAPI

```env
APP_NAME=NusaFlow AI Service
APP_ENV=production
APP_DEBUG=false
API_VERSION=v1
```

### Flutter Web

Flutter Web memakai compile-time variable:

```bash
flutter build web --release --dart-define=API_BASE_URL=https://your-laravel-api-domain.com/api
```

## Deployment Steps

### A. Database PostgreSQL

1. Buat PostgreSQL cloud database di provider pilihan.
2. Ambil host, port, database, username, dan password.
3. Masukkan nilai tersebut ke environment Laravel.
4. Generate `APP_KEY` Laravel di environment production.
5. Jalankan migration dan seeder:

```bash
php artisan migrate --force
php artisan db:seed --force
```

### B. Deploy FastAPI

1. Deploy folder `ai-service`.
2. Install dependency:

```bash
pip install -r requirements.txt
```

3. Jalankan production server:

```bash
uvicorn app.main:app --host 0.0.0.0 --port ${PORT:-8001}
```

4. Cek endpoint:

```bash
curl https://your-fastapi-domain.com/health
curl https://your-fastapi-domain.com/model-info
```

5. Jika model belum tersedia di deployment, generate dataset dan train model:

```bash
python scripts/generate_sample_training_data.py
python scripts/train_crowd_model.py
```

Model binary `.joblib` dan `.pkl` diabaikan oleh git. Untuk production, simpan model lewat build artifact, persistent disk, atau object storage sesuai provider.

### C. Deploy Laravel

1. Deploy folder `backend-laravel`.
2. Set semua environment variable production.
3. Install dependency:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
```

4. Buat storage link:

```bash
php artisan storage:link
```

5. Jalankan migration:

```bash
php artisan migrate --force
```

6. Cache konfigurasi production:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

7. Jalankan server MVP:

```bash
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
```

8. Cek endpoint:

```bash
curl https://your-laravel-api-domain.com/api/destinations
curl https://your-laravel-api-domain.com/api/ai-service/health
```

Filament admin tersedia di:

```txt
https://your-laravel-api-domain.com/admin
```

### D. Deploy Flutter Web

1. Masuk ke folder Flutter:

```bash
cd frontend_flutter
```

2. Build Flutter Web:

```bash
flutter build web --release --dart-define=API_BASE_URL=https://your-laravel-api-domain.com/api
```

3. Deploy isi folder `build/web` ke Vercel, Netlify, Cloudflare Pages, Firebase Hosting, atau static hosting lain.
4. Buka domain Flutter Web dan pastikan aplikasi bisa mengambil data dari Laravel API.

## Docker MVP

Laravel:

```bash
docker build -t nusaflow-laravel ./backend-laravel
docker run --env-file backend-laravel/.env -p 8000:8000 nusaflow-laravel
```

FastAPI:

```bash
docker build -t nusaflow-ai-service ./ai-service
docker run --env-file ai-service/.env -p 8001:8001 nusaflow-ai-service
```

Gunakan `.env` lokal hanya di mesin sendiri atau secret environment di platform hosting.
