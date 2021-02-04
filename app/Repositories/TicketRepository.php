<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * @param User $user
     * @param int $amount
     * @return Collection|Model|mixed
     */
    public function generate(User $user, int $amount)
    {
        return Ticket::factory()
            ->count((int)$amount)
            ->state(
                fn(): array => ['user_id' => $user->id]
            )->create();
    }

    /**
     * @return Ticket|null
     */
    public function getOldestOpen(): ?Ticket
    {
        return $this->allOpenBuilder()->oldest()->first();
    }
}
