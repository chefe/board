<?php

namespace App\Http\Controllers\Api;

use App\Task;
use App\Story;
use App\TaskState;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function index(Story $story)
    {
        $this->authorize('view', $story);

        return $story->tasks;
    }

    public function store(Request $request, Story $story)
    {
        $this->authorize('edit', $story);

        $data = $request->validate([
            'caption' => 'required|string|min:3',
            'description' => 'nullable|string|min:3',
        ]);

        $task = $story->tasks()->create([
            'caption' => $data['caption'],
            'description' => $data['description'],
            'state_id' => TaskState::first()->id,
        ]);

        broadcast(new TaskCreated($task));

        return $task;
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return $task;
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('edit', $task);

        $data = $request->validate([
            'caption' => 'string|min:3',
            'description' => 'nullable|string|min:3',
            'state_id' => 'integer|exists:task_states,id',
        ]);

        $task->update($data);
        $task = $task->fresh();

        broadcast(new TaskUpdated($task));

        return $task;
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        broadcast(new TaskDeleted($task));
        $task->delete();
    }
}
