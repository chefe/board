<?php

namespace App\Policies;

use App\User;
use App\Team;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /** */
    public function edit(User $user, Team $team)
    {
        return $team->user_id == $user->id;
    }

    /** */
    public function delete(User $user, Team $team)
    {
        return $team->user_id == $user->id;
    }
}
