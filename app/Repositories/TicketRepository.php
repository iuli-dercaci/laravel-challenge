<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TicketRepository extends BaseRepository
{

    public function __construct(Ticket $model)
    {
        $this->model = $model;
    }

    public function allOpen(): Collection
    {
        return $this->allOpenBuilder()->get();
    }

    public function allOpenBuilder(): Builder
    {
        return $this->model->opened();
    }

    public function allClosedBuilder(): Builder
    {
        return $this->model->closed();
    }

    public function close(Ticket $ticket): Ticket
    {
        $ticket->status = true;
        $ticket->save();

        return $ticket;
    }

    public function findByUserEmailBuilder(string $email): Builder
    {
        return $this->model->whereHas(
            'user',
            fn(Builder $q) => $q->where('email', '=', $email)
        );
    }
}
