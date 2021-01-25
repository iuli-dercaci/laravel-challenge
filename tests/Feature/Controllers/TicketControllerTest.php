<?php
declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;

    public function testOpenTickets(): void
    {
        $response = $this->get('/open-tickets');
        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'application/json');

        $total = Ticket::open()->count();
        $payload = json_decode($response->getContent());

        $this->assertEquals($payload->total, $total);
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
