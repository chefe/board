<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\TaskState;

class TaskStateController extends Controller
{
    public function index()
    {
        return TaskState::get();
    }
}
