<?php
declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function create(array $attributes): Model;

    public function find($id): ?Model;
}
