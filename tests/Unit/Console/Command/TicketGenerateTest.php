<?php

namespace Tests\Unit\Console\Command;

use App\Models\User;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class TicketGenerateTest extends TestCase
{
    public function testWorksWithoutArguments()
    {
        $this->setContainerDependencies(new User());

        $this->artisan('ticket:generate')
            ->assertExitCode(0);
    }

    public function testWorksWithNumericArguments()
    {
        $this->setContainerDependencies(new User());

        $this->artisan('ticket:generate 3')
            ->assertExitCode(0);
    }

    public function testWorksWithNonNumericArguments()
    {
        $this->setContainerDependencies(new User());

        $this->artisan('ticket:generate f')
            ->assertExitCode(1);
    }

    public function testWorksWithNonUsers()
    {
        $this->setContainerDependencies();

        $this->artisan('ticket:generate 1000')
            ->assertExitCode(1);
    }

    private function setContainerDependencies(?User $user = null)
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
