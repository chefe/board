<?php

namespace App\Http\Controllers\Api;

use App\Sprint;
use App\TaskState;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BoardController extends Controller
{
    /** */
    public function show(Sprint $sprint)
    {
        $this->authorize('view', $sprint);

        return [
            'states' => TaskState::get(),
            'sprint' => $sprint->toArray(),
            'team' => $sprint->team,
            'stories' => $sprint->stories,
            'tasks' => $sprint->tasks,
        ];
    }
}
