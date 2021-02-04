<?php
declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use App\Repositories\TicketRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketGenerateCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var TicketRepository
     */
    private TicketRepository $ticketRepository;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->ticketRepository = new TicketRepository(new Ticket());
    }

    public function testCannotGenerateWithNoUsers(): void
    {
        $this->artisan('ticket:generate')
            ->assertExitCode(1);
    }

    /**
     * @dataProvider positiveAmount
     * @param int $amount
     */
    public function testCanGeneratePositiveAmount(int $amount): void
    {
        User::factory(1)->create();

        $initialAmount = $this->ticketRepository->countAll();
        $this->artisan('ticket:generate', compact('amount'))
            ->assertExitCode(0);

        $generatedAmount = $this->ticketRepository->countAll() - $initialAmount;
        $this->assertEquals($amount, $generatedAmount);
    }

    /**
     * @dataProvider notPositiveAmount
     * @param int $amount
     */
    public function testCannotGenerateNotPositiveAmount(int $amount): void
    {
        User::factory(1)->create();
        $this->artisan('ticket:generate', compact('amount'))
            ->assertExitCode(1);
    }

    /**
     * @return int[][]
     */
    public function positiveAmount(): array
    {
        return [
            [1],
            [34],
        ];
    }

    /**
     * @return int[][]
     */
    public function notPositiveAmount(): array
    {
        return [
            [0],
            [-1],
            [-2000]
        ];
    }
}
