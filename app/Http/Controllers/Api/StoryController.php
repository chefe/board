<?php

namespace App\Http\Controllers\Api;

use App\Story;
use App\Sprint;
use App\Events\StoryCreated;
use App\Events\StoryUpdated;
use App\Events\StoryDeleted;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class StoryController extends Controller
{
    /** */
    public function index(Sprint $sprint)
    {
        $this->authorize('view', $sprint);

        return $sprint->stories;
    }

    /** */
    public function store(Request $request, Sprint $sprint)
    {
        $this->authorize('edit', $sprint);

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
        $this->authorize('view', $story);

        return $story;
    }

    /** */
    public function update(Request $request, Story $story)
    {
        $this->authorize('edit', $story);

        $sprintIdRule = Rule::in(
            $story
                ->sprint
                ->team
                ->sprints()
                ->pluck('id')
        );

        $data = $request->validate([
            'caption' => 'required|string|min:3',
            'description' => 'nullable|string',
            'points' => 'nullable|integer|min:0',
            'sprint_id' => ['integer', $sprintIdRule]
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
