@extends('admin.layouts.app')

@section('title','Datasets')

@section('content')
<div class="container mx-auto space-y-4">

    {{-- ACTIONS BAR --}}
    <div class="bg-white p-4 shadow rounded flex items-center justify-between">
        <div>
            <h3 class="font-semibold text-lg">Datasets</h3>
            <p class="text-sm text-gray-500">Kelola dataset: buat baru atau import CSV.</p>
        </div>

        <div class="flex items-center space-x-2">
            <button id="btnCreate" class="px-4 py-2 bg-indigo-600 text-white rounded">Create</button>
            <button id="btnImport" class="px-4 py-2 bg-yellow-500 text-white rounded">Import</button>
        </div>
    </div>

    {{-- CREATE MODAL --}}
    <div id="modalCreate" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded shadow-lg w-11/12 md:w-1/3 p-4">
            <h4 class="font-semibold mb-2">Create Dataset</h4>

            <form method="POST" action="{{ route('admin.dataset.create') }}">
                @csrf
                <div class="mb-2">
                    <label class="block text-sm">Name</label>
                    <input name="name" type="text" class="border p-2 w-full" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm">Description (optional)</label>
                    <textarea name="description" class="border p-2 w-full"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('modalCreate')" class="px-3 py-1 border rounded">Cancel</button>
                    <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded">Create</button>
                </div>
            </form>
        </div>
    </div>

    {{-- IMPORT MODAL --}}
    <div id="modalImport" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded shadow-lg w-11/12 md:w-1/3 p-4">
            <h4 class="font-semibold mb-2">Import CSV to Dataset</h4>

            <form id="formImport" method="POST" enctype="multipart/form-data" action="">
                @csrf

                <div class="mb-2">
                    <label class="block text-sm">Target Dataset</label>
                    <select id="importDatasetSelect" class="border p-2 w-full" required>
                        <option value="">-- pilih dataset --</option>
                        @foreach($datasets as $ds)
                            <option value="{{ $ds->id }}">{{ $ds->name }} ({{ $ds->rows }} rows)</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm">CSV File (columns: text, label)</label>
                    <input type="file" name="csvfile" accept=".csv,.txt" class="border p-2 w-full" required>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('modalImport')" class="px-3 py-1 border rounded">Cancel</button>
                    <button type="submit" class="px-3 py-1 bg-yellow-500 text-white rounded">Import</button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white p-4 shadow rounded">
        <h3 class="font-semibold mb-2">Available Datasets</h3>

        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2">ID</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Rows</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($datasets as $d)
                <tr class="border-t">
                    <td class="p-2 text-sm">{{ $d->id }}</td>
                    <td class="p-2 text-sm">{{ $d->name }}</td>
                    <td class="p-2 text-sm">{{ $d->rows }}</td>
                    <td class="p-2 space-x-2">

                        <a href="{{ route('admin.dataset.show', $d->id) }}" 
                           class="px-3 py-1 bg-indigo-600 text-white rounded">Open</a>

                        <a href="{{ route('admin.dataset.export', $d->id) }}" 
                           class="px-3 py-1 bg-green-600 text-white rounded">Export</a>

                        <form action="{{ route('admin.dataset.destroy', $d->id) }}" 
                              method="POST" class="inline-block"
                              onsubmit="return confirm('Delete this dataset?');">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
                        </form>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">No dataset found.</td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>
</div>

<script>
function openModal(id){ 
    document.getElementById(id).classList.remove('hidden'); 
    document.getElementById(id).classList.add('flex'); 
}

function closeModal(id){ 
    document.getElementById(id).classList.add('hidden'); 
    document.getElementById(id).classList.remove('flex'); 
}

document.getElementById('btnCreate').addEventListener('click', ()=> openModal('modalCreate'));
document.getElementById('btnImport').addEventListener('click', ()=> openModal('modalImport'));

const importSelect = document.getElementById('importDatasetSelect');
const formImport = document.getElementById('formImport');

if(importSelect){
    importSelect.addEventListener('change', function(){
        const id = this.value;
        formImport.action = id ? `/admin/dataset/import/${id}` : '';
    });
}
</script>
@endsection
