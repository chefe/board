<?php

namespace App\Policies;

use App\Task;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Task $task)
    {
        return $task->story->sprint->team->user_id == $user->id;
    }

    public function edit(User $user, Task $task)
    {
        return $task->story->sprint->team->user_id == $user->id;
    }

    public function delete(User $user, Task $task)
    {
        return $task->story->sprint->team->user_id == $user->id;
    }
}
