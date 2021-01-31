<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use App\Services\TicketService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public const ITEM_PER_PAGE = 10;

    /**
     * @param Request $request
     * @param TicketRepository $ticketRepository
     * @return LengthAwarePaginator
     */
    public function openTickets(
        Request $request,
        TicketRepository $ticketRepository
    ): LengthAwarePaginator
    {
        return $ticketRepository
            ->allOpenBuilder()
            ->paginate($this->perPage($request));
    }

    /**
     * @param Request $request
     * @param TicketRepository $ticketRepository
     * @return LengthAwarePaginator
     */
    public function closedTickets(
        Request $request,
        TicketRepository $ticketRepository
    ): LengthAwarePaginator
    {
        return $ticketRepository
            ->allClosedBuilder()
            ->paginate($this->perPage($request));
    }

    /**
     * @param string $email
     * @param Request $request
     * @param TicketRepository $ticketRepository
     * @return LengthAwarePaginator
     */
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

    /**
     * @param TicketRepository $ticketRepository
     * @param UserRepository $userRepository
     * @param TicketService $ticketService
     * @return array
     */
    public function stats(
        TicketRepository $ticketRepository,
        UserRepository $userRepository,
        TicketService $ticketService
    ): array
    {
        return $ticketService->assembleStats(
            $ticketRepository->countAll(),
            $ticketRepository->countAllOpen(),
            $userRepository->getUserByMostCreatedTickets(),
            $ticketRepository->getLastUpdated()
        );
    }

    /**
     * @param Request $request
     * @return int
     */
    private function perPage(Request $request): int
    {
        return (int)$request->get('per_page')
            ?: self::ITEM_PER_PAGE;
    }
}
