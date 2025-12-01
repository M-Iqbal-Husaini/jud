@extends('admin.layouts.app')

@section('title', 'Model LSTM')

@section('content')
<div class="max-w-5xl mx-auto space-y-4">

    @if(session('error'))
        <div class="mb-2 flex items-center bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-2 flex items-center bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white px-5 py-4 rounded-xl shadow-sm border border-gray-100 space-y-4">
        <h3 class="font-semibold text-lg text-gray-900 mb-2">Model LSTM</h3>

        {{-- Info file --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">
            <div>
                <p>Model path:</p>
                <p class="font-mono text-xs bg-gray-50 border border-gray-100 rounded px-2 py-1 mt-1">
                    {{ $modelFilePath ?? '-' }}
                </p>
            </div>
            <div>
                <p>Status file:</p>
                <ul class="mt-1 text-xs space-y-1">
                    <li>
                        <span class="font-medium">Model:</span>
                        {{ $modelFilePath ? 'Ada' : 'Tidak ada' }}
                    </li>
                    @if(!empty($modelModified))
                        <li>
                            <span class="font-medium">Model modified:</span>
                            {{ date('Y-m-d H:i:s', (int) $modelModified) }}
                        </li>
                    @endif
                    @if(!empty($trainModified))
                        <li>
                            <span class="font-medium">Train script modified:</span>
                            {{ date('Y-m-d H:i:s', (int) $trainModified) }}
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <hr class="my-3">

        {{-- Trigger training via FastAPI --}}
        <div class="space-y-2">
            <h4 class="font-semibold text-sm text-gray-800">Training via FastAPI</h4>

            <form method="POST"
                  action="{{ route('admin.model.triggerTrain') }}"
                  class="flex flex-wrap items-end gap-4 text-sm">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Dataset ID</label>
                    <input type="number" name="dataset_id"
                           value="{{ old('dataset_id', 3) }}"
                           class="border border-gray-300 px-2 py-1.5 rounded w-24 focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Epochs</label>
                    <input type="number" name="epochs"
                           value="{{ old('epochs', 3) }}"
                           class="border border-gray-300 px-2 py-1.5 rounded w-24 focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Max words</label>
                    <input type="number" name="max_words"
                           value="{{ old('max_words', 20000) }}"
                           class="border border-gray-300 px-2 py-1.5 rounded w-28 focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maxlen</label>
                    <input type="number" name="maxlen"
                           value="{{ old('maxlen', 200) }}"
                           class="border border-gray-300 px-2 py-1.5 rounded w-24 focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Model name prefix</label>
                    <input type="text" name="model_name_prefix"
                           value="{{ old('model_name_prefix', 'admin_run') }}"
                           class="border border-gray-300 px-2 py-1.5 rounded w-32 focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <button class="px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600">
                        Train Model
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.model.download') }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700">
                <i class="fas fa-download mr-2"></i>
                Download latest model
            </a>
        </div>
    </div>
</div>
@endsection
