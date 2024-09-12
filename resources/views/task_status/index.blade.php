@extends('layouts.app')

@section('content')
<div class="grid col-span-full">
    <h1 class="mb-5">{{ __('task_statuses.index.header') }}</h1>

    <div>
        @can('create', App\Models\TaskStatus::class)
            <a href="{{ route('task_statuses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('task_statuses.index.create_button') }}
            </a>
        @endcan
    </div>

    <table class="mt-4">
        <thead class="border-b-2 border-solid border-black text-left">
            <tr>
                <th>{{ __('task_statuses.index.id') }}</th>
                <th>{{ __('task_statuses.index.name') }}</th>
                <th>{{ __('task_statuses.index.created_at') }}</th>
                @can('seeActions', App\Models\TaskStatus::class)
                    <th>{{ __('task_statuses.index.actions') }}</th>
                @endcan
            </tr>
        </thead>
        @foreach($taskStatuses as $status)
            <tr class="border-b border-dashed text-left">
                <td>{{ $status->id }}</td>
                <td>{{ $status->name }}</td>
                <td>{{ $status->created_at->format('d.m.Y') }}</td>
                <td>
                    @can('delete', $status)
                        <a
                            data-confirm="{{ __('task_statuses.index.delete_confirmation') }}"
                            data-method="delete"
                            class="text-red-600 hover:text-red-900"
                            href="{{ route('task_statuses.destroy', $status) }}">
                            {{ __('task_statuses.index.delete') }}
                        </a>
                    @endcan
                    @can('update', $status)
                        <a class="text-blue-600 hover:text-blue-900" href="{{ route('task_statuses.edit', $status) }}">
                            {{ __('task_statuses.index.edit') }}
                        </a>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
    {{ $taskStatuses->links() }}
</div>
@endsection