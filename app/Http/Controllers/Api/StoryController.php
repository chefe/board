<?php

namespace App\Http\Controllers\Api;

use App\Story;
use App\Sprint;
use App\Events\StoryCreated;
use App\Events\StoryUpdated;
use App\Events\StoryDeleted;
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

        $story = $sprint->stories()->create($data);

        broadcast(new StoryCreated($story));
        return $story;
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
        $story = $story->fresh();

        broadcast(new StoryUpdated($story));
        return $story;
    }

    /** */
    public function destroy(Story $story)
    {
        $this->authorize('delete', $story);

        broadcast(new StoryDeleted($story));
        $story->delete();
    }
}
