@extends('layouts.app')

@section('content')
<div class="grid col-span-full">
    <h1 class="mb-5">{{ __('labels.index.header') }}</h1>

    <div>
        @can('create', App\Models\Label::class)
            <a href="{{ route('labels.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('labels.index.create_button') }}
            </a>
        @endcan
    </div>

    <table class="mt-4">
        <thead class="border-b-2 border-solid border-black text-left">
            <tr>
                <th>{{ __('labels.index.id') }}</th>
                <th>{{ __('labels.index.name') }}</th>
                <th>{{ __('labels.index.description') }}</th>
                <th>{{ __('labels.index.created_at') }}</th>
                @can('seeActions', App\Models\Label::class)
                    <th>{{ __('labels.index.actions') }}</th>
                @endcan
            </tr>
        </thead>
        @foreach($labels as $label)
            <tr class="border-b border-dashed text-left">
                <td>{{ $label->id }}</td>
                <td>{{ $label->name }}</td>
                <td>{{ $label->description }}</td>
                <td>{{ $label->created_at->format('d.m.Y') }}</td>
                <td>
                    @can('delete', $label)
                        <a
                            data-confirm="{{ __('labels.index.delete_confirmation') }}"
                            data-method="delete"
                            class="text-red-600 hover:text-red-900"
                            href="{{ route('labels.destroy', $label) }}"
                        >
                            {{ __('labels.index.delete') }}
                        </a>
                    @endcan
                    @can('update', $label)
                        <a class="text-blue-600 hover:text-blue-900" href="{{ route('labels.edit', $label) }}">
                            {{ __('labels.index.edit') }}
                        </a>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>

    {{ $labels->links() }}
</div>
@endsection