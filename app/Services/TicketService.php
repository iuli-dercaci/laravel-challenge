<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;

class TicketService
{
    public const VALUE_IS_NOT_AVAILABLE = 'na';
    public const STATS_DATE_TIME_FORMAT = 'd-m-Y H:i:s';

    /**
     * @param int $count
     * @param int $totalOpen
     * @param User|null $topUser
     * @param Ticket|null $lastClosedTicket
     * @return array
     */
    public function assembleStats(
        int $count,
        int $totalOpen,
        ?User $topUser = null,
        ?Ticket $lastClosedTicket = null
    ): array
    {
        return [
            'total' => $count,
            'total_open_tickets' => $totalOpen,
            'top_user' => $topUser
                ? $topUser->email : self::VALUE_IS_NOT_AVAILABLE,
            'last_ticket_processed_at' => $lastClosedTicket && $lastClosedTicket->updated_at
                ? $lastClosedTicket->updated_at->format(self::STATS_DATE_TIME_FORMAT)
                : self::VALUE_IS_NOT_AVAILABLE,
        ];
    }
}
