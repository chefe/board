<?php

namespace Tests\Feature\API;

use App\Task;
use App\User;
use App\Story;
use DatabaseSeeder;
use Tests\TestCase;
use TaskStateSeeder;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use Tests\InteractWithBroadcasting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase, InteractWithBroadcasting;

    public function setUp(): void
    {
        parent::setUp();

        app(DatabaseSeeder::class)->call(TaskStateSeeder::class);
    }

    /** @test */
    public function a_user_can_get_the_tasks_of_his_sprints()
    {
        $story = factory(Story::class)->create();
        $user = $story->sprint->team->owner;

        factory(Task::class)->create([
            'caption' => 'Task from this story',
            'story_id' => $story->id,
        ]);

        factory(Task::class)->create([
            'caption' => 'Task from another story',
        ]);

        $this->actingAs($user)
            ->get(route('task.index', $story))
            ->assertStatus(200)
            ->assertJson([
                ['caption' => 'Task from this story'],
            ])->assertJsonMissing([
                ['caption' => 'Task from another story'],
            ]);
    }

    /** @test */
    public function a_user_can_not_get_the_tasks_from_a_foreign_story()
    {
        $story = factory(Story::class)->create();
        $user = factory(User::class)->create();

        factory(Task::class)->create([
            'story_id' => $story->id,
        ]);

        $this->actingAs($user)
            ->get(route('task.index', $story))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_get_the_details_from_a_task()
    {
        $task = factory(Task::class)->create([
            'caption' => 'The task caption',
            'description' => 'The task description',
        ]);

        $user = $task->story->sprint->team->owner;

        $this->actingAs($user)
            ->get(route('task.show', $task))
            ->assertStatus(200)
            ->assertJson([
                'caption' => 'The task caption',
                'description' => 'The task description',
            ]);
    }

    /** @test */
    public function a_user_can_not_get_the_details_from_a_foreign_task()
    {
        $task = factory(Task::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('task.show', $task))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_create_a_task()
    {
        $story = factory(Story::class)->create();
        $user = $story->sprint->team->owner;

        $validaTaskData = [
            'caption' => 'The task caption',
            'description' => 'The task description',
        ];

        $this->assertDatabaseMissing('tasks', [
            'caption' => 'The task caption',
        ]);

        $this->expectBroadcast(TaskCreated::class);

        $this->actingAs($user)
            ->postJson(route('task.store', $story), $validaTaskData)
            ->assertStatus(201)
            ->assertJson([
                'caption' => 'The task caption',
                'description' => 'The task description',
            ]);

        $this->assertDatabaseHas('tasks', [
            'caption' => 'The task caption',
        ]);
    }

    /** @test */
    public function a_user_can_not_create_a_task_for_a_foreign_story()
    {
        $story = factory(Story::class)->create();
        $user = factory(User::class)->create();

        $validaTaskData = [
            'caption' => 'The task caption',
            'description' => 'The task description',
        ];

        $this->assertDatabaseMissing('tasks', [
            'caption' => 'The task caption',
        ]);

        $this->expectNoBroadcast();

        $this->actingAs($user)
            ->postJson(route('task.store', $story), $validaTaskData)
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', [
            'caption' => 'The task caption',
        ]);
    }

    /** @test */
    public function a_user_can_not_create_a_task_with_missing_caption()
    {
        $story = factory(Story::class)->create();
        $user = $story->sprint->team->owner;

        $this->expectNoBroadcast();

        $this->actingAs($user)
            ->postJson(route('task.store', $story), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('caption');
    }

    /** @test */
    public function a_user_can_update_a_task()
    {
        $story = factory(Story::class)->create();
        $user = $story->sprint->team->owner;

        $task = factory(Task::class)->create([
            'caption' => 'Old caption',
            'description' => 'Old description',
            'state_id' => 2,
            'story_id' => $story->id,
        ]);

        $validaTaskData = [
            'caption' => 'The new caption',
            'description' => '',
            'state_id' => 1,
        ];

        $this->assertDatabaseMissing('tasks', [
            'caption' => 'The new caption',
        ]);

        $this->expectBroadcastWithId(TaskUpdated::class, $task->id, 'task');

        $this->actingAs($user)
            ->putJson(route('task.update', $task), $validaTaskData)
            ->assertStatus(200)
            ->assertJson([
                'caption' => 'The new caption',
                'description' => null,
                'state_id' => 1,
            ]);

        $this->assertDatabaseHas('tasks', [
            'caption' => 'The new caption',
        ]);
    }

    /** @test */
    public function a_user_can_not_update_a_foreign_task()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create([
            'caption' => 'The old caption',
        ]);

        $validaTaskData = [
            'caption' => 'The new caption',
            'description' => '',
        ];

        $this->assertDatabaseHas('tasks', [
            'caption' => 'The old caption',
        ]);

        $this->expectNoBroadcast();

        $this->actingAs($user)
            ->putJson(route('task.update', $task), $validaTaskData)
            ->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'caption' => 'The old caption',
        ]);
    }

    /** @test */
    public function a_user_can_delete_a_task()
    {
        $task = factory(Task::class)->create([
            'caption' => 'Task to delete',
        ]);

        $user = $task->story->sprint->team->owner;

        $this->assertDatabaseHas('tasks', [
            'caption' => 'Task to delete',
        ]);

        $this->expectBroadcastWithId(TaskDeleted::class, $task->id, 'task');

        $this->actingAs($user)
            ->delete(route('task.destroy', $task))
            ->assertStatus(200);

        $this->assertDatabaseMissing('tasks', [
            'caption' => 'Task to delete',
        ]);
    }

    /** @test */
    public function a_user_can_not_delete_a_foreign_task()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create([
            'caption' => 'Task to delete',
        ]);

        $this->assertDatabaseHas('tasks', [
            'caption' => 'Task to delete',
        ]);

        $this->expectNoBroadcast();

        $this->actingAs($user)
            ->delete(route('task.destroy', $task))
            ->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'caption' => 'Task to delete',
        ]);
    }
}
