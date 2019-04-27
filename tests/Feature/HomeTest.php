<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_is_redirect_to_the_login_page_by_default()
    {
        $this->get('/')->assertRedirect('login');
    }

    /** @test */
    public function a_logged_in_user_can_see_the_home_page()
    {
        $user = factory(User::class)->create();

        $this
            ->actingAs($user)
            ->get('/')
            ->assertLocation('/')
            ->assertSee('Home')
            ->assertSee($user->name);
    }
}
