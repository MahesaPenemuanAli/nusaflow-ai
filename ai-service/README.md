# NusaFlow AI Service

AI service terpisah untuk memprediksi keramaian dan merekomendasikan destinasi wisata. Service ini dibangun dengan Python FastAPI.

Saat ini service masih berjalan dengan algoritma *rule-based* sebagai fondasi. Model machine learning penuh akan ditambahkan pada tahap pengembangan selanjutnya.

## Struktur Folder
```
ai-service/
├── app/
│   ├── main.py
│   ├── config.py
│   ├── routes/
│   ├── schemas/
│   └── services/
├── data/
├── tests/
├── run.py
└── requirements.txt
```

## Prasyarat
- Python 3.10+

## Cara Instalasi & Setup Environment

1. Buka terminal dan masuk ke folder `ai-service`:
   ```bash
   cd ai-service
   ```
2. Buat virtual environment:
   ```bash
   python -m venv .venv
   ```
3. Aktifkan virtual environment:
   - **Windows:** `.venv\Scripts\activate`
   - **Linux/Mac:** `source .venv/bin/activate`
4. Update pip dan install dependencies:
   ```bash
   python -m pip install --upgrade pip
   pip install -r requirements.txt
   ```

## Setup di VS Code (Mengatasi Error Import Pylance)

Jika Anda melihat error seperti `Cannot find module 'fastapi'`, lakukan langkah ini di VS Code:
1. Tekan **Ctrl + Shift + P** (atau Cmd + Shift + P di Mac).
2. Ketik dan pilih **Python: Select Interpreter**.
3. Pilih interpreter dari dalam folder `.venv`, contohnya: `ai-service/.venv/Scripts/python.exe`.

Error Pylance akan hilang setelah VS Code menggunakan interpreter `.venv`.

## Cara Menjalankan Service

Jalankan perintah berikut:
```bash
python run.py
```

Service akan berjalan di `http://127.0.0.1:8001`.

## Machine Learning

Saat ini `ai-service` telah dilengkapi dengan model machine learning berbasis RandomForestRegressor untuk memprediksi keramaian wisata. Data yang digunakan merupakan *dummy dataset* untuk tujuan *prototype*. Di masa depan, data akan menggunakan *visitor logs* dari Laravel.

### 1. Generate Dataset Dummy
```bash
python scripts/generate_sample_training_data.py
```

### 2. Training Model
```bash
python scripts/train_crowd_model.py
```
Model dan metadata akan disimpan ke dalam folder `saved_models/`.

## Endpoint Tersedia

- `GET /` : Endpoint root
- `GET /health` : Health check
- `GET /model-info` : Menampilkan informasi versi & metadata model ML
- `POST /predict-crowd` : Prediksi keramaian (rule-based)
- `POST /predict-crowd-ml` : Prediksi keramaian (machine learning)
- `POST /recommend-destinations` : Rekomendasi destinasi alternatif

## Swagger UI

Anda dapat menguji endpoint dan melihat skema data lengkap melalui antarmuka Swagger UI:
👉 **[http://127.0.0.1:8001/docs](http://127.0.0.1:8001/docs)**

## Contoh Payload

Terdapat contoh payload di dalam file `data/sample_payloads.json` yang dapat digunakan untuk melakukan testing pada endpoint POST.

---
*Catatan: Service ini adalah fondasi untuk machine learning di tahap berikutnya.*
