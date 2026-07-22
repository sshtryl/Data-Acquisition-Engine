# Data Acquisition Engine

Aplikasi Laravel untuk mengekstrak informasi perusahaan dari berbagai sumber publik: metadata website, data registrasi domain (RDAP), dan lokasi geografis (OpenStreetMap Nominatim) — digabung menjadi satu endpoint terintegrasi.

## Daftar Isi

- [Arsitektur](#arsitektur)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Dokumentasi Endpoint](#dokumentasi-endpoint)
- [Testing dengan Postman](#testing-dengan-postman)
- [Asumsi](#asumsi)
- [Kendala & Keterbatasan](#kendala--keterbatasan)

## Arsitektur

Project ini menggunakan pola **MVC + Service Layer**:

```
Request
   ↓
Route (routes/web.php)
   ↓
Controller (app/Http/Controllers/)      → validasi input, error handling
   ↓
Service (app/Services/)                 → business logic, fetch API eksternal
   ↓
Response JSON  →  atau  →  Blade View + JS module (resources/)
```

| Layer | Tanggung jawab |
|---|---|
| Controller | Terima request, validasi, panggil Service, format response |
| Service | Logic murni: fetch API eksternal, parsing data — tidak menyentuh HTTP request/response |
| Blade View | Tampilan/UI per fitur |
| JS Module | Submit form via `fetch()`, render hasil ke DOM tanpa reload halaman |

## Instalasi

**Requirement:** PHP 8.2+, Composer, Node.js 18+, npm

```bash
# 1. Clone repository
git clone https://github.com/sshtryl/Data-Acquisition-Engine.git
cd <nama-folder>

# 2. Install dependency PHP
composer install

# 3. Install dependency JS
npm install

# 4. Copy file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Jalankan migration (jika ada tabel yang dipakai)
php artisan migrate
```

## Konfigurasi

Buka file `.env`, pastikan konfigurasi berikut sesuai:

```env
APP_NAME="Data Acquisition Engine"
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
```

Tidak ada API key eksternal yang diperlukan — seluruh layanan (RDAP, Nominatim) bersifat publik dan gratis.

**Catatan penting untuk Nominatim (OpenStreetMap):** API ini mewajibkan header `User-Agent` yang identifiable. Sudah diatur di `CompanyLocationService`, tapi disarankan mengganti email placeholder di dalamnya dengan email asli sebelum deploy ke production, untuk menghindari rate limit/pemblokiran dari OSM.

## Menjalankan Aplikasi

Buka 2 terminal terpisah:

```bash
# Terminal 1 — jalankan server Laravel
php artisan serve

# Terminal 2 — compile asset (CSS/JS) dan watch perubahan
npm run dev
```

Aplikasi bisa diakses di `http://localhost:8000`.

Untuk build production:
```bash
npm run build
```

## Dokumentasi Endpoint

Seluruh endpoint mengembalikan response dalam format **JSON**, dengan format error yang konsisten:
```json
{ "error": "Pesan error yang menjelaskan masalahnya" }
```

### 1. Extract Website Metadata

```
POST /extract/website
Content-Type: application/json
```

**Request body:**
```json
{ "url": "https://paper.id" }
```

**Response (200):**
```json
{
    "url": "https://paper.id",
    "title": "Paper.id",
    "description": "...",
    "canonical": "https://paper.id",
    "favicon": "/favicon.ico",
    "email": ["contact@paper.id"],
    "phone": ["+622112345678"],
    "social_media": ["https://facebook.com/paper.id"],
    "open_graph": {
        "title": "...",
        "description": "...",
        "image": "..."
    }
}
```

### 2. Extract Domain Intelligence

```
POST /extract/domain
Content-Type: application/json
```

**Request body:**
```json
{ "domain": "paper.id" }
```

**Response (200):**
```json
{
    "domain": "paper.id",
    "registrar": "PT ...",
    "registered_at": "2018-01-01T00:00:00Z",
    "expired_at": "2027-01-01T00:00:00Z",
    "last_updated": "2025-01-01T00:00:00Z",
    "status": ["active"],
    "nameservers": ["ns1.example.com", "ns2.example.com"]
}
```

### 3. Extract Company Location

```
POST /extract/location
Content-Type: application/json
```

**Request body:**
```json
{ "query": "PT Telkom Indonesia" }
```

**Response (200):**
```json
{
    "display_name": "PT Telkom Indonesia, ...",
    "latitude": "-6.9024657",
    "longitude": "107.6186597",
    "importance": "0.6",
    "osm_type": "way",
    "address": {
        "road": "...",
        "city": "...",
        "country": "Indonesia"
    }
}
```

### 4. Company Information (Combined)

```
GET /company-information?domain=paper.id
```

Menggabungkan ketiga endpoint di atas dalam satu response. Setiap bagian diproses independen — jika salah satu sumber gagal (misalnya domain tidak terdaftar di RDAP), bagian lain tetap dikembalikan normal.

**Response (200):**
```json
{
    "website": { "...": "hasil dari /extract/website" },
    "domain": { "...": "hasil dari /extract/domain" },
    "location": { "...": "hasil dari /extract/location" }
}
```

Jika salah satu sumber gagal diambil:
```json
{
    "website": { "...": "..." },
    "domain": { "error": "Domain tidak ditemukan atau RDAP tidak tersedia untuk: ..." },
    "location": { "...": "..." }
}
```

### Kode status HTTP yang digunakan

| Status | Kondisi |
|---|---|
| 200 | Berhasil |
| 422 | Validasi input gagal (field kosong/format salah) |
| 400 | Gagal memproses request (API eksternal gagal, data tidak ditemukan) |

## Testing dengan Postman

1. Import `Data-Acquisition-Engine.postman_collection.json` dan `Data-Acquisition-Engine.postman_environment.json` ke Postman.
2. Pilih environment **"Data Acquisition Engine - Local"** di pojok kanan atas Postman.
3. Untuk endpoint `POST`, pastikan header `Accept: application/json` sudah terpasang (sudah termasuk di collection) — ini penting agar Laravel mengembalikan JSON, bukan redirect HTML, saat terjadi error validasi.
4. Jalankan request satu per satu, atau gunakan Collection Runner untuk menjalankan semuanya sekaligus.

**Catatan:** endpoint `/extract/*` dan `/company-information` dikecualikan dari CSRF protection (lihat `bootstrap/app.php`) khusus untuk mempermudah testing lewat Postman, karena Postman tidak memiliki CSRF token seperti browser yang membuka halaman aplikasi.

## Asumsi

- Input `domain` pada endpoint `/extract/domain` dan `/company-information` diasumsikan berupa nama domain murni (contoh: `paper.id`), bukan URL lengkap.
- Endpoint `/extract/website` membutuhkan URL lengkap dengan scheme (`https://...`), bukan sekadar nama domain.
- Untuk endpoint `/company-information`, URL website yang di-fetch untuk metadata diasumsikan `https://{domain}` (HTTPS default).
- Hasil pencarian Nominatim yang diambil adalah hasil pertama (paling relevan) dari daftar kandidat yang dikembalikan API.
- Data RDAP mengasumsikan struktur response standar (`vcardArray`, `events`, `entities`) sesuai spesifikasi RDAP; TLD dengan implementasi RDAP non-standar berpotensi menghasilkan field kosong.

## Kendala & Keterbatasan

- **Rate limiting API eksternal:** RDAP dan Nominatim adalah layanan publik gratis tanpa API key, sehingga memiliki rate limit yang tidak terdokumentasi secara pasti. Penggunaan intensif/testing berulang berpotensi terkena pembatasan sementara dari pihak penyedia.
- **Ketersediaan data bervariasi per TLD:** tidak semua TLD memiliki dukungan RDAP penuh atau field data yang lengkap (misalnya beberapa domain `.id` memiliki data registrar yang tersembunyi/redacted karena kebijakan privasi registry).
- **Parsing HTML tidak selalu sempurna:** `DOMDocument` PHP cukup toleran terhadap HTML yang tidak valid, namun beberapa website dengan struktur non-standar atau yang memerlukan JavaScript rendering (SPA) mungkin tidak menghasilkan metadata yang akurat karena hanya HTML mentah yang diambil (tanpa eksekusi JavaScript).
- **Nominatim membutuhkan User-Agent yang valid:** tanpa header ini, request berpotensi ditolak oleh kebijakan penggunaan resmi OpenStreetMap.
- **CSRF dikecualikan untuk endpoint API:** karena endpoint ini juga diuji manual lewat Postman (bukan hanya lewat form di browser), proteksi CSRF dikecualikan khusus untuk path `/extract/*` dan `/company-information`. Endpoint ini tidak menyimpan/mengubah data sensitif pengguna, sehingga risiko keamanannya tergolong rendah untuk konteks aplikasi ini.