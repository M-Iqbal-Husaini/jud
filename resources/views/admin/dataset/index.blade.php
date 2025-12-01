@extends('admin.layouts.app')

@section('title', 'Datasets')

@section('content')
<div class="max-w-6xl mx-auto space-y-4">

    {{-- ACTION BAR --}}
    <div class="bg-white px-5 py-4 shadow-sm rounded-xl border border-red-100 flex items-center justify-between">
        <div>
            <h3 class="font-semibold text-lg text-gray-900">Datasets</h3>
            <p class="text-sm text-gray-500">
                Kelola dataset: buat baru atau import file CSV.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <button id="btnCreate"
                    class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 focus:outline-none">
                <i class="fas fa-plus mr-1"></i> Dataset Baru
            </button>
            <button id="btnImport"
                    class="px-4 py-2 bg-white text-sm font-semibold rounded-lg border border-red-500 text-red-600 hover:bg-red-50 focus:outline-none">
                <i class="fas fa-file-import mr-1"></i> Import CSV
            </button>
        </div>
    </div>

    {{-- CREATE MODAL --}}
    <div id="modalCreate"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-xl shadow-lg w-11/12 md:w-1/3 p-5">
            <h4 class="font-semibold text-lg mb-3 text-gray-900">Buat Dataset Baru</h4>

            <form method="POST" action="{{ route('admin.dataset.create') }}">
                @csrf

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Dataset</label>
                    <input name="name" type="text"
                           class="border border-gray-300 px-3 py-2 rounded w-full text-sm focus:outline-none focus:ring-1 focus:ring-red-500"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (opsional)</label>
                    <textarea name="description"
                              class="border border-gray-300 px-3 py-2 rounded w-full text-sm focus:outline-none focus:ring-1 focus:ring-red-500"
                              rows="3"></textarea>
                </div>

                <div class="flex justify-end space-x-2 text-sm">
                    <button type="button"
                            onclick="closeModal('modalCreate')"
                            class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- IMPORT MODAL --}}
    <div id="modalImport"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-xl shadow-lg w-11/12 md:w-1/3 p-5">
            <h4 class="font-semibold text-lg mb-3 text-gray-900">Import CSV ke Dataset</h4>

            <form id="formImport" method="POST" enctype="multipart/form-data" action="">
                @csrf

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Dataset</label>
                    <select id="importDatasetSelect"
                            class="border border-gray-300 px-3 py-2 rounded w-full text-sm focus:outline-none focus:ring-1 focus:ring-red-500"
                            required>
                        <option value="">-- pilih dataset --</option>
                        @foreach($datasets as $ds)
                            <option value="{{ $ds->id }}">
                                {{ $ds->name }} ({{ $ds->rows }} rows)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        File (CSV / XLS / XLSX — kolom: <code>text</code>, <code>label</code>)
                    </label>
                    <input type="file"
                        name="datafile"
                        accept=".csv,.txt,.xls,.xlsx"
                        class="border border-gray-300 px-3 py-2 rounded w-full text-sm focus:outline-none focus:ring-1 focus:ring-red-500"
                        required>
                </div>

                <div class="flex justify-end space-x-2 text-sm">
                    <button type="button"
                            onclick="closeModal('modalImport')"
                            class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700">
                        Import
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white px-5 py-4 shadow-sm rounded-xl border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900">Daftar Dataset</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b">
                        <th class="p-2 font-medium">ID</th>
                        <th class="p-2 font-medium">Nama</th>
                        <th class="p-2 font-medium">Jumlah Baris</th>
                        <th class="p-2 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($datasets as $d)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="p-2 text-xs text-gray-600">{{ $d->id }}</td>
                            <td class="p-2">{{ $d->name }}</td>
                            <td class="p-2">{{ $d->rows }}</td>
                            <td class="p-2 text-center space-x-1">
                                <a href="{{ route('admin.dataset.show', $d->id) }}"
                                   class="inline-block px-3 py-1.5 bg-white border border-red-500 text-red-600 rounded-lg hover:bg-red-50">
                                    Lihat
                                </a>

                                <a href="{{ route('admin.dataset.export', $d->id) }}"
                                   class="inline-block px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Export
                                </a>

                                <form action="{{ route('admin.dataset.destroy', $d->id) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Hapus dataset ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">
                                Belum ada dataset.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        const el = document.getElementById(id);
        el.classList.remove('hidden');
        el.classList.add('flex');
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        el.classList.add('hidden');
        el.classList.remove('flex');
    }

    document.getElementById('btnCreate').addEventListener('click', () => openModal('modalCreate'));
    document.getElementById('btnImport').addEventListener('click', () => openModal('modalImport'));

    // IMPORT — set action sesuai dataset yg dipilih
    const importSelect = document.getElementById('importDatasetSelect');
    const formImport   = document.getElementById('formImport');

    if (importSelect && formImport) {
        importSelect.addEventListener('change', function () {
            const id = this.value;
            formImport.action = id ? `/admin/dataset/import/${id}` : '';
        });
    }
</script>
@endsection
