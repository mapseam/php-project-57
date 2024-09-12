<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = QueryBuilder::for(Task::class)
        ->allowedFilters([
            'name',
            AllowedFilter::exact('status_id'),
            AllowedFilter::exact('created_by_id'),
            AllowedFilter::exact('assigned_to_id'),
        ])
        ->orderBy('id')
        ->paginate(15);

        $taskStatusesForFilter = TaskStatus::pluck('name', 'id');
        $usersForFilter = User::pluck('name', 'id');
        $filter = $request->input('filter');
        
        return view('task.index', compact(
            'tasks',
            'taskStatusesForFilter',
            'usersForFilter',
            'filter'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $task = new Task();
        $taskStatuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();
        
        return view('task.create', compact(
            'task',
            'taskStatuses',
            'users',
            'labels'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $this->validate(
            $request,
            [
                'name' => 'required|unique:tasks',
                'status_id' => 'required|exists:task_statuses,id',
                'description' => 'nullable|string',
                'assigned_to_id' => 'nullable|integer',
                'label' => 'nullable|array',
            ],
            [
                'name.unique' => __('tasks.validation.unique')
            ]
        );

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $task = $currentUser->createdTasks()->create($validated);

        $labels = collect($request->input('labels'))->whereNotNull();
        $task->save();

        if ($labels->isNotEmpty()) {
            $task->labels()->attach($labels);
        }

        flash(__('flashes.tasks.store'))->success();

        return redirect()->route('tasks.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return view('task.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $taskStatuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();
        
        return view('task.edit', compact(
            'task',
            'taskStatuses',
            'users',
            'labels'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $validated = $this->validate(
            $request,
            [
                'name' => 'required|unique:tasks,name,' . $task->id,
                'description' => 'nullable|string',
                'assigned_to_id' => 'nullable|integer',
                'status_id' => 'required|integer',
                'label' => 'nullable|array',
            ],
            [
                'name.unique' => __('tasks.validation.unique')
            ]
        );

        $labels = collect($request->input('labels'));

        $task->fill($validated);
        $task->save();

        $task->labels()->sync($labels);

        flash(__('flashes.tasks.updated'))->success();

        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->labels()->detach();
        $task->delete();
        flash(__('flashes.tasks.deleted'))->success();

        return redirect()->route('tasks.index');
    }
}
