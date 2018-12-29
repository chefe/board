<?php

namespace App\Http\Controllers\Api;

use App\Team;
use App\Sprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SprintController extends Controller
{
    /** */
    public function index(Team $team)
    {
        return $team->sprints;
    }

    /** */
    public function store(Request $request, Team $team)
    {
        $data = $request->validate([
            'caption' => 'required|string|min:3',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        return $team->sprints()->create($data);
    }

    /** */
    public function show(Sprint $sprint)
    {
        return $sprint;
    }

    /** */
    public function update(Request $request, Sprint $sprint)
    {
        $this->authorize('edit', $sprint);

        $data = $request->validate([
            'caption' => 'required|string|min:3',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        $sprint->update($data);

        return $sprint->fresh();
    }

    /** */
    public function destroy(Sprint $sprint)
    {
        $this->authorize('delete', $sprint);

        $sprint->delete();
    }
}
