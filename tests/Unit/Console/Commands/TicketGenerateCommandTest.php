<?php
declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use App\Models\User;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class TicketGenerateCommandTest extends TestCase
{
    public function testWorksWithoutArguments(): void
    {
        /** @var User $user */
        $user = $this->mock(User::class);
        $this->setContainerDependencies($user);

        $this->artisan('ticket:generate')
            ->assertExitCode(0);
    }

    public function testWorksWithNumericArguments(): void
    {
        /** @var User $user */
        $user = $this->mock(User::class);
        $this->setContainerDependencies($user);

        $this->artisan('ticket:generate 3')
            ->assertExitCode(0);
    }

    public function testWorksWithNonNumericArguments(): void
    {
        /** @var User $user */
        $user = $this->mock(User::class);
        $this->setContainerDependencies($user);

        $this->artisan('ticket:generate f')
            ->assertExitCode(1);
    }

    public function testWorksWithNonUsers(): void
    {
        $this->setContainerDependencies();

        $this->artisan('ticket:generate 1000')
            ->assertExitCode(1);
    }

    /**
     * @param User|null $user
     */
    private function setContainerDependencies(?User $user = null): void
    {
        $ticketRepository = $this->createMock(TicketRepository::class);
        $ticketRepository
            ->expects($this->atMost(1))
            ->method('generate')
            ->willReturn(new Collection());

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->expects($this->atMost(1))
            ->method('getFirstUser')
            ->willReturn($user);

        $this->app->instance(TicketRepository::class, $ticketRepository);
        $this->app->instance(UserRepository::class, $userRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
