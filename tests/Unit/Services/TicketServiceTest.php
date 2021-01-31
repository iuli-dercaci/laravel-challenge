<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class TicketServiceTest extends TestCase
{
    /**
     * @var TicketService
     */
    private TicketService $ticketService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->ticketService = new TicketService();
    }

    /**
     * @dataProvider statsData
     * @param int $count
     * @param int $totalOpen
     * @param Ticket|null $ticket
     * @param User|null $user
     */
    public function testCanAssembleStats(
        int $count,
        int $totalOpen,
        ?Ticket $ticket,
        ?User $user
    ): void
    {
        $userEmail = $user ? $user->email : TicketService::VALUE_IS_NOT_AVAILABLE;
        $updatedAt = $ticket
            ? $ticket->updated_at->format(TicketService::STATS_DATE_TIME_FORMAT)
            : TicketService::VALUE_IS_NOT_AVAILABLE;

        $result = $this->ticketService->assembleStats($count, $totalOpen, $user, $ticket);

        $this->assertIsArray($result);

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('total_open_tickets', $result);
        $this->assertArrayHasKey('top_user', $result);
        $this->assertArrayHasKey('last_ticket_processed_at', $result);

        $this->assertEquals($count, $result['total']);
        $this->assertEquals($totalOpen, $result['total_open_tickets']);
        $this->assertEquals($userEmail, $result['top_user']);
        $this->assertEquals($updatedAt, $result['last_ticket_processed_at']);
    }

    /**
     * @return array
     */
    public function statsData(): array
    {
        $updatedAt = now();
        $ticket = $this->getTicketMock($updatedAt);
        $user = $this->getUserMock('user_email@email.com');

        return [
            'all_data' => [10, 3, $ticket, $user],
            'user_missing' => [10, 3, $ticket, null],
            'ticket_missing' => [10, 3, null, $user],
            'user_ticket_missing' => [10, 3, null, null],
        ];
    }

    /**
     * @param string $email
     * @return User
     */
    private function getUserMock(string $email): User
    {
        $user = $this->createMock(User::class);
        $user->method('__get')
            ->with('email')
            ->willReturn($email);

        return $user;
    }

    /**
     * @param DateTimeInterface $updatedAt
     * @return Ticket
     */
    private function getTicketMock(DateTimeInterface $updatedAt): Ticket
    {
        $ticket = $this->createMock(Ticket::class);
        $ticket->method('__get')
            ->with($this->equalTo('updated_at'))
            ->will($this->returnValue($updatedAt));

        return $ticket;
    }
}
