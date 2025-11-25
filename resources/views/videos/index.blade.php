@extends('layouts.app')

@section('title', 'Video YouTube Saya')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Video YouTube Saya</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Video dari channel YouTube Anda</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
                @if(!auth()->user()->youtube_access_token)
                <a href="{{ route('connect.youtube') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fab fa-youtube mr-2"></i>
                    Hubungkan YouTube
                </a>
                @endif
            </div>
        </div>
    </div>

    @if(session('info'))
    <div class="bg-blue-50 border border-blue-200 text-blue-700 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-200 rounded-lg p-4 flex items-center shadow-sm">
        <div class="flex-shrink-0 text-lg">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium">{{ session('info') }}</p>
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-200 rounded-lg p-4 flex items-center shadow-sm">
        <div class="flex-shrink-0 text-lg">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium">{{ session('warning') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
        <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
            <span class="flex items-center">
                <i class="fab fa-youtube text-red-500 mr-2"></i>
                Menampilkan {{ count($videos) }} video dari YouTube
            </span>
            <div class="flex items-center space-x-4">
                <span>Channel: {{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($videos as $video)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
            <div class="relative">
                <img src="{{ $video['thumbnail'] }}" alt="{{ $video['title'] }}" class="w-full h-48 object-cover">
                <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded-sm font-medium">
                    {{ $video['duration'] }}
                </div>
                <div class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded-sm flex items-center font-medium">
                    <i class="fab fa-youtube mr-1"></i>
                    YouTube
                </div>
                <a href="{{ $video['youtube_url'] }}" target="_blank" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 hover:bg-opacity-40 transition-all duration-300 group">
                    <div class="bg-white bg-opacity-90 rounded-full p-4 transform scale-0 group-hover:scale-100 transition-transform duration-300">
                        <i class="fas fa-play text-red-600 text-xl"></i>
                    </div>
                </a>
            </div>

            <div class="p-4">
                <h3 class="font-semibold text-gray-800 dark:text-white text-lg leading-tight mb-2">
                    <a href="{{ $video['youtube_url'] }}" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400 line-clamp-2">
                        {{ $video['title'] }}
                    </a>
                </h3>

                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-3">
                    {{ $video['description'] }}
                </p>

                {{-- Old: Kategori Video (deteksi berdasarkan judul/deskripsi video) telah dihapus sesuai permintaan --}}

                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-3">
                    <span class="flex items-center mr-2">
                        <i class="fas fa-user-circle mr-1"></i>
                        {{ $video['author'] }}
                    </span>
                    <span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 px-2 py-0.5 rounded-full text-xs font-medium">
                        {{ $video['category'] }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center mr-2">
                        <i class="fas fa-eye mr-1"></i>
                        {{ number_format($video['views']) }} views
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ date('d M Y', strtotime($video['upload_date'])) }}
                    </span>
                </div>

                <div class="mt-4 flex space-x-2">
                    <a href="{{ $video['youtube_url'] }}" target="_blank" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 text-center">
                        <i class="fab fa-youtube mr-2"></i>
                        Tonton di YouTube
                    </a>
                    <button onclick="shareVideo('{{ $video['youtube_url'] }}', '{{ addslashes($video['title']) }}')" class="px-3 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <i class="fas fa-share-alt text-gray-600 dark:text-white"></i>
                    </button>
                </div>
                
                <div class="mt-3">
                    <button onclick="detectGamblingComments('{{ $video['id'] }}', '{{ addslashes($video['title']) }}', this)" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 ...">
                        <i class="fas fa-search mr-2"></i>
                        Deteksi Komentar Judi Online
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 text-center">
                <i class="fab fa-youtube text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada video ditemukan</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada video di channel YouTube Anda atau akun belum terhubung.</p>
                @if(!auth()->user()->youtube_access_token)
                <a href="{{ route('connect.youtube') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fab fa-youtube mr-2"></i>
                    Hubungkan YouTube
                </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>
</div> {{-- Tutup div class="space-y-6" --}}

{{-- Modal untuk Menampilkan Hasil Deteksi Komentar --}}
<div id="detectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden flex items-center justify-center">
    <div class="relative p-8 bg-white dark:bg-gray-800 w-full max-w-2xl mx-auto rounded-lg shadow-lg">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="modalTitle">Komentar Promosi Judi Online</h3>
            <button class="modal-close-button px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-3xl leading-none font-semibold focus:outline-none">×</button>
        </div>
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Video: <span class="font-semibold" id="modalVideoTitle"></span></p>
        </div>
        <div id="modalBody" class="max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-md p-3 space-y-3">
            <p id="loadingMessage" class="text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-spinner fa-spin mr-2"></i> Memuat dan mendeteksi komentar...
            </p>
            <div id="detectedCommentsList">
                {{-- Komentar akan dimuat di sini oleh JavaScript --}}
            </div>
            <p id="noCommentsMessage" class="text-center text-gray-500 dark:text-gray-400 hidden">Tidak ada komentar promosi judi online yang terdeteksi.</p>
            <p id="errorMessage" class="text-center text-red-500 hidden"></p>
        </div>
        <div class="flex justify-end pt-2">
            <button class="modal-close-button px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-md font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400">Tutup</button>
        </div>
        <div class="flex justify-between pt-2">
            <button id="deleteAllBtn"
                    onclick="deleteDetectedComments()"
                    class="px-6 py-2 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 hidden">
                <i class="fas fa-trash mr-2"></i>
                Hapus Semua Komentar Judi
            </button>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
// Pastikan Font Awesome CSS sudah di-link di layout utama Anda
// <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

function shareVideo(url, title) {
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).then(() => {
            console.log('Video shared successfully!');
        }).catch((error) => {
            console.error('Error sharing video:', error);
            copyToClipboard(url);
        });
    } else {
        copyToClipboard(url);
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Link video berhasil disalin ke clipboard!');
    }).catch(function() {
        alert('Gagal menyalin link. Silakan salin manual: ' + text);
    });
}

// === Fungsi Deteksi Komentar Judi Online ===
async function detectGamblingComments(videoId, videoTitle, buttonEl) {
    const modal = document.getElementById('detectionModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalVideoTitle = document.getElementById('modalVideoTitle');
    const loadingMessage = document.getElementById('loadingMessage');
    const detectedCommentsList = document.getElementById('detectedCommentsList');
    const noCommentsMessage = document.getElementById('noCommentsMessage');
    const errorMessage = document.getElementById('errorMessage');

    // Reset modal content
    modalVideoTitle.textContent = videoTitle;
    detectedCommentsList.innerHTML = '';
    noCommentsMessage.classList.add('hidden');
    errorMessage.classList.add('hidden');

    // Tampilkan loading, disable tombol
    loadingMessage.classList.remove('hidden');
    if (buttonEl) {
        buttonEl.disabled = true;
        buttonEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
    }
    modal.classList.remove('hidden');

    try {
        const response = await fetch(`/videos/${videoId}/detect-comments`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        loadingMessage.classList.add('hidden');

        const data = await response.json();

        if (response.ok) {
        if (data.status === 'success') {
            // simpan global untuk delete massal
            window._lastDetectedVideoId = videoId;
            window._lastDetectedComments = data.detected_comments || [];

            const deleteBtn = document.getElementById('deleteAllBtn');
            if (window._lastDetectedComments.length > 0) {
                deleteBtn.classList.remove('hidden');
            } else {
                deleteBtn.classList.add('hidden');
            }

            if (data.detected_comments && data.detected_comments.length > 0) {
                data.detected_comments.forEach(comment => {
                    const el = document.createElement('div');
                    el.className = 'p-3 bg-gray-50 dark:bg-gray-700 rounded-md shadow-sm mb-2';
                    el.innerHTML = `
                        <p class="font-semibold text-gray-800 dark:text-white">${comment.author}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">${comment.text}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Terdeteksi:
                            <span class="font-medium text-red-600 dark:text-red-400">${comment.detection_status}</span>
                            &bull; Skor: ${(comment.prediction_score ?? 0).toFixed(3)}
                            ${comment.publishedAt ? ' &bull; ' + new Date(comment.publishedAt).toLocaleDateString() : ''}
                        </p>
                    `;
                    detectedCommentsList.appendChild(el);
                });
            } else {
                noCommentsMessage.classList.remove('hidden');
            }
        } else {
                errorMessage.textContent = data.message || 'Terjadi kesalahan saat deteksi komentar.';
                errorMessage.classList.remove('hidden');
            }
        } else {
            errorMessage.textContent = data.message || `Terjadi kesalahan ${response.status}: ${response.statusText}`;
            errorMessage.classList.remove('hidden');
        }
    } catch (error) {
        loadingMessage.classList.add('hidden');
        errorMessage.textContent = 'Tidak dapat terhubung ke server untuk deteksi komentar.';
        errorMessage.classList.remove('hidden');
        console.error('Fetch error:', error);
    } finally {
        if (buttonEl) {
            buttonEl.disabled = false;
            buttonEl.innerHTML = '<i class="fas fa-search mr-2"></i> Deteksi Komentar Judi Online';
        }
    }
}

async function deleteDetectedComments() {
    const deleteBtn = document.getElementById('deleteAllBtn');

    const detected = window._lastDetectedComments || [];
    const videoId  = window._lastDetectedVideoId;

    if (!videoId || !detected.length) {
        alert('Tidak ada komentar terdeteksi untuk dihapus.');
        return;
    }

    // gunakan comment_id (bukan thread_id) untuk setModerationStatus
    const commentIds = detected
        .map(c => c.comment_id)
        .filter(id => !!id);

    if (!commentIds.length) {
        alert('ID komentar tidak ditemukan.');
        return;
    }

    if (!confirm(`Yakin ingin menghapus ${commentIds.length} komentar judi dari YouTube?`)) {
        return;
    }

    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menghapus...';

    try {
        const resp = await fetch(`/videos/${videoId}/delete-comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ comments: commentIds })  // <-- PENTING
        });

        const data = await resp.json();

        if (resp.ok && data.status === 'success') {
            alert(`Berhasil menghapus ${data.deleted_count} komentar (gagal: ${data.failed_count}).`);

            document.getElementById('detectedCommentsList').innerHTML = '';
            document.getElementById('noCommentsMessage').classList.remove('hidden');
            deleteBtn.classList.add('hidden');
        } else {
            alert(data.message || 'Gagal menghapus komentar.');
        }
    } catch (e) {
        console.error(e);
        alert('Tidak dapat terhubung ke server untuk hapus komentar.');
    } finally {
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = '<i class="fas fa-trash mr-2"></i> Hapus Semua Komentar Judi';
    }
}




// Fungsi untuk menutup modal
document.querySelectorAll('.modal-close-button').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('detectionModal').classList.add('hidden');
    });
});

// Tutup modal jika klik di luar konten modal
document.getElementById('detectionModal').addEventListener('click', (e) => {
    if (e.target.id === 'detectionModal') {
        document.getElementById('detectionModal').classList.add('hidden');
    }
});
</script>
@endsection