<?php

namespace Tests\Feature\API;

use App\Sprint;
use App\Story;
use App\Task;
use App\TaskState;
use App\Team;
use App\User;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TaskStateSeeder;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        app(DatabaseSeeder::class)->call(TaskStateSeeder::class);
    }

    /** @test */
    public function a_user_can_get_the_board_details_from_his_sprints()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['user_id' => $user->id]);
        $sprint = factory(Sprint::class)->create(['team_id' => $team->id]);
        $stories = factory(Story::class, 3)->create(['sprint_id' => $sprint->id]);
        $tasksOfFirstStory = factory(Task::class, 2)->create(['story_id' => $stories[0]->id]);
        $tasksOfSecondStory = factory(Task::class, 3)->create(['story_id' => $stories[1]->id]);

        $this->actingAs($user)
            ->get(route('board.index', $sprint))
            ->assertStatus(200)
            ->assertJsonCount(TaskState::count(), 'states')
            ->assertJson([
                'sprint' => [
                    'caption' => $sprint->caption,
                ],
                'team' => [
                    'caption' => $team->caption,
                ],
                'stories' => [
                    ['caption' => $stories[0]->caption],
                    ['caption' => $stories[1]->caption],
                    ['caption' => $stories[2]->caption],
                ],
                'tasks' => [
                    ['caption' => $tasksOfFirstStory[0]->caption],
                    ['caption' => $tasksOfFirstStory[1]->caption],
                    ['caption' => $tasksOfSecondStory[0]->caption],
                    ['caption' => $tasksOfSecondStory[1]->caption],
                    ['caption' => $tasksOfSecondStory[2]->caption],
                ],
            ]);
    }

    /** @test */
    public function the_board_details_do_not_include_wrong_stories_and_tasks()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['user_id' => $user->id]);
        $sprint = factory(Sprint::class)->create(['team_id' => $team->id]);
        $story = factory(Story::class)->create();
        $task = factory(Task::class)->create();

        $this->actingAs($user)
            ->get(route('board.index', $sprint))
            ->assertStatus(200)
            ->assertJson([
                'sprint' => [
                    'caption' => $sprint->caption,
                ],
                'team' => [
                    'caption' => $team->caption,
                ],
            ])->assertJsonMissing([
                'stories' => [
                    ['caption' => $story->caption],
                ],
                'tasks' => [
                    ['caption' => $task->caption],
                ],
            ]);
    }

    /** @test */
    public function a_user_can_not_get_the_board_details_from_a_foreign_sprint()
    {
        $user = factory(User::class)->create();
        $sprint = factory(Sprint::class)->create();

        $this->actingAs($user)
            ->get(route('board.index', $sprint))
            ->assertStatus(403);
    }
}
