@extends('admin.layouts.app')

@section('title', 'Dataset: ' . ($dataset->name ?? 'Dataset'))

@section('content')
<div class="max-w-6xl mx-auto space-y-4">

    {{-- Header --}}
    <div class="bg-white px-5 py-4 shadow-sm rounded-xl border border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="font-semibold text-lg text-gray-900">
                Dataset: {{ $dataset->name }}
            </h3>
            <p class="text-sm text-gray-600">
                Total baris: {{ $dataset->rows }}
            </p>
        </div>

        <a href="{{ route('admin.dataset.index') }}"
           class="inline-flex items-center px-4 py-2 bg-white border border-red-500 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke daftar dataset
        </a>
    </div>

    {{-- Preview table (readonly) --}}
    <div class="bg-white px-5 py-4 shadow-sm rounded-xl border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-medium text-gray-900">Preview Data</h3>
            <p class="text-xs text-gray-500">
                Menampilkan 20 baris per halaman (data terbaru terlebih dahulu).
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b">
                        <th class="p-2 font-medium">Teks</th>
                        <th class="p-2 font-medium w-40">Label</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $it)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="p-2 text-sm break-words">
                                {{ \Illuminate\Support\Str::limit($it->text, 160) }}
                            </td>
                            <td class="p-2 text-sm">
                                @php
                                    $lbl = is_null($it->label) ? null : (int) $it->label;
                                @endphp

                                @if($lbl === 1)
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        1 • Promo/Judi
                                    </span>
                                @elseif($lbl === 0)
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        0 • Normal
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        Belum diberi label
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">
                                Belum ada data pada dataset ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $items->onEachSide(1)->links() }}
        </div>
    </div>
</div>
@endsection
