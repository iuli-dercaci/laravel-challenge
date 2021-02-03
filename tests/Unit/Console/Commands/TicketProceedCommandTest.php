<?php
declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use App\Models\Ticket;
use App\Repositories\TicketRepository;
use Tests\TestCase;

class TicketProceedCommandTest extends TestCase
{
    public function testCanProceedWhenThereAreTickets(): void
    {
        /** @var Ticket $ticket */
        $ticket = $this->mock(Ticket::class);
        $this->setContainerDependencies($ticket);

        $this->artisan('ticket:proceed')
            ->assertExitCode(0);
    }

    public function testFailsToProceedWhenThereAreNoTickets(): void
    {
        $this->setContainerDependencies();

        $this->artisan('ticket:proceed')
            ->assertExitCode(1);
    }

    /**
     * @param Ticket|null $return
     */
    private function setContainerDependencies(?Ticket $return = null): void
    {
        $ticketRepository = $this->createMock(TicketRepository::class);
        $ticketRepository
            ->expects($this->atMost(1))
            ->method('getOldestOpen')
            ->willReturn($return);
        $ticketRepository
            ->expects($this->atMost(1))
            ->method('close');


        $this->app->instance(TicketRepository::class, $ticketRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
