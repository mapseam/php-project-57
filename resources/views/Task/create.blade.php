@extends('layouts.main')

@section('content')

<div class="grid col-span-full">
        <h1 class="mb-5 max-w-2xl text-4xl md:text-4xl xl:text-5xl">{{ __('strings.create task') }}</h1>
        <div>
            {{ html()->modelForm($task, 'POST', route('tasks.store'))->open() }}
            <div>
                {{ html()->label(__('strings.name'))->for('name') }}
            </div>
            <div class="mt-2">
                {{ html()->input('text', 'name', old('name'))->value(old('name'))->class('rounded border border-gray-300 w-1/3 p-2') }}
            </div>
            @error('name')
            <div class="text-rose-600">
                {{ $message }}
            </div>
            @enderror
            <div class="mt-2">
                {{ html()->label(__('strings.description'))->for('description') }}
            </div>
            <div class="mt-2">
                {{ html()->textarea('description', old('description'))->value(old('description'))->rows(10)->cols(50)->class('rounded border border-gray-300 w-1/3 h-32 p-2') }}

            </div>
            @error('description')
            <div class="text-rose-600">
                {{ $message }}
            </div>
            @enderror

            <div class="mt-2">
                {!! html()->label(__('strings.status'))->for('status_id') !!}
            </div>
            <div class="mt-2">
                {{ html()->select('status_id', $taskStatuses)->placeholder('----------')->class('rounded border border-gray-300 w-1/3 p-2 bg-white') }}

            </div>
            @error('status_id')
            <div class="text-rose-600">
                {{ $message }}
            </div>
            @enderror

            <div class="mt-2">
                {!! html()->label(__('strings.executor'))->for('assigned_to_id') !!}
            </div>
            <div class="mt-2">
                {{ html()->select('assigned_to_id', $users)->placeholder('----------')->class('rounded border border-gray-300 w-1/3 p-2 bg-white') }}
            </div>
            @error('assigned_to_id')
            <div class="text-rose-600">
                {{ $message }}
            </div>
            @enderror

            <div class="mt-2">
                {!! html()->label(__('strings.labels'))->for('labels[]') !!}
            </div>
            <div class="mt-2">
                {{ html()->select('labels[]', $labels)->multiple()->placeholder('')->class('rounded border border-gray-300 w-1/3 p-2 bg-white') }}
            </div>

            <div class="mt-2">
                {{ html()->submit(__('strings.create'))->class('bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded') }}
            </div>
            {{ html()->closeModelForm() }}
        </div>
    </div>

@endsection