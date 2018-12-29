<?php

namespace App\Http\Controllers\Api;

use App\Story;
use App\Task;
use App\TaskState;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    /** */
    public function index(Story $story)
    {
        return $story->tasks;
    }

    /** */
    public function store(Request $request, Story $story)
    {
        $data = $request->validate([
            'caption' => 'required|string|min:3',
            'description' => 'nullable|string|min:3',
        ]);

        return $story->tasks()->create([
            'caption' => $data['caption'],
            'description' => $data['description'],
            'state_id' => TaskState::first()->id
        ]);
    }

    /** */
    public function show(Task $task)
    {
        return $task;
    }

    /** */
    public function update(Request $request, Task $task)
    {
        $this->authorize('edit', $task);

        $data = $request->validate([
            'caption' => 'required|string|min:3',
            'description' => 'nullable|string|min:3',
            'state_id' => 'required|integer|exists:task_states,id'
        ]);

        $task->update($data);

        return $task->fresh();
    }

    /** */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();
    }
}
