<?php

namespace Tests\Feature\API;

use App\Team;
use App\User;
use App\Sprint;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SprintTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->team = factory(Team::class)->create([
            'user_id' => $this->user->id,
            'caption' => 'Own Team',
        ]);
        $this->sprint = factory(Sprint::class)->create([
            'team_id' => $this->team->id,
            'caption' => 'A first sprint',
        ]);
    }

    /** @test */
    public function a_user_can_get_the_sprints_of_his_teams()
    {
        factory(Sprint::class)->create([
            'caption' => 'A second sprint',
            'team_id' => $this->team->id,
        ]);

        factory(Sprint::class)->create([
            'caption' => 'A sprint from another team',
        ]);

        $this->actingAs($this->user)
            ->get(route('sprint.index', $this->team))
            ->assertStatus(200)
            ->assertJson([
                ['caption' => 'A first sprint'],
                ['caption' => 'A second sprint'],
            ])->assertJsonMissing([
                ['caption' => 'A sprint from another team'],
            ]);
    }

    /** @test */
    public function a_user_can_get_only_the_sprints_of_his_teams()
    {
        $otherTeam = factory(Team::class)->create();

        $this->actingAs($this->user)
            ->get(route('sprint.index', $otherTeam))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_create_a_sprint_for_his_team()
    {
        $this->assertDatabaseMissing('sprints', [
            'caption' => 'A new sprint',
        ]);

        $this->actingAs($this->user)
            ->post(route('sprint.store', $this->team), [
                'caption' => 'A new sprint',
                'start' => '2019-01-01',
                'end' => '2019-01-14',
            ])->assertStatus(201)
            ->assertJson([
                'caption' => 'A new sprint',
                'start' => '2019-01-01 00:00:00',
                'end' => '2019-01-14 00:00:00',
            ]);

        $this->assertDatabaseHas('sprints', [
            'caption' => 'A new sprint',
        ]);
    }

    /** @test */
    public function a_user_can_not_create_a_sprint_for_another_team()
    {
        $otherTeam = factory(Team::class)->create();

        $validSprintData = [
            'caption' => 'A new sprint',
            'start' => '2019-01-01',
            'end' => '2019-01-14',
        ];

        $this->assertDatabaseMissing('sprints', [
            'caption' => 'A new sprint',
        ]);

        $this->actingAs($this->user)
            ->post(route('sprint.store', $otherTeam), $validSprintData)
            ->assertStatus(403);

        $this->assertDatabaseMissing('sprints', [
            'caption' => 'A new sprint',
        ]);
    }

    /** @test */
    public function a_user_can_not_create_a_sprint_with_invalid_end_date()
    {
        $invalidSprintData = [
            'caption' => 'A new sprint',
            'start' => '2019-01-14',
            'end' => '2019-01-01',
        ];

        $this->actingAs($this->user)
            ->postJson(route('sprint.store', $this->team), $invalidSprintData)
            ->assertStatus(422)
            ->assertJsonValidationErrors('end');
    }

    /** @test */
    public function a_user_can_get_the_details_of_a_sprint()
    {
        $this->actingAs($this->user)
            ->get(route('sprint.show', $this->sprint))
            ->assertStatus(200)
            ->assertJson([
                'caption' => 'A first sprint',
            ]);
    }

    /** @test */
    public function a_user_can_only_get_the_details_of_a_sprint()
    {
        $otherSprint = factory(Sprint::class)->create();

        $this->actingAs($this->user)
            ->get(route('sprint.show', $otherSprint))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_update_his_sprints()
    {
        $validSprintData = [
            'caption' => 'Updated caption',
            'start' => '2019-01-01',
            'end' => '2019-01-14',
        ];

        $this->assertDatabaseMissing('sprints', [
            'caption' => 'Updated caption',
        ]);

        $this->actingAs($this->user)
            ->putJson(route('sprint.update', $this->sprint), $validSprintData)
            ->assertStatus(200)
            ->assertJson([
                'caption' => 'Updated caption',
                'start' => '2019-01-01 00:00:00',
                'end' => '2019-01-14 00:00:00',
            ]);

        $this->assertDatabaseHas('sprints', [
            'caption' => 'Updated caption',
        ]);
    }

    /** @test */
    public function a_user_can_update_only_his_sprints()
    {
        $otherSprint = factory(Sprint::class)->create();

        $validSprintData = [
            'caption' => 'Updated caption',
            'start' => '2019-01-01',
            'end' => '2019-01-14',
        ];

        $this->actingAs($this->user)
            ->putJson(route('sprint.update', $otherSprint), $validSprintData)
            ->assertStatus(403);

        $this->assertDatabaseMissing('sprints', [
            'caption' => 'Updated caption',
        ]);
    }

    /** @test */
    public function a_user_can_update_a_sprint_only_with_valid_data()
    {
        $invalidSprintData = [
            'caption' => '',
            'start' => '2019-01-14',
            'end' => '2019-01-01',
        ];

        $this->assertDatabaseMissing('sprints', [
            'caption' => 'Updated caption',
        ]);

        $this->actingAs($this->user)
            ->putJson(route('sprint.update', $this->sprint), $invalidSprintData)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['caption', 'end']);

        $this->assertDatabaseMissing('sprints', [
            'caption' => 'Updated caption',
        ]);
    }

    /** @test */
    public function a_user_can_delete_his_sprints()
    {
        $this->assertDatabaseHas('sprints', [
            'caption' => 'A first sprint',
        ]);

        $this->actingAs($this->user)
            ->delete(route('sprint.destroy', $this->sprint))
            ->assertStatus(200);

        $this->assertDatabaseMissing('sprints', [
            'caption' => 'A first sprint',
        ]);
    }

    /** @test */
    public function a_user_can_only_delete_his_sprints()
    {
        $otherSprint = factory(Sprint::class)->create([
            'caption' => 'A sprint to delete',
        ]);

        $this->assertDatabaseHas('sprints', [
            'caption' => 'A sprint to delete',
        ]);

        $this->actingAs($this->user)
            ->delete(route('sprint.destroy', $otherSprint))
            ->assertStatus(403);

        $this->assertDatabaseHas('sprints', [
            'caption' => 'A sprint to delete',
        ]);
    }
}
