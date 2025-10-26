<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixGroupParticipantDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Corrigiendo DEFAULT value en la tabla group_participants...');
        
        try {
            // Esta es la consulta SQL que corrige el 'typo'
            DB::statement("ALTER TABLE group_participants ALTER enrollment_status SET DEFAULT 'active';");
            
            $this->command->info('¡Éxito! El valor DEFAULT de enrollment_status ahora es "active".');

        } catch (\Exception $e) {
            $this->command->error('Error al ejecutar el ALTER TABLE: ' . $e->getMessage());
        }
    }
}