<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} – Bersihkan Komentar Judi YouTube</title>

    <!-- Tailwind -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <meta name="description"
          content="{{ config('app.name') }} membantu kreator YouTube mendeteksi dan menghapus komentar promosi judi online secara otomatis.">
</head>

<body class="antialiased bg-gray-50 text-gray-900">

    <!-- NAVBAR -->
    <nav class="bg-white shadow-sm sticky top-0 z-30 border-b border-red-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('assets/logo/logo.png') }}" class="h-9 w-9 rounded-lg" alt="logo">
                    <span class="font-bold text-lg tracking-tight text-red-600">
                        {{ config('app.name') }}
                    </span>

                    <div class="hidden md:flex space-x-6 ml-10">
                        <a href="#features" class="text-gray-700 hover:text-red-600 transition">Fitur</a>
                        <a href="#how-it-works" class="text-gray-700 hover:text-red-600 transition">Cara Kerja</a>
                    </div>
                </div>

                <!-- Login -->
                <a href="{{ route('login') }}"
                   class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-semibold shadow hover:bg-red-700 transition">
                    <i class="fab fa-google mr-2"></i> Masuk dengan Google
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 py-20 sm:py-28 grid lg:grid-cols-2 gap-12 items-center">

            <!-- Text -->
            <div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight">
                    Bersihkan Komentar
                    <span class="text-red-600">Judi Online</span>
                    di Channel YouTube Anda
                </h1>

                <p class="mt-4 text-gray-600 text-lg max-w-lg">
                    Sistem moderasi otomatis yang mendeteksi dan menolak komentar spam bertema judi online menggunakan AI berbahasa Indonesia.
                </p>

                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('login') }}"
                       class="px-6 py-3 bg-red-600 text-white rounded-md font-semibold shadow hover:bg-red-700 transition">
                        <i class="fab fa-google mr-2"></i> Mulai Sekarang
                    </a>

                    <a href="#how-it-works"
                       class="px-6 py-3 bg-red-50 border border-red-200 text-red-700 font-semibold rounded-md hover:bg-red-100 transition">
                        Lihat Cara Kerja
                    </a>
                </div>

                <div class="mt-6 text-sm text-gray-500 space-y-1">
                    <p><i class="fas fa-check text-red-600 mr-1"></i> Deteksi otomatis komentar spam</p>
                    <p><i class="fas fa-check text-red-600 mr-1"></i> Moderasi sekali klik</p>
                    <p><i class="fas fa-check text-red-600 mr-1"></i> Terintegrasi YouTube Data API</p>
                </div>
            </div>

            <!-- Mockup -->
            <div class="relative bg-white rounded-xl border shadow-lg p-6">
                <div class="text-sm font-semibold text-gray-700 flex items-center mb-4">
                    <i class="fab fa-youtube text-red-600 mr-2"></i>
                    Komentar Terbaru
                </div>

                <div class="space-y-4">
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="font-semibold text-gray-800">Komentar mencurigakan</p>
                        <p class="text-gray-600 text-sm mt-1">divideo ini kami semua menjelaskan cara ...</p>

                        <span class="inline-flex items-center mt-2 px-2 py-1 text-xs bg-red-600 text-white rounded">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Judi Terdeteksi
                        </span>
                    </div>

                    <div class="p-4 bg-gray-50 border rounded-lg">
                        <p class="font-semibold text-gray-800">Komentar positif</p>
                        <p class="text-gray-600 text-sm mt-1">Mantap bang! Lanjutkan konten edukasinya!</p>

                        <span class="inline-flex items-center mt-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                            <i class="fas fa-check mr-1"></i> Aman
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">

            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">Fitur Utama</h2>
                <p class="mt-3 text-gray-600">Dibuat khusus untuk kreator YouTube Indonesia</p>
            </div>

            <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-8">

                <div class="p-6 bg-white rounded-xl shadow hover:shadow-lg transition">
                    <div class="w-12 h-12 flex items-center justify-center bg-red-600 text-white rounded-lg">
                        <i class="fas fa-magic"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Deteksi Komentar Judi</h3>
                    <p class="text-gray-600 mt-2 text-sm">
                        AI membaca komentar dan menandai yang terindikasi promosi judi online.
                    </p>
                </div>

                <div class="p-6 bg-white rounded-xl shadow hover:shadow-lg transition">
                    <div class="w-12 h-12 flex items-center justify-center bg-red-600 text-white rounded-lg">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Moderasi Sekali Klik</h3>
                    <p class="text-gray-600 mt-2 text-sm">
                        Tanda komentar sebagai <b>rejected</b> langsung via API resmi YouTube.
                    </p>
                </div>

                <div class="p-6 bg-white rounded-xl shadow hover:shadow-lg transition">
                    <div class="w-12 h-12 flex items-center justify-center bg-red-600 text-white rounded-lg">
                        <i class="fas fa-language"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">AI Bahasa Indonesia</h3>
                    <p class="text-gray-600 mt-2 text-sm">
                        Model dilatih dengan dataset komentar Indonesia untuk akurasi optimal.
                    </p>
                </div>

                <div class="p-6 bg-white rounded-xl shadow hover:shadow-lg transition">
                    <div class="w-12 h-12 flex items-center justify-center bg-red-600 text-white rounded-lg">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Cepat & Akurat</h3>
                    <p class="text-gray-600 mt-2 text-sm">
                        Proses deteksi hanya beberapa detik meski komentar ratusan.
                    </p>
                </div>

                <div class="p-6 bg-white rounded-xl shadow hover:shadow-lg transition">
                    <div class="w-12 h-12 flex items-center justify-center bg-red-600 text-white rounded-lg">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Aman dengan OAuth</h3>
                    <p class="text-gray-600 mt-2 text-sm">
                        Akses moderasi komentar 100% legal melalui otorisasi Google.
                    </p>
                </div>

                <div class="p-6 bg-white rounded-xl shadow hover:shadow-lg transition">
                    <div class="w-12 h-12 flex items-center justify-center bg-red-600 text-white rounded-lg">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Model Bisa Dilatih Ulang</h3>
                    <p class="text-gray-600 mt-2 text-sm">
                        Anda dapat retrain model AI menggunakan dataset terbaru.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CARA KERJA -->
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">

            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Cara Kerja
                </h2>
            </div>

            <div class="mt-12 grid md:grid-cols-3 gap-10">

                <div class="text-center p-6 bg-gray-50 rounded-xl shadow">
                    <div class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center font-bold mx-auto">1</div>
                    <h3 class="mt-4 font-semibold text-gray-900">Login dengan Google</h3>
                    <p class="text-gray-600 text-sm mt-2">Hubungkan akun YouTube Anda</p>
                </div>

                <div class="text-center p-6 bg-gray-50 rounded-xl shadow">
                    <div class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center font-bold mx-auto">2</div>
                    <h3 class="mt-4 font-semibold text-gray-900">Pilih Video</h3>
                    <p class="text-gray-600 text-sm mt-2">Sistem akan menarik komentar terbaru</p>
                </div>

                <div class="text-center p-6 bg-gray-50 rounded-xl shadow">
                    <div class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center font-bold mx-auto">3</div>
                    <h3 class="mt-4 font-semibold text-gray-900">Moderasi Komentar</h3>
                    <p class="text-gray-600 text-sm mt-2">Hapus komentar dengan satu klik</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-gray-400 py-10">
        <div class="max-w-7xl mx-auto px-4">

            <div class="flex justify-between items-center flex-wrap">

                <div class="flex items-center space-x-3">
                    <img src="{{ asset('assets/logo/logo.png') }}" class="h-9 w-9 rounded-lg" alt="logo">
                    <span class="text-white font-semibold">{{ config('app.name') }}</span>
                </div>

                <div class="text-xs text-gray-500 mt-4 sm:mt-0">
                    Terintegrasi dengan <span class="text-red-400"><i class="fab fa-youtube mr-1"></i>YouTube Data API</span>
                </div>

                <div class="w-full border-t border-gray-800 my-6"></div>

                <div class="text-sm text-gray-500">
                    © {{ now()->year }} {{ config('app.name') }} — Semua hak dilindungi.
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
