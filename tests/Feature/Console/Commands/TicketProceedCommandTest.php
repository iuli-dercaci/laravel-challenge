<?php
declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketProceedCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testCanProceedWithUsers(): void
    {
        $ticket = $this->seedTicket($this->seedUser(), now());

        $this->artisan('ticket:proceed')
            ->assertExitCode(0);

        $ticket->refresh();

        $this->assertNotEquals(false, $ticket->status);
    }

    public function testDoesNotProceedWithoutTickets(): void
    {
        $this->seedUser();

        $this->artisan('ticket:proceed')
            ->assertExitCode(1);
    }

    /**
     * @return User
     */
    private function seedUser(): User
    {
        return User::factory(1)->create()->first();
    }

    /**
     * @param User $user
     * @param DateTimeInterface $createdAt
     * @return Ticket
     */
    private function seedTicket(User $user, DateTimeInterface $createdAt): Ticket
    {
        return Ticket::factory()
            ->count(1)
            ->state(
                ['user_id' => $user->id, 'created_at' => $createdAt]
            )->create()->first();
    }
}
