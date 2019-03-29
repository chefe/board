<?php

namespace App\Http\Controllers\Api;

use App\TaskState;
use App\Http\Controllers\Controller;

class TaskStateController extends Controller
{
    public function index()
    {
        return TaskState::get();
    }
}
