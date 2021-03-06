<?php

namespace App\Policies;

use App\Sprint;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SprintPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Sprint $sprint)
    {
        return $sprint->team->user_id == $user->id;
    }

    public function edit(User $user, Sprint $sprint)
    {
        return $sprint->team->user_id == $user->id;
    }

    public function delete(User $user, Sprint $sprint)
    {
        return $sprint->team->user_id == $user->id;
    }
}
