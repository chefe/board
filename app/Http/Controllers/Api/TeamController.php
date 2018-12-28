<?php

namespace App\Http\Controllers\Api;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /** */
    public function index()
    {
        return Team::get();
    }

    /** */
    public function store(Request $request)
    {
        $data = $request->validate([
            'caption' => 'required|string|min:3'
        ]);

        return Team::create([
            'caption' => $data['caption'],
            'user_id' => Auth::id(),
        ]);
    }

    /** */
    public function show(Team $team)
    {
        return $team;
    }

    /** */
    public function update(Request $request, Team $team)
    {
        $this->authorize('edit', $team);

        $data = $request->validate([
            'caption' => 'required|string|min:3'
        ]);

        $team->update([
            'caption' => $data['caption'],
        ]);

        return $team->fresh();
    }

    /** */
    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);

        $team->delete();
    }
}
