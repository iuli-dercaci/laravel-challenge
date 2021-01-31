<?php
declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Repositories\TicketRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    public TicketRepository $ticketRepository;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->ticketRepository = new TicketRepository(new Ticket);
    }

    public function testOpenTickets(): void
    {
        $ticketsCount = 3;
        $userCount = 2;
        $this->seedTickets($ticketsCount, $userCount);

        $response = $this->get('/open-tickets');
        $payload = json_decode($response->getContent());

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'application/json');
        $this->assertEquals($payload->total, $ticketsCount * $userCount);
    }

    public function testOpenTicketsEmptyCollection(): void
    {
        $response = $this->get('/open-tickets');
        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'application/json');

        $payload = json_decode($response->getContent());

        $this->assertEquals(0, $payload->total);
    }


    public function testClosedTickets(): void
    {
        $ticketsCount = 3;
        $userCount = 2;
        $this->seedTickets($ticketsCount, $userCount, true);

        $response = $this->get('/closed-tickets');
        $response->assertSuccessful();

        $payload = json_decode($response->getContent());
        $this->assertEquals($payload->total, $ticketsCount * $userCount);
    }

    public function testClosedTicketsEmptyCollection(): void
    {
        $response = $this->get('/closed-tickets');
        $response->assertSuccessful();

        $payload = json_decode($response->getContent());
        $this->assertEquals(0, $payload->total);
    }

    public function testOneUserTickets(): void
    {
        $ticketsCount = 3;
        $email = 'test@email.com';
        $this->seedUserTickets($ticketsCount, $email);
        $response = $this->get("/users/{$email}/tickets");
        $payload = json_decode($response->getContent());

        $response->assertSuccessful();
        $this->assertEquals($ticketsCount, $payload->total);
    }

    public function testSeveralUserTickets(): void
    {
        $ticketsCount = 3;
        $email1 = 'test1@email.com';
        $email2 = 'test2@email.com';
        $email3 = 'test3@email.com';
        $this->seedUserTickets($ticketsCount, $email1);
        $this->seedUserTickets($ticketsCount, $email2);
        $this->seedUserTickets($ticketsCount, $email3);

        $response = $this->get("/users/{$email1}/tickets");
        $payload = json_decode($response->getContent());

        $response->assertSuccessful();
        $this->assertEquals($ticketsCount, $payload->total);
    }

    public function testUserTicketsEmptyCollection(): void
    {
        $ticketsCount = 3;
        $email1 = 'test1@email.com';
        $email2 = 'test2@email.com';
        $email3 = 'test3@email.com';
        $this->seedUserTickets($ticketsCount, $email2);
        $this->seedUserTickets($ticketsCount, $email3);

        $response = $this->get("/users/{$email1}/tickets");
        $payload = json_decode($response->getContent());
        $this->assertEquals(0, $payload->total);
    }

    public function testStats(): void
    {
        $response = $this->get('/stats');
        $response->assertStatus(200);
    }


    private function seedUserTickets(int $ticketsCount, string $email): void
    {
        User::factory(1)
            ->has(
                Ticket::factory()
                    ->count($ticketsCount)
                    ->state(
                        fn(array $attributes, User $user): array => ['user_id' => $user->id]
                    )
            )->state(
                fn(): array => ['email' => $email]
            )
            ->create();
    }

    private function seedTickets(
        int $ticketsCount,
        int $userCount,
        bool $isClosed = false
    ): void
    {
        User::factory($userCount)
            ->has(
                Ticket::factory()
                    ->count($ticketsCount)
                    ->state(
                        fn(array $attributes, User $user): array => ['user_id' => $user->id, 'status' => $isClosed]
                    )
            )
            ->create();
    }
}
