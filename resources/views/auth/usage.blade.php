<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cara Pemakaian - KeuanganApp</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-slate-50">
    {{-- Navbar --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-slate-800">
                <x-keuangan-logo class="w-7 h-7" />
                KeuanganApp
            </a>
            <a href="{{ url('/') }}"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Beranda
            </a>
        </div>
    </header>

    {{-- Hero --}}
    <section class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
        <div class="max-w-4xl mx-auto px-4 py-14">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-3 py-1 text-xs font-medium mb-4">
                <x-keuangan-logo class="w-4 h-4" />
                Panduan Lengkap
            </div>
            <h1 class="text-3xl md:text-4xl font-bold leading-tight">Cara Pemakaian KeuanganApp</h1>
            <p class="mt-3 text-blue-100 leading-relaxed max-w-2xl">
                Mulai dari membuat akun hingga berbagi buku keuangan dengan keluarga â€” semua langkah dijelaskan di sini, lengkap dengan tips agar pencatatan keuangan Anda tetap rapi.
            </p>
        </div>
    </section>

    <main class="max-w-4xl mx-auto px-4 py-12">

        {{-- Daftar Isi --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-10">
            <h2 class="font-semibold text-slate-800 mb-4">Daftar Isi</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @php
                    $toc = [
                        ['#memulai', '1. Memulai', 'Daftar & login akun'],
                        ['#periode', '2. Periode Keuangan', 'Kerangka pencatatan'],
                        ['#kategori', '3. Kategori', 'Kelompok transaksi'],
                        ['#dompet', '4. Dompet', 'Bank, tunai, e-wallet'],
                        ['#transaksi', '5. Transaksi', 'Catat pemasukan & pengeluaran'],
                        ['#mutasi', '6. Mutasi & Penyesuaian', 'Pindah & koreksi saldo'],
                        ['#dashboard', '7. Dashboard', 'Pantau & analisis'],
                        ['#target', '8. Target & Capaian', 'Tetapkan tujuan keuangan'],
                        ['#ai', '9. Rekomendasi AI', 'Saran hemat otomatis'],
                        ['#laporan', '10. Laporan Excel', 'Unduh laporan periode'],
                        ['#keluarga', '11. Keluarga', 'Kelola bersama 2+ akun'],
                        ['#tips', '12. Tips', 'Praktik terbaik'],
                    ];
                @endphp
                @foreach ($toc as $item)
                    <a href="{{ $item[0] }}" class="flex items-start gap-3 rounded-lg p-3 hover:bg-blue-50 transition group">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold shrink-0">{{ $loop->iteration }}</span>
                        <span>
                            <span class="block text-sm font-medium text-slate-700 group-hover:text-blue-700">{{ $item[1] }}</span>
                            <span class="block text-xs text-slate-400">{{ $item[2] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Sections --}}
        <div class="space-y-10">

            @php
                $sections = [
                    [
                        'id' => 'memulai',
                        'title' => 'Memulai',
                        'desc' => 'Daftar akun baru atau masuk ke akun yang sudah ada.',
                        'steps' => [
                            ['Daftar Akun', 'Klik "Buat Akun Baru" di halaman beranda, isi nama, email, dan kata sandi.'],
                            ['Verifikasi (opsional)', 'Jika verifikasi email diaktifkan, buka tautan verifikasi yang dikirim ke email Anda.'],
                            ['Login', 'Masuk menggunakan email dan kata sandi. Anda akan diarahkan ke Dashboard.'],
                        ],
                    ],
                    [
                        'id' => 'periode',
                        'title' => 'Periode Keuangan',
                        'desc' => 'Periode adalah kerangka waktu pencatatan (tahunan/bulanan). Semua transaksi dikelompokkan ke dalam periode agar laporan per bulan menjadi akurat.',
                        'steps' => [
                            ['Otomatis Dibuat', 'Sistem otomatis membuat periode bulan berjalan saat Anda membuka dashboard.'],
                            ['Kelola di Menu Periode', 'Buka menu "Periode" untuk melihat daftar, menambah, atau menandai periode selesai (close).'],
                            ['Ubah Periode Aktif', 'Pada dashboard, gunakan dropdown "Semua Periode" untuk memilih periode tertentu dan melihat ringkasan khusus periode itu.'],
                            ['Unduh Laporan', 'Di halaman periode, klik ikon unduh untuk mengekspor laporan periode ke Excel.'],
                        ],
                    ],
                    [
                        'id' => 'kategori',
                        'title' => 'Kategori',
                        'desc' => 'Kategori mengelompokkan pemasukan dan pengeluaran (mis. Gaji, Makanan, Transportasi) sehingga laporan dan grafik lebih mudah dibaca.',
                        'steps' => [
                            ['Buka Menu Kategori', 'Klik "Kategori" di sidebar, lalu tombol Tambah.'],
                            ['Tentukan Tipe & Budget', 'Pilih tipe Pemasukan/Pengeluaran. Isi "Batas Anggaran" bila ingin memantau budget kategori.'],
                            ['Pilih di Transaksi', 'Saat mencatat transaksi, pilih kategori yang sesuai agar pengelompokan akurat.'],
                        ],
                    ],
                    [
                        'id' => 'dompet',
                        'title' => 'Dompet (Akun Kas)',
                        'desc' => 'Dompet mewakili tempat uang Anda berada: rekening bank, tunai, atau e-wallet.',
                        'steps' => [
                            ['Buka Menu Dompet', 'Klik "Dompet" di sidebar, lalu Tambah.'],
                            ['Isi Data', 'Nama dompet (mis. BCA, Cash, GoPay), tipe (Bank/Tunai/E-Wallet), dan keterangan opsional.'],
                            ['Saldo Terhitung Otomatis', 'Saldo setiap dompet dihitung dari seluruh transaksi yang tercatat pada dompet tersebut.'],
                            ['Pilih Saat Transaksi', 'Saat mencatat pemasukan/pengeluaran, pilih dompet asal dana.'],
                        ],
                    ],
                    [
                        'id' => 'transaksi',
                        'title' => 'Transaksi: Pemasukan & Pengeluaran',
                        'desc' => 'Inti aplikasi â€” catat setiap uang masuk dan keluar. Pengeluaran otomatis mengurangi saldo, pemasukan menambah saldo.',
                        'steps' => [
                            ['Pilih Menu', 'Di sidebar pilih "Pemasukan" atau "Pengeluaran", lalu klik Tambah.'],
                            ['Isi Detail', 'Tanggal, periode, kategori, dompet, jumlah, dan catatan opsional.'],
                            ['Upload Struk', 'Lampirkan foto struk/bukti (jpg, png, gif, webp, maks 2MB) â€” tersimpan aman dan bisa dilihat kapan saja.'],
                            ['Edit & Hapus', 'Gunakan tombol Edit atau Hapus pada baris transaksi bila perlu koreksi.'],
                        ],
                    ],
                    [
                        'id' => 'mutasi',
                        'title' => 'Mutasi & Penyesuaian (Adjustment)',
                        'desc' => 'Fitur khusus untuk memindahkan saldo antar dompet dan mengoreksi saldo.',
                        'steps' => [
                            ['Mutasi Antar Dompet', 'Gunakan menu "Mutasi" untuk mencatat pemindahan uang dari satu dompet ke dompet lain.'],
                            ['Penyesuaian', 'Gunakan "Adjustment" untuk mengoreksi saldo, misalnya selisih kas kecil atau pembulatan.'],
                            ['Sesuaikan Kategori', 'Mutasi dan adjustment tetap dapat dikelompokkan per kategori agar laporan tetap rapi.'],
                        ],
                    ],
                    [
                        'id' => 'dashboard',
                        'title' => 'Dashboard',
                        'desc' => 'Tampilan ringkasan seluruh keuangan Anda dalam satu layar.',
                        'steps' => [
                            ['Kartu Ringkasan', 'Total pemasukan, total pengeluaran, saldo bersih, dan saldo akhir periode.'],
                            ['Grafik Bulanan', 'Grafik pemasukan vs pengeluaran sepanjang tahun berjalan.'],
                            ['Rincian per Kategori', 'Lihat kategori mana yang paling besar pemasukan dan pengeluarannya.'],
                            ['Saldo per Dompet', 'Pantau saldo setiap dompet secara langsung.'],
                            ['Transaksi Terbaru', 'Sepuluh transaksi terakhir untuk pengecekan cepat.'],
                        ],
                    ],
                    [
                        'id' => 'target',
                        'title' => 'Target & Capaian',
                        'desc' => 'Tetapkan tujuan keuangan dan capaiannya dihitung otomatis dari transaksi Anda.',
                        'steps' => [
                            ['Pilih Jenis Target', 'Tabungan (pemasukan - pengeluaran), total pemasukan, batas pengeluaran, batas per kategori, atau target saldo dompet.'],
                            ['Isi Nominal & Periode', 'Tentukan nominal target dan periode/rentang tanggal. Kosongkan periode untuk memakai periode berjalan.'],
                            ['Pantau Progress', 'Setiap target menampilkan progress bar, persentase, dan sisa nominal. Status "Tercapai" / "Melebihi batas" otomatis diperbarui.'],
                            ['Aktif/Nonaktif', 'Matikan target yang tidak lagi relevan lewat kotak centang "Aktif".'],
                        ],
                    ],
                    [
                        'id' => 'ai',
                        'title' => 'Rekomendasi AI',
                        'desc' => 'Asisten AI menganalisis seluruh data Anda dan memberi saran konkret untuk menghemat dan menaikkan pendapatan.',
                        'steps' => [
                            ['Buka Menu Target & Capaian', 'Klik tombol "Minta Rekomendasi" pada panel Rekomendasi AI.'],
                            ['AI Menganalisis', 'AI membaca pemasukan, pengeluaran per kategori, saldo dompet, dan capaian target Anda.'],
                            ['Terima Saran', 'Dapatkan rekomendasi penghematan (dengan estimasi nominal), ide meningkatkan pendapatan, dan evaluasi target.'],
                        ],
                    ],
                    [
                        'id' => 'laporan',
                        'title' => 'Laporan Excel',
                        'desc' => 'Ekspor seluruh transaksi satu periode ke file Excel untuk arsip atau analisis lebih lanjut.',
                        'steps' => [
                            ['Buka Menu Periode', 'Temukan periode yang ingin dilaporkan.'],
                            ['Klik Unduh', 'Tekan tombol unduh (laporan) pada baris periode.'],
                            ['Dapatkan File', 'File .xlsx berisi daftar transaksi, total pemasukan, total pengeluaran, dan saldo akhir.'],
                        ],
                    ],
                    [
                        'id' => 'keluarga',
                        'title' => 'Keluarga (Kelola Bersama)',
                        'desc' => 'Satukan pengelolaan keuangan untuk 2 akun atau lebih dalam satu buku bersama â€” misalnya Anda dan pasangan.',
                        'steps' => [
                            ['Buat Keluarga', 'Buka menu "Keluarga", klik "Buat Keluarga", beri nama keluarga. Anda otomatis menjadi Kepala Keluarga.'],
                            ['Undang Anggota', 'Masukkan email akun yang sudah terdaftar (mis. pasangan) untuk bergabung.'],
                            ['Kelola Bersama', 'Semua anggota melihat & mengelola transaksi, dompet, periode, dan target yang sama. Setiap transaksi tetap tercatat pembuatnya.'],
                            ['Keluar / Bubarkan', 'Anggota bisa keluar sendiri; Kepala Keluarga bisa mengeluarkan anggota atau membubarkan keluarga.'],
                        ],
                    ],
                    [
                        'id' => 'tips',
                        'title' => 'Tips Praktik Terbaik',
                        'desc' => 'Agar pencatatan tetap rapi dan akurat.',
                        'tips' => [
                            'Catat transaksi setiap hari agar tidak menumpuk dan lupa.',
                            'Gunakan kategori yang konsisten agar laporan per kategori akurat.',
                            'Upload struk untuk pengeluaran penting sebagai bukti.',
                            'Pantau target tabungan setiap pekan, bukan hanya akhir bulan.',
                            'Manfaatkan Rekomendasi AI sebulan sekali untuk mengevaluasi penghematan.',
                            'Jika berkeluarga, buat satu buku keluarga agar tidak ada pengeluaran ganda.',
                            'Beri nama periode dan dompet dengan jelas agar mudah dikenali.',
                        ],
                    ],
                ];
            @endphp

            @foreach ($sections as $section)
                <section id="{{ $section['id'] }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8 scroll-mt-24">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">{{ $loop->iteration }}</span>
                        <h2 class="text-xl font-bold text-slate-800">{{ $section['title'] }}</h2>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed mb-5">{{ $section['desc'] }}</p>

                    @if (isset($section['steps']))
                        <ol class="space-y-4">
                            @foreach ($section['steps'] as $step)
                                <li class="flex gap-4">
                                    <span class="w-6 h-6 rounded-full border-2 border-blue-200 text-blue-700 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">{{ $loop->iteration }}</span>
                                    <div>
                                        <p class="font-medium text-slate-700">{{ $step[0] }}</p>
                                        <p class="text-sm text-slate-500 mt-0.5 leading-relaxed">{{ $step[1] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <ul class="space-y-3">
                            @foreach ($section['tips'] as $tip)
                                <li class="flex gap-3">
                                    <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <p class="text-sm text-slate-600 leading-relaxed">{{ $tip }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach
        </div>

        {{-- CTA --}}
        <div class="mt-12 bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-8 text-center">
            <h2 class="text-xl font-bold">Siap mulai mengatur keuangan Anda?</h2>
            <p class="text-blue-100 text-sm mt-2 mb-6">Buat akun gratis dan catat pemasukan pertama Anda hari ini.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg bg-white text-blue-700 px-6 py-3 font-semibold hover:bg-blue-50 transition">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg border border-white/40 px-6 py-3 font-semibold hover:bg-white/10 transition">Buat Akun Baru</a>
                @endif
            </div>
        </div>

        <p class="mt-10 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'KeuanganApp') }} &middot;
            <a href="{{ url('/') }}" class="hover:text-slate-600">Beranda</a>
        </p>
    </main>
</body>
</html>
