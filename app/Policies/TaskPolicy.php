<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /** */
    public function edit()
    {
        return true;
    }

    /** */
    public function delete()
    {
        return true;
    }
}
