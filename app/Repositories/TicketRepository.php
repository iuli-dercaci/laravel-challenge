<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TicketRepository extends BaseRepository
{

    /**
     * TicketRepository constructor.
     * @param Ticket $model
     */
    public function __construct(Ticket $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function allOpen(): Collection
    {
        return $this->allOpenBuilder()->get();
    }

    /**
     * @return Builder
     */
    public function allOpenBuilder(): Builder
    {
        return $this->model->opened();
    }

    /**
     * @return Builder
     */
    public function allClosedBuilder(): Builder
    {
        return $this->model->closed();
    }

    /**
     * @param Ticket $ticket
     * @return Ticket
     */
    public function close(Ticket $ticket): Ticket
    {
        $ticket->status = true;
        $ticket->save();

        return $ticket;
    }

    /**
     * @param string $email
     * @return Builder
     */
    public function findByUserEmailBuilder(string $email): Builder
    {
        return $this->model->whereHas(
            'user',
            fn(Builder $q) => $q->where('email', '=', $email)
        );
    }

    /**
     * @return int
     */
    public function countAll(): int
    {
        return $this->model->count();
    }

    /**
     * @return int
     */
    public function countAllOpen(): int
    {
        return $this->model->opened()->count();
    }

    /**
     * @return Ticket|null
     */
    public function getLastUpdated(): ?Ticket
    {
        return $this->model->latest('updated_at')->first();
    }
}
