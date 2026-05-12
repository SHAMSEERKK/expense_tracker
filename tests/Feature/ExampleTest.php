<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guest_is_sent_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }
}
