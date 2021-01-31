<?php
declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Repositories\TicketRepository;
use App\Services\TicketService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var TicketRepository
     */
    public TicketRepository $ticketRepository;

    /**
     * TicketControllerTest constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(
        ?string $name = null,
        array $data = [],
        $dataName = ''
    )
    {
        parent::__construct($name, $data, $dataName);
        $this->ticketRepository = new TicketRepository(new Ticket);
    }

    public function testOpenTickets(): void
    {
        $ticketsCount = 3;
        $userCount = 2;
        $this->seedUsersWithTickets($ticketsCount, $userCount, ['status' => false]);

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
        $this->seedUsersWithTickets($ticketsCount, $userCount, ['status' => true]);

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
        $this->seedUserTicketsWithParticularEmail($ticketsCount, $email);
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
        $this->seedUserTicketsWithParticularEmail($ticketsCount, $email1);
        $this->seedUserTicketsWithParticularEmail($ticketsCount, $email2);
        $this->seedUserTicketsWithParticularEmail($ticketsCount, $email3);

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
        $this->seedUserTicketsWithParticularEmail($ticketsCount, $email2);
        $this->seedUserTicketsWithParticularEmail($ticketsCount, $email3);

        $response = $this->get("/users/{$email1}/tickets");
        $payload = json_decode($response->getContent());
        $this->assertEquals(0, $payload->total);
    }

    public function testStats(): void
    {
        $topUserEmail = 'top_user@mail.com';
        $topUserTicketsCount = 10;
        $topUser = $this->seedUserTicketsWithParticularEmail($topUserTicketsCount, $topUserEmail, ['status' => true]);

        $secondUserEmail = 'second_user@mail.com';
        $secondUserTicketsCount = 5;
        $this->seedUserTicketsWithParticularEmail($secondUserTicketsCount, $secondUserEmail);

        $totalUnprocessed = $secondUserTicketsCount;
        $totalTickets = $topUserTicketsCount + $secondUserTicketsCount;
        $lastUpdatedTicket = $topUser->tickets()->first();
        $lastUpdatedTicket->touch();

        $response = $this->get('/stats');
        $payload = json_decode($response->getContent(), true);

        $response->assertOk();

        $this->assertArrayHasKey('total', $payload);
        $this->assertArrayHasKey('total_open_tickets', $payload);
        $this->assertArrayHasKey('top_user', $payload);
        $this->assertArrayHasKey('last_ticket_processed_at', $payload);

        $this->assertEquals($totalTickets, $payload['total']);
        $this->assertEquals($totalUnprocessed, $payload['total_open_tickets']);
        $this->assertEquals($topUserEmail, $payload['top_user']);
        $this->assertEquals(
            $lastUpdatedTicket->updated_at->format(TicketService::STATS_DATE_TIME_FORMAT),
            $payload['last_ticket_processed_at']
        );
    }

    /**
     * @param int $ticketsCount
     * @param string $email
     * @param array $ticketAttributes
     * @return User
     */
    private function seedUserTicketsWithParticularEmail(
        int $ticketsCount,
        string $email,
        array $ticketAttributes = []
    ): User
    {
        return User::factory(1)
            ->has(
                Ticket::factory()
                    ->count($ticketsCount)
                    ->state(
                        fn(array $attributes, User $user): array => array_merge(['user_id' => $user->id], $ticketAttributes)
                    )
            )->state(
                fn(): array => ['email' => $email]
            )
            ->create()
            ->first();
    }

    /**
     * @param int $ticketsCount
     * @param int $userCount
     * @param array $ticketAttributes
     * @return Collection
     */
    private function seedUsersWithTickets(
        int $ticketsCount,
        int $userCount,
        array $ticketAttributes = []
    ): Collection
    {
        return User::factory($userCount)
            ->has(
                Ticket::factory()
                    ->count($ticketsCount)
                    ->state(
                        fn(array $attributes, User $user): array => array_merge(['user_id' => $user->id], $ticketAttributes)
                    )
            )
            ->create();
    }
}
