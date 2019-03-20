<?php

namespace Tests\Feature\API;

use App\User;
use App\Team;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_get_his_teams()
    {
        $user = factory(User::class)->create();
        factory(Team::class)->create(['caption' => 'Team 1', 'user_id' => $user->id]);
        factory(Team::class)->create(['caption' => 'Team 2', 'user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('team.index'))
            ->assertStatus(200)
            ->assertJson([
                ['caption' => 'Team 1'],
                ['caption' => 'Team 2']
            ]);
    }

    /** @test */
    public function a_user_can_only_get_his_teams()
    {
        $user = factory(User::class)->create();
        factory(Team::class)->create(['caption' => 'Own Team', 'user_id' => $user->id]);
        factory(Team::class)->create(['caption' => 'Other Team']);

        $this->actingAs($user)
            ->get(route('team.index'))
            ->assertStatus(200)
            ->assertJson([
                ['caption' => 'Own Team'],
            ])->assertJsonMissing([
                ['caption' => 'Other Team'],
            ]);
    }

    /** @test */
    public function a_user_can_create_a_team()
    {
        $user = factory(User::class)->create();

        $this->assertDatabaseMissing('teams', [
            'caption' => 'New Team'
        ]);

        $this->actingAs($user)
            ->post(route('team.store'), [
                'caption' => 'New Team'
            ])->assertStatus(201)
            ->assertJson([
                'caption' => 'New Team'
            ]);

        $this->assertDatabaseHas('teams', [
            'caption' => 'New Team'
        ]);
    }

    /** @test */
    public function a_user_can_edit_his_teams()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create([
            'caption' => 'Old Name',
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->put(route('team.update', $team), [
                'caption' => 'New Name'
            ])->assertStatus(200)
            ->assertJson([
                'id' => $team->id,
                'caption' => 'New Name'
            ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'caption' => 'New Name'
        ]);
    }

    /** @test */
    public function a_user_can_edit_only_his_teams()
    {
        $user = factory(User::class)->create();
        $otherTeam = factory(Team::class)->create();

        $this->actingAs($user)
            ->put(route('team.update', $otherTeam), [
                'caption' => 'Change'
            ])->assertStatus(403);

        $this->assertDatabaseMissing('teams', [
            'id' => $otherTeam->id,
            'caption' => 'Change'
        ]);
    }

    /** @test */
    public function a_user_can_get_the_details_about_his_team()
    {
        $user = factory(User::class)->create();
        $ownTeam = factory(Team::class)->create([
            'caption' => 'Own Team',
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->get(route('team.show', $ownTeam))
            ->assertStatus(200)
            ->assertJson([
                'caption' => 'Own Team'
            ]);
    }

    /** @test */
    public function a_user_can_not_get_the_details_about_a_foreign_team()
    {
        $user = factory(User::class)->create();
        $otherTeam = factory(Team::class)->create([
            'caption' => 'Own Team',
        ]);

        $this->actingAs($user)
            ->get(route('team.show', $otherTeam))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_delete_his_teams()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('teams', ['id' => $team->id]);

        $this->actingAs($user)
            ->delete(route('team.destroy', $team))
            ->assertStatus(200);

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    /** @test */
    public function a_user_can_delete_only_his_teams()
    {
        $user = factory(User::class)->create();
        $otherTeam = factory(Team::class)->create();

        $this->assertDatabaseHas('teams', ['id' => $otherTeam->id]);

        $this->actingAs($user)
            ->delete(route('team.destroy', $otherTeam))
            ->assertStatus(403);

        $this->assertDatabaseHas('teams', ['id' => $otherTeam->id]);
    }
}
