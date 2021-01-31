<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    /**
     * TicketRepository constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @return User|null
     */
    public function getUserByMostCreatedTickets(): ?User
    {
        return $this->model
            ->withCount('tickets')
            ->orderBy('tickets_count', 'desc')
            ->first();
    }
}
