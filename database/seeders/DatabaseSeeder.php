<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\BrandSeeder;
use Database\Seeders\DimensionSeeder;
use Database\Seeders\EquipmentCategorySeeder;
use Database\Seeders\PlaylistSeeder;
use Database\Seeders\ProductTypeSeeder;
use Database\Seeders\WeightSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       
        // Executa todos os seeders disponíveis
        $this->call([
            // Seeders de configurações básicas
            WeightSeeder::class,
            DimensionSeeder::class,
            BrandSeeder::class,
            ProductTypeSeeder::class,
            EquipmentCategorySeeder::class,
            
            // Seeders de conteúdo
            PlaylistSeeder::class,
        ]);
    }
}
