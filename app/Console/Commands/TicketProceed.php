<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Repositories\TicketRepository;
use Illuminate\Console\Command;

class TicketProceed extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'ticket:proceed';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Proceeds with the oldest open ticket';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @param TicketRepository $ticketRepository
     * @return int
     */
    public function handle(TicketRepository $ticketRepository): int
    {
        if (null === $ticket = $ticketRepository->getOldestOpen()) {
            return 1;
        }

        $ticketRepository->close($ticket);

        return 0;
    }
}
