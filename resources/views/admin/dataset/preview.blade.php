@extends('admin.layouts.app')

@section('title','Preview Dataset')

@section('content')
<div class="container mx-auto">

    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-bold text-lg mb-3">
            Preview: {{ $dataset->name }}
        </h2>

        <table class="w-full text-left border">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-2">ID</th>
                    <th class="p-2">Text</th>
                    <th class="p-2">Label</th>
                </tr>
            </thead>

            <tbody>
                @foreach($items as $i)
                <tr class="border-b">
                    <td class="p-2">{{ $i->id }}</td>
                    <td class="p-2">{{ $i->text }}</td>
                    <td class="p-2">{{ $i->label }}</td>
                </tr>
                @endforeach

                @if(count($items) == 0)
                <tr>
                    <td colspan="3" class="p-4 text-center text-gray-500">
                        Tidak ada data.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <a href="{{ route('admin.dataset.index') }}"
           class="inline-block mt-4 px-4 py-2 bg-gray-700 text-white rounded">
           Back
        </a>
    </div>

</div>
@endsection
