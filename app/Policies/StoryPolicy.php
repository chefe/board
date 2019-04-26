<?php

namespace App\Policies;

use App\User;
use App\Story;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoryPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Story $story)
    {
        return $story->sprint->team->user_id == $user->id;
    }

    public function edit(User $user, Story $story)
    {
        return $story->sprint->team->user_id == $user->id;
    }

    public function delete(User $user, Story $story)
    {
        return $story->sprint->team->user_id == $user->id;
    }
}
