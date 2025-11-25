@extends('admin.layouts.app')

@section('title','Model LSTM')

@section('content')
<div class="container mx-auto">
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-4 text-lg">Model LSTM</h3>

        <p>Model path: <strong>{{ $modelFilePath ?? '-' }}</strong></p>
        <p>Model exists: <strong>{{ $modelFilePath ? 'Yes' : 'No' }}</strong></p>
        <p>Train script exists: <strong>{{ $trainFilePath ? 'Yes' : 'No' }}</strong></p>

        @if(!empty($modelModified))
            <p>Model modified: {{ date('Y-m-d H:i:s', (int)$modelModified) }}</p>
        @endif
        @if(!empty($trainModified))
            <p>Train script modified: {{ date('Y-m-d H:i:s', (int)$trainModified) }}</p>
        @endif

        <hr class="my-4">

        {{-- Upload model / script --}}
        <form action="{{ route('admin.model.upload') }}" method="POST" enctype="multipart/form-data" class="mb-4">
            @csrf
            <label class="block mb-1 font-medium">Upload model / script (.h5 / .py / lain)</label>
            <input type="file" name="model_file" accept="*" class="mb-2 border px-2 py-1 rounded w-full md:w-1/2">
            <button class="px-4 py-2 bg-green-600 text-white rounded">Upload</button>
        </form>

        {{-- Trigger training via FastAPI --}}
        <form method="POST" action="{{ route('admin.model.triggerTrain') }}" class="mt-2 flex flex-wrap items-end gap-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Dataset ID</label>
                <input type="number" name="dataset_id" value="{{ old('dataset_id', 3) }}"
                    class="border px-2 py-1 rounded w-24">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Epochs</label>
                <input type="number" name="epochs" value="{{ old('epochs', 3) }}"
                    class="border px-2 py-1 rounded w-24">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Max words</label>
                <input type="number" name="max_words" value="{{ old('max_words', 20000) }}"
                    class="border px-2 py-1 rounded w-32">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Maxlen</label>
                <input type="number" name="maxlen" value="{{ old('maxlen', 200) }}"
                    class="border px-2 py-1 rounded w-24">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Model name prefix</label>
                <input type="text" name="model_name_prefix" value="{{ old('model_name_prefix', 'admin_run') }}"
                    class="border px-2 py-1 rounded w-32">
            </div>

            <div>
                <button class="px-4 py-2 bg-yellow-500 text-white rounded">
                    Train Model (FastAPI)
                </button>
            </div>
        </form>

        <div class="mt-4">
            <a href="{{ route('admin.model.download') }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded inline-block">
                Download latest model
            </a>
        </div>
    </div>
</div>
@endsection
