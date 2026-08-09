# KeuanganApp

Aplikasi pencatatan keuangan pribadi & keluarga berbasis web. Kelola pemasukan, pengeluaran, mutasi antar akun, target capaian, dan pantau saldo dalam periode tertentu — lengkap dengan dashboard interaktif, fitur berbagi satu rumah tangga, hingga rekomendasi AI.

> Dibangun dengan Laravel + Tailwind CSS + Alpine.js + Chart.js.

## ✨ Fitur

- **Autentikasi** — Login, registrasi, dan manajemen profil (Laravel Breeze).
- **Dashboard** — Ringkasan saldo, pemasukan, pengeluaran, dan grafik per periode.
- **Transaksi** — Catat pemasukan & pengeluaran per kategori dengan attach foto bukti.
- **Mutasi Akun** — Transfer saldo antar akun/dompet.
- **Penyesuaian Saldo** — Koreksi mutasi, selisih hitung, dan bonus.
- **Target (Goals)** — Tentukan target hemat/berhemat dan pantau capaian & progress per kategori.
- **Rekomendasi AI** — Saran menghemat & menambah pemasukan berdasarkan data Anda (OpenRouter, gratis).
- **Fitur Keluarga** — Buat/satukan keluarga, kelola anggota, dan berbagi data keuangan.
- **Periode Keuangan** — Rekap tahunan/bulanan dengan laporan.
- **Manajemen Pengguna (Admin)** — Kelola akun pengguna & peran.
- **Manajemen Kategori & Akun** — Kustomisasi kategori dan rekening/dompet.
- **PWA (Progressive Web App)** — Bisa dipasang di HP dan tetap kebuka cepat.
- **Export Excel** — Unduh data transaksi ke spreadsheet.

## 🧰 Teknologi

| Bagian | Teknologi |
| --- | --- |
| Backend | PHP 8.3, Laravel 13 |
| Frontend | Tailwind CSS, Alpine.js, Chart.js, Sweetalert2 |
| Build | Vite, Laravel Vite Plugin |
| Database | MySQL |
| AI | OpenRouter API (optional) |
| Lainnya | maatwebsite/excel, blade-heroicons, Ziggy |

## 🚀 Instalasi

Gunakan eksekusi `composer setup` yang sudah tersedia di `composer.json` (default build otomatis).

### Prasyarat

- PHP 8.3+
- Composer
- Node.js & npm
- Server MySQL (ataupun ubah `.env` untuk SQLite dsb.)

### Langkah Cepat (dengan script)

```bash
# 1. Clone repositori
git clone https://github.com/yasiryas/catat-uang.git
cd catat-uang

# 2. Setup otomatis (install dependency, isi .env, generate key, migrate, build asset)
composer setup

# 3. Isi kredensial database di file .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 4. Jalankan development server
php artisan serve
```

### Setup Manual

```bash
# Dependensi
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Sesuaikan koneksi database di .env, lalu migrasi
php artisan migrate

# Build asset (atau jalankan dalam mode dev)
npm run build
# atau
npm run dev

# Jalankan server
php artisan serve
```

> Untuk mode development dengan hot-reload (Vite + queue worker), jalankan:

```bash
composer dev
```

### Konfigurasi AI (Opsional)

Fitur rekomendasi AI menggunakan [OpenRouter](https://openrouter.ai) dengan model gratis. Tambahkan di `.env`:

```env
AI_ENABLED=true
OPENROUTER_API_KEY=your-api-key
OPENROUTER_MODEL=google/gemma-4-26b-a4b-it:free
```

## 💾 Struktur Database

Migrasi tersedia di `database/migrations/` untuk tabel:

- `users`, `families` (keluarga), `categories`, `accounts`
- `periods` (periode tahunan), `transactions` (pemasukan & pengeluaran)
- `mutations`, `adjustment_logs`, `goals` (target)

## 🧑‍🤝‍🧑 Keluarga & Pengguna

- Pengguna dapat bergabung dalam **satu keluarga** berbagi data keuangan.
- Admin dapat mengelola pengguna dari menu **Users**.
- Data ter-scope per user & per periode tahunan.

## 📄 Lisensi

Proyek ini dirilis di bawah lisensi [MIT](LICENSE).

---

Dibangun untuk membantu mencatat dan memahami keuangan pribadi – catat pemasukan, pantau pengeluaran, capai target. 😄