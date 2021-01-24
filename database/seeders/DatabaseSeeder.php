<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)
            ->has(
                Ticket::factory()
                    ->count(3)
                    ->state(
                        fn(array $attributes, User $user): array => ['user_id' => $user->id]
                    )
            )
            ->create();
    }
}
