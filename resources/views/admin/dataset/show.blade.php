@extends('admin.layouts.app')

@section('title','Dataset: ' . ($dataset->name ?? 'Dataset'))

@section('content')
<div class="container mx-auto space-y-4">
    <div class="bg-white p-4 shadow rounded">
        <h3 class="font-semibold mb-2">Dataset: {{ $dataset->name }}</h3>
        <p class="text-sm text-gray-600">Rows: {{ $dataset->rows }}</p>
    </div>

    <div class="bg-white p-4 shadow rounded">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-medium">Preview Latest Items</h3>
        <div class="flex items-center space-x-2">
            <button id="save-all" class="px-3 py-1 bg-green-600 text-white rounded">Save All</button>
            <span id="save-status" class="text-sm text-gray-600"></span>
        </div>
    </div>

    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">Comment ID</th>
                <th class="p-2">Text</th>
                <th class="p-2">Label</th>
                <th class="p-2">Auto</th>
            </tr>
        </thead>
        <tbody>
        @foreach(\App\Models\DatasetItem::latest()->limit(200)->get() as $it)
            <tr class="border-t" data-item-id="{{ $it->id }}" data-original-label="{{ $it->label === null ? '' : $it->label }}" data-original-label-auto="{{ $it->label_auto ? 1 : 0 }}">
                <td class="p-2 text-xs">{{ $it->comment_id }}</td>
                <td class="p-2 text-sm break-words">{{ Str::limit($it->text, 140) }}</td>
                <td class="p-2 text-sm">
                    <select class="label-select border p-1 text-sm" data-id="{{ $it->id }}">
                        <option value="">(reset)</option>
                        <option value="0" {{ $it->label === 0 && $it->label !== null ? 'selected' : '' }}>0 - Normal</option>
                        <option value="1" {{ $it->label === 1 ? 'selected' : '' }}>1 - Promo/Judi</option>
                    </select>
                </td>
                <td class="p-2 text-sm">
                    <input type="checkbox" class="label-auto" data-id="{{ $it->id }}" {{ $it->label_auto ? 'checked' : '' }}>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

</div>
@endsection
