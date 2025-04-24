<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\PlaylistSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria um usuário de teste
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Executa o seeder de playlists
        $this->call([
            PlaylistSeeder::class,
        ]);
    }
}
