<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VinylMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixVinylSlug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vinyl:fix-slug {slug? : Slug específico para corrigir} {--table=vinyl_masters : Tabela a ser corrigida (vinyl_masters ou products)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige slugs duplicados na tabela de vinyl_masters';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $slug = $this->argument('slug') ?? 'everybody';
        $table = $this->option('table');

        $this->info("Procurando registros com slug '{$slug}' na tabela '{$table}'...");

        if ($table === 'vinyl_masters') {
            $this->fixVinylMastersSlugs($slug);
        } else if ($table === 'products') {
            $this->fixProductsSlugs($slug);
        } else {
            // Corrigir ambas as tabelas
            $this->fixVinylMastersSlugs($slug);
            $this->fixProductsSlugs($slug);
        }
    }
    
    /**
     * Corrige slugs duplicados na tabela vinyl_masters
     */
    private function fixVinylMastersSlugs($slug)
    {
        $this->info("Corrigindo slugs na tabela vinyl_masters...");
        
        // Buscar registros com o slug específico
        $count = DB::table('vinyl_masters')->where('slug', $slug)->count();
        
        if ($count === 0) {
            $this->info("Nenhum registro encontrado com slug '{$slug}' na tabela vinyl_masters.");
            return;
        }
        
        $this->info("Encontrados {$count} registros com slug '{$slug}'.");
        
        // Buscar e atualizar registros
        $records = DB::table('vinyl_masters')->where('slug', $slug)->get();
        
        foreach ($records as $index => $record) {
            $newSlug = $slug . '-' . time() . '-' . $index;
            
            $this->info("Atualizando registro ID {$record->id} para slug '{$newSlug}'...");
            
            // Atualizar diretamente no banco para evitar eventos de modelo
            DB::table('vinyl_masters')
                ->where('id', $record->id)
                ->update(['slug' => $newSlug]);
        }
        
        $this->info("Slugs na tabela vinyl_masters atualizados com sucesso!");
    }
    
    /**
     * Corrige slugs duplicados na tabela products
     */
    private function fixProductsSlugs($slug)
    {
        $this->info("Corrigindo slugs na tabela products...");
        
        // Buscar registros com o slug específico
        $count = DB::table('products')->where('slug', $slug)->count();
        
        if ($count === 0) {
            $this->info("Nenhum registro encontrado com slug '{$slug}' na tabela products.");
            return;
        }
        
        $this->info("Encontrados {$count} registros com slug '{$slug}'.");
        
        // Buscar e atualizar registros
        $records = DB::table('products')->where('slug', $slug)->get();
        
        foreach ($records as $index => $record) {
            $newSlug = $slug . '-' . time() . '-' . $index;
            
            $this->info("Atualizando registro ID {$record->id} para slug '{$newSlug}'...");
            
            // Atualizar diretamente no banco para evitar eventos de modelo
            DB::table('products')
                ->where('id', $record->id)
                ->update(['slug' => $newSlug]);
        }
        
        $this->info("Slugs na tabela products atualizados com sucesso!");
    }
}
