@extends('layouts.main')

@section('content')

<div class="grid col-span-full">
        <h1 class="mb-5 max-w-2xl text-4xl md:text-4xl xl:text-5xl">{{ __('strings.edit status') }}</h1>
        <div>
            {{ html()->modelForm($taskStatus, 'PATCH', route('task_statuses.update', $taskStatus))->open() }}
            <div>
                {{ html()->label(__('strings.name'))->for('name') }}
            </div>
            <div class="mt-2">
                {{ html()->input('text', 'name', $taskStatus->name)->value(old('name'))->class('rounded border border-gray-300 w-1/3 p-2') }}
            </div>
            @error('name')
            <div class="text-rose-600">
                {{ $message }}
            </div>
            @enderror
            <div class="mt-2">
                {{ html()->submit(__('strings.update'))->class('bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded') }}
            </div>
            {{ html()->closeModelForm() }}
        </div>
    </div>

@endsection