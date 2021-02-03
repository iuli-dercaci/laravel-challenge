<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;

class TicketGenerate extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'ticket:generate {amount=1}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Generates a given number (one by default) of dummy data tickets';

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
     * @param UserRepository $userRepository
     * @return int
     */
    public function handle(TicketRepository $ticketRepository, UserRepository $userRepository): int
    {
        $amount = $this->argument('amount');

        if (!is_numeric($amount)) {
            return 1;
        }

        if (null === $user = $userRepository->getFirstUser()) {
            return 1;
        }

        $ticketRepository->generate($user, (int)$amount);

        return 0;
    }
}
