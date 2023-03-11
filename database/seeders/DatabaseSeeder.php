<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(100)->create();

        NewsCategory::factory(10)->create();
        NewsCategory::each(function ($category) use ($users) {
            $category->subscriptions()->createMany(
                $users->random(rand(0, 100))->map(fn ($user) => [
                    'user_email' => $user->email,
                    'user_name' => $user->name,
                    'unsubscription_key' => Str::random(),
                ]),
            );
        });
    }
}
