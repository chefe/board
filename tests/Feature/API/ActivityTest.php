<?php

namespace Tests\Feature\API;

use App\Task;
use App\User;
use App\Story;
use DatabaseSeeder;
use Tests\TestCase;
use TaskStateSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        app(DatabaseSeeder::class)->call(TaskStateSeeder::class);
    }

    public function performActionsAsUser($user, $callback)
    {
        Auth::login($user);

        $callback();

        Auth::logout();
    }

    /** @test */
    public function a_user_can_get_his_activities()
    {
        $story = factory(Story::class)->create();
        $user = $story->sprint->team->owner;

        $this->performActionsAsUser($user, function () use ($story) {
            $task = factory(Task::class)->create([
                'story_id' => $story->id,
                'state_id' => 1,
            ]);

            $task->update([
                'caption' => 'A new task caption',
                'state_id' => 2,
            ]);

            $task->update([
                'caption' => 'A very new task caption',
            ]);
        });

        $this->actingAs($user)
            ->get(route('activity.index'))
            ->assertStatus(200)
            ->assertJson([
                'current_page' => 1,
                'data' => [
                    ['description' => 'created'],
                    ['description' => 'updated', 'changes' => [
                        'caption' => [], 'state_id' => [],
                    ]],
                    ['description' => 'updated', 'changes' => [
                        'caption' => [],
                    ]],
                ],
                'total' => 3,
            ]);
    }

    /** @test */
    public function a_user_can_get_only_his_activities()
    {
        $story = factory(Story::class)->create();
        $user = factory(User::class)->create();

        $task = factory(Task::class)->create([
            'story_id' => $story->id,
            'state_id' => 1,
        ]);

        $task->update([
            'caption' => 'A new task caption',
            'state_id' => 2,
        ]);

        $task->update([
            'caption' => 'A very new task caption',
        ]);

        $this->actingAs($user)
            ->get(route('activity.index'))
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
