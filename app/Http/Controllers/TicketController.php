<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\TicketRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public const ITEM_PER_PAGE = 10;

    public function openTickets(Request $request, TicketRepository $ticketRepository): LengthAwarePaginator
    {
        return $ticketRepository
            ->allOpenBuilder()
            ->paginate($this->perPage($request));
    }

    public function closedTickets(Request $request, TicketRepository $ticketRepository): LengthAwarePaginator
    {
        return $ticketRepository
            ->allClosedBuilder()
            ->paginate($this->perPage($request));
    }

    public function userTickets(
        string $email,
        Request $request,
        TicketRepository $ticketRepository
    ): LengthAwarePaginator
    {
        $records = $ticketRepository->findByUserEmailBuilder($email);

        return $records
            ->paginate($this->perPage($request));
    }

    public function stats(): void
    {
        abort(200);
    }

    private function perPage(Request $request): int
    {
        return (int)$request->get('per_page')
            ?: self::ITEM_PER_PAGE;
    }
}
