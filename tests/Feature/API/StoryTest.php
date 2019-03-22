<?php

namespace Tests\Feature\API;

use App\User;
use App\Team;
use App\Story;
use App\Sprint;
use Tests\TestCase;
use App\Events\StoryDeleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use App\Events\StoryCreated;
use App\Events\StoryUpdated;
use Tests\InteractWithBroadcasting;

class StoryTest extends TestCase
{
    use RefreshDatabase, InteractWithBroadcasting;

    /** */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->team = factory(Team::class)->create([
            'user_id' => $this->user->id,
            'caption' => 'Own Team'
        ]);
        $this->sprint = factory(Sprint::class)->create([
            'team_id' => $this->team->id,
            'caption' => 'A first sprint'
        ]);
    }

    /** @test */
    public function a_user_can_get_the_stories_of_his_teams()
    {
        factory(Story::class)->create([
            'caption' => 'Story #1',
            'sprint_id' => $this->sprint->id
        ]);

        factory(Story::class)->create([
            'caption' => 'Story #2',
            'sprint_id' => $this->sprint->id
        ]);

        $this->actingAs($this->user)
            ->get(route('story.index', $this->sprint))
            ->assertStatus(200)
            ->assertJson([
                ['caption' => 'Story #1'],
                ['caption' => 'Story #2'],
            ]);
    }

    /** @test */
    public function a_user_can_only_see_the_stories_of_his_teams()
    {
        factory(Story::class)->create([
            'caption' => 'Story - Own Team',
            'sprint_id' => $this->sprint->id
        ]);

        factory(Story::class)->create([
            'caption' => 'Story - Another Team',
        ]);

        $this->actingAs($this->user)
            ->get(route('story.index', $this->sprint))
            ->assertStatus(200)
            ->assertJson([
                ['caption' => 'Story - Own Team']
            ])->assertJsonMissing([
                ['caption' => 'Story - Another Team'],
            ]);
    }

    /** @test */
    public function a_user_can_not_see_the_stories_from_another_team()
    {
        $otherSprint = factory(Sprint::class)->create();

        factory(Story::class)->create([
            'caption' => 'Story - Own Team',
            'sprint_id' => $otherSprint->id
        ]);

        $this->actingAs($this->user)
            ->get(route('story.index', $otherSprint))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_create_a_story()
    {
        $validStoryData = [
            'caption' => 'A new story',
            'description' => 'Lorem ipsum ...',
            'points' => 10
        ];

        $this->expectBroadcast(StoryCreated::class);

        $this->assertDatabaseMissing('stories', [
            'caption' => 'A new story',
        ]);

        $this->actingAs($this->user)
            ->postJson(route('story.store', $this->sprint), $validStoryData)
            ->assertStatus(201)
            ->assertJson([
                'caption' => 'A new story',
                'description' => 'Lorem ipsum ...',
                'points' => 10
            ]);

        $this->assertDatabaseHas('stories', [
            'caption' => 'A new story',
        ]);
    }

    /** @test */
    public function a_user_can_only_create_stories_for_his_sprints()
    {
        $otherSprint = factory(Sprint::class)->create();

        $validStoryData = [
            'caption' => 'A new story',
            'description' => 'Lorem ipsum ...',
            'points' => 10
        ];

        $this->expectNoBroadcast();

        $this->assertDatabaseMissing('stories', [
            'caption' => 'A new story',
        ]);

        $this->actingAs($this->user)
            ->postJson(route('story.store', $otherSprint), $validStoryData)
            ->assertStatus(403);

        $this->assertDatabaseMissing('stories', [
            'caption' => 'A new story',
        ]);
    }

    /** @test */
    public function a_user_can_create_a_story_only_with_a_caption()
    {
        $validStoryData = [
            'caption' => 'A new story',
        ];

        $this->expectBroadcast(StoryCreated::class);

        $this->assertDatabaseMissing('stories', [
            'caption' => 'A new story',
        ]);

        $this->actingAs($this->user)
            ->postJson(route('story.store', $this->sprint), $validStoryData)
            ->assertStatus(201)
            ->assertJson([
                'caption' => 'A new story',
            ]);

        $this->assertDatabaseHas('stories', [
            'caption' => 'A new story',
        ]);
    }

    /** @test */
    public function a_user_can_get_the_details_about_a_story()
    {
        $story = factory(Story::class)->create([
            'caption' => 'Story #1',
            'sprint_id' => $this->sprint->id
        ]);

        $this->actingAs($this->user)
            ->get(route('story.show', $story->id))
            ->assertStatus(200)
            ->assertJson([
                'caption' => 'Story #1',
            ]);
    }

    /** @test */
    public function a_user_can_not_get_the_details_from_a_foreign_story()
    {
        $story = factory(Story::class)->create([
            'caption' => 'Story #1',
        ]);

        $this->actingAs($this->user)
            ->get(route('story.show', $story->id))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_update_a_story()
    {
        $story = factory(Story::class)->create([
            'caption' => 'Story2Update',
            'sprint_id' => $this->sprint->id
        ]);

        $validStoryData = [
            'caption' => 'Story Updated'
        ];

        $this->expectBroadcastWithId(StoryUpdated::class, $story->id, 'story');

        $this->assertDatabaseHas('stories', [
            'id' => $story->id,
            'caption' => 'Story2Update',
        ]);

        $this->actingAs($this->user)
            ->putJson(route('story.update', $story->id), $validStoryData)
            ->assertStatus(200)
            ->assertJson([
                'caption' => 'Story Updated'
            ]);

        $this->assertDatabaseHas('stories', [
            'id' => $story->id,
            'caption' => 'Story Updated',
        ]);
    }

    /** @test */
    public function a_user_can_not_update_a_foreign_story()
    {
        $story = factory(Story::class)->create([
            'caption' => 'Story2Update',
        ]);

        $validStoryData = [
            'caption' => 'Story Updated'
        ];

        $this->expectNoBroadcast();

        $this->assertDatabaseHas('stories', [
            'id' => $story->id,
            'caption' => 'Story2Update',
        ]);

        $this->actingAs($this->user)
            ->putJson(route('story.update', $story->id), $validStoryData)
            ->assertStatus(403);

        $this->assertDatabaseHas('stories', [
            'id' => $story->id,
            'caption' => 'Story2Update',
        ]);
    }

    /** @test */
    public function a_user_can_delete_a_story()
    {
        $story = factory(Story::class)->create([
            'caption' => 'Story2Delete',
            'sprint_id' => $this->sprint->id
        ]);

        $this->expectBroadcastWithId(StoryDeleted::class, $story->id, 'story');

        $this->assertDatabaseHas('stories', [
            'caption' => 'Story2Delete',
        ]);

        $this->actingAs($this->user)
            ->delete(route('story.destroy', $story->id))
            ->assertStatus(200);

        $this->assertDatabaseMissing('stories', [
            'caption' => 'Story2Delete',
        ]);
    }

    /** @test */
    public function a_user_can_not_delete_foreign_stories()
    {
        $story = factory(Story::class)->create([
            'caption' => 'Story2Delete',
        ]);

        $this->expectNoBroadcast();

        $this->assertDatabaseHas('stories', [
            'caption' => 'Story2Delete',
        ]);

        $this->actingAs($this->user)
            ->delete(route('story.destroy', $story->id))
            ->assertStatus(403);

        $this->assertDatabasehas('stories', [
            'caption' => 'Story2Delete',
        ]);
    }
}
