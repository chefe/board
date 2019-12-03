<?php

namespace Tests\Feature\API;

use App\TaskState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskStateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_ca_get_all_task_states()
    {
        TaskState::create(['caption' => 'state #1']);
        TaskState::create(['caption' => 'state #2']);
        TaskState::create(['caption' => 'state #3']);

        $this->get(route('state.index'))
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJson([
                ['caption' => 'state #1'],
                ['caption' => 'state #2'],
                ['caption' => 'state #3'],
            ]);
    }
}
