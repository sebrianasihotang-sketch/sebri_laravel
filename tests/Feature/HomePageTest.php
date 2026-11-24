<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_homepage_renders_the_animated_space_background(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Ruang untuk ide')
            ->assertSee('star-drift', false);
    }
}
