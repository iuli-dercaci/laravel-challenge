<?php
declare(strict_types=1);

namespace Tests\Feature\Controllers;

use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    public function testOpenTickets(): void
    {
        $response = $this->get('/open-tickets');
        $response->assertStatus(200);
    }

    public function testClosedTickets(): void
    {
        $response = $this->get('/closed-tickets');
        $response->assertStatus(200);
    }

    public function testUserTickets(): void
    {
        $response = $this->get('/users/{email}/tickets');
        $response->assertStatus(200);
    }

    public function testStats(): void
    {
        $response = $this->get('/stats');
        $response->assertStatus(200);
    }
}