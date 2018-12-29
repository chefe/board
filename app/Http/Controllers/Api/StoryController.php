<?php

namespace App\Http\Controllers\Api;

use App\Story;
use App\Sprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoryController extends Controller
{
    /** */
    public function index(Sprint $sprint)
    {
        return $sprint->stories;
    }

    /** */
    public function store(Request $request, Sprint $sprint)
    {
        $data = $request->validate([
            'caption' => 'required|string|min:3',
            'description' => 'nullable|string',
            'points' => 'nullable|integer|min:0',
        ]);

        return $sprint->stories()->create($data);
    }

    /** */
    public function show(Story $story)
    {
        return $story;
    }

    /** */
    public function update(Request $request, Story $story)
    {
        $this->authorize('edit', $story);

        $data = $request->validate([
            'caption' => 'required|string|min:3',
            'description' => 'nullable|string',
            'points' => 'nullable|integer|min:0',
        ]);

        $story->update($data);

        return $story->fresh();
    }

    /** */
    public function destroy(Story $story)
    {
        $this->authorize('delete', $story);

        $story->delete();
    }
}
