# TODO - Rapikan Sidebar & CRUD Keuangan

## Tahap 1: Audit & Rapikan Sidebar
- [x] Perbaiki duplikasi menu "Kategori" di `resources/views/partials/sidebar.blade.php`
- [x] Ubah link menu placeholder (Pemasukan/Pengeluaran/Mutasi/Periode/Adjustment) menjadi route nyata
- [x] Perbarui state highlight `$menu[...]` di sidebar sesuai route names

## Tahap 2: Perbaiki CRUD Kategori yang ada
- [x] Perbaiki syntax error di `app/Http/Requests/CategoryRequest.php`
- [x] Perbaiki `CategoryController::store()` (huruf besar `Category::create`)
- [x] Pastikan create/show/edit tidak crash (buat view/modals konsisten bila diperlukan)

## Tahap 3: Tambahkan CRUD modul lain (resource)
- [x] Tambahkan `PeriodController` + `StorePeriodRequest` + views `resources/views/periods/*`
- [x] Tambahkan `TransactionController` + `StoreTransactionRequest` + views `resources/views/transactions/*`
- [x] Tambahkan `MutationController` + `StoreMutationRequest` + views `resources/views/mutations/*`
- [x] Tambahkan `AdjustmentLogController` + `StoreAdjustmentLogRequest` + views `resources/views/adjustments/*`
- [x] Daftarkan route resource masing-masing di `routes/web.php`

## Tahap 4: Konsistensi UI
- [x] Samakan style layout/title dan partial tabel antar modul (periods/transactions/mutations/adjustments) sesuai pola kategori
- [x] Pastikan aksi tombol Edit/Hapus pada mutations & adjustments menggunakan route/destroy yang benar (hapus placeholder `#`)
- [x] Samakan style table (thead/tbody padding, border, align) dan tombol aksi dasar


## Tahap 5: Verifikasi
- [x] Jalankan `php artisan route:list` dan pastikan route resource terdaftar
- [ ] Jalankan test/manual: akses index/create/edit/delete untuk tiap modul
- [x] Jalankan `php artisan test` (jika environment/test suite tersedia)

- [ ] Buat git branch `blackboxai/*`, commit, lalu push ke `master`

