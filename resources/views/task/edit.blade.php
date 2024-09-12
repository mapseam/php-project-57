@extends('layouts.app')

@section('content')
<div class="grid col-span-full">
    <h1 class="mb-5">{{ __('tasks.edit.form_header') }}</h1>

    {{ Form::model($task, ['route' => ['tasks.update', $task], 'method' => 'PATCH', 'class' => 'w-50']) }}
    <div class="flex flex-col">
        <div>
            {{ Form::label('name', __('tasks.edit.labels.name')) }}
        </div>
        <div class="mt-2">
            {{ Form::text('name', null, ['class' => 'rounded border-gray-300 w-1/3']) }}
        </div>
        @error('name')
        <div class="text-rose-600">{{ $message }}</div>
        @enderror
        <div class="mt-2">
            {{ Form::label('description', __('tasks.edit.labels.description')) }}
        </div>
        <div>
        {{ Form::textarea('description', null, ['class' => 'rounded border-gray-300 w-1/3 h-32', 'cols' => 50, 'rows' => 10]) }}
        </div>
        <div class="mt-2">
            {{ Form::label('status_id', __('tasks.edit.labels.status_id')) }}
        </div>
        <div>
            {{ Form::select('status_id', $taskStatuses->pluck('name', 'id'), null, ['class' => 'rounded border-gray-300 w-1/3',  'placeholder' => '----------'] )}}
        </div>
        @error('status_id')
            <div class="text-rose-600">{{ $message }}</div>
        @enderror
        <div class="mt-2">
            {{ Form::label('assigned_to_id', __('tasks.edit.labels.assigned_to_id')) }}
        </div>
        <div>
            {{ Form::select('assigned_to_id', $users->pluck('name', 'id'), null, ['class' => 'rounded border-gray-300 w-1/3', 'placeholder' => '----------'] )}}
        </div>
        <div class="mt-2">
            {{Form::label('labels', __('tasks.edit.labels.assigned_to_id'))}}
        </div>
        <div>
            {{Form::select('labels', $labels->pluck('name', 'id'), null, ['placeholder' => '', 'multiple' => 'multiple', 'name' => 'labels[]', 'class' => 'rounded border-gray-300 w-1/3 h-32'])}}
        </div>
        <div class="mt-2">
            {{ Form::submit(__('tasks.edit.buttons.update'), ['class' => 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded']) }}
        </div>
    </div>
    {{ Form::close() }}
</div>
@endsection