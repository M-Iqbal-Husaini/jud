<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Modern Web Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="#" class="flex-shrink-0">
                        <img class="h-8 w-8" src="/logo.png" alt="Logo">
                    </a>
                    <div class="hidden md:flex space-x-8 ml-10">
                        <a href="#features" class="text-gray-700 hover:text-indigo-600 transition duration-300">Fitur</a>
                        <a href="#testimonials" class="text-gray-700 hover:text-indigo-600 transition duration-300">Testimoni</a>
                        <a href="#pricing" class="text-gray-700 hover:text-indigo-600 transition duration-300">Harga</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 transition duration-300">Masuk</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-indigo-50">
        <div class="max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block">Tingkatkan Produktivitas</span>
                    <span class="block text-indigo-600">Tim Anda Sekarang</span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Platform kolaborasi modern yang membantu tim Anda bekerja lebih efisien dan mencapai hasil luar biasa.
                </p>
                <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                    <div class="rounded-md shadow">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Fitur Unggulan Kami
                </h2>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    Semua yang Anda butuhkan untuk mengelola tim secara efektif
                </p>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="pt-6">
                    <div class="flow-root bg-white rounded-lg px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300">
                        <div class="-mt-6">
                            <div class="flex items-center justify-center w-12 h-12 bg-indigo-600 text-white rounded-full">
                                <i class="fas fa-users-cog text-xl"></i>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">Manajemen Tim</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Kelola anggota tim, tugas, dan proyek dengan antarmuka yang intuitif.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="pt-6">
                    <div class="flow-root bg-white rounded-lg px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300">
                        <div class="-mt-6">
                            <div class="flex items-center justify-center w-12 h-12 bg-indigo-600 text-white rounded-full">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">Analitik Real-time</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Pantau perkembangan proyek dengan dashboard analitik lengkap.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="pt-6">
                    <div class="flow-root bg-white rounded-lg px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300">
                        <div class="-mt-6">
                            <div class="flex items-center justify-center w-12 h-12 bg-indigo-600 text-white rounded-full">
                                <i class="fas fa-shield-alt text-xl"></i>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">Keamanan Terjamin</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Data Anda dilindungi dengan enkripsi tingkat enterprise.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonial Section -->
    <div id="testimonials" class="bg-indigo-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Apa Kata Mereka?
                </h2>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-2">
                <!-- Testimonial 1 -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Testimonial">
                        <div class="ml-4">
                            <h4 class="font-semibold">Sarah Johnson</h4>
                            <p class="text-indigo-600">CEO Tech Startup</p>
                        </div>
                    </div>
                    <p class="mt-4 text-gray-600">
                        "Platform ini benar-benar mengubah cara tim kami berkolaborasi. Sangat mudah digunakan dan fiturnya lengkap!"
                    </p>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Testimonial">
                        <div class="ml-4">
                            <h4 class="font-semibold">Michael Chen</h4>
                            <p class="text-indigo-600">Project Manager</p>
                        </div>
                    </div>
                    <p class="mt-4 text-gray-600">
                        "Analitik real-time membantu kami membuat keputusan lebih cepat. Support timnya juga sangat responsif!"
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-indigo-600">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                    Siap Bergabung?
                </h2>
                <p class="mt-4 text-lg text-indigo-100">
                    Mulai gratis 14 hari tanpa perlu kartu kredit
                </p>
                <div class="mt-8">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <div class="space-y-4">
                    <img class="h-8 w-auto" src="/logo-white.png" alt="Logo">
                    <p class="text-gray-400 text-sm">
                        Membantu tim Anda mencapai produktivitas maksimal sejak 2023
                    </p>
                </div>
                <div class="space-y-4">
                    <h4 class="text-white font-semibold">Perusahaan</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Tentang Kami</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Karir</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Blog</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h4 class="text-white font-semibold">Dukungan</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Pusat Bantuan</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Status Layanan</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Kontak</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h4 class="text-white font-semibold">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Syarat Layanan</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Cookie</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-700 pt-8 text-center">
                <p class="text-gray-400 text-sm">
                    &copy; 2023 {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Smooth Scroll -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>