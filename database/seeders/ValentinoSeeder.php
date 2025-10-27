<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class ValentinoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // --- 1. BLOQUE DE LIMPIEZA ---
        $this->command->info('Deshabilitando llaves foráneas para ValentinoSeeder...');
        Schema::disableForeignKeyConstraints();

        $this->command->info('Vaciando tablas (TRUNCATE) de Egresados y Encuestas...');
        
        // Truncamos solo las tablas que este seeder "posee"
        DB::table('satisfaction_survey_categories')->truncate();
        DB::table('graduates')->truncate();

        // NO truncamos 'users' ni 'instructors' porque son compartidas.
        
        $this->command->info('Tablas vaciadas. Reactivando llaves foráneas...');
        Schema::enableForeignKeyConstraints();

        
        // --- 2. DATOS DE ENCUESTA ---
        $this->command->info('Creando categorías de encuestas...');
        DB::table('satisfaction_survey_categories')->insert([
            [
                'id_category' => 1,
                'category_name' => 'Infraestructura',
                'description' => 'Evaluación de la infraestructura de la universidad',
            ],
            [
                'id_category' => 2,
                'category_name' => 'Docencia',
                'description' => 'Evaluación de la calidad docente',
            ],
            [
                'id_category' => 3,
                'category_name' => 'Servicios',
                'description' => 'Evaluación de servicios académicos y administrativos',
            ],
        ]);

        
        // --- 3. DATOS DE EGRESADOS (GRADUATES) ---
        $this->command->info('Enlazando egresados (graduates)...');

        // Obtener IDs de usuarios (deben existir por el seeder base)
        $luisId = DB::table('users')->where('email', 'luis.gonzales@example.com')->value('id');
        $angelId = DB::table('users')->where('email', 'angel.bustamante@example.com')->value('id');
        $anderzonId = DB::table('users')->where('email', 'anderzon.portal@example.com')->value('id');

        // Obtener IDs de programas (deben existir por el seeder base)
        $softwareProgramId = DB::table('programs')->where('name', 'Desarrollo de Software y Arquitectura')->value('id');
        $infraProgramId = DB::table('programs')->where('name', 'Infraestructura Tecnológica y Redes')->value('id');
        $gestionProgramId = DB::table('programs')->where('name', 'Gestión y Sistemas de Información')->value('id');

        // Construir datos de graduados
        $graduatesData = [
            [
                'user_id' => $luisId,
                'program_id' => $softwareProgramId,
                'graduation_date' => '2024-05-10',
                'final_note' => 18.4,
                'state' => 'graduated',
                'employability' => 'empleado',
                'feedback' => 'Excelente desempeño en desarrollo y arquitectura de software.',
            ],
            [
                'user_id' => $angelId,
                'program_id' => $infraProgramId,
                'graduation_date' => '2024-06-22',
                'final_note' => 17.9,
                'state' => 'graduated',
                'employability' => 'empleado',
                'feedback' => 'Buen dominio de redes y administración tecnológica.',
            ],
            [
                'user_id' => $anderzonId,
                'program_id' => $gestionProgramId,
                'graduation_date' => '2024-07-05',
                'final_note' => 19.2,
                'state' => 'graduated',
                'employability' => 'empleado',
                'feedback' => 'Destacado en gestión de información y liderazgo.',
            ],
        ];

        // Filtramos por si algún ID de usuario o programa no se encontró
        $validGraduates = array_filter($graduatesData, function($g) {
            return !is_null($g['user_id']) && !is_null($g['program_id']);
        });

        if (count($validGraduates) > 0) {
             DB::table('graduates')->insert($validGraduates);
             $this->command->info(count($validGraduates) . ' egresados insertados.');
        } else {
             $this->command->warn('No se pudo insertar egresados (IDs de usuario o programa no encontrados).');
        }

        
        // --- 4. NUEVOS INSTRUCTORES ---
        $this->command->info('Creando/Actualizando 2 nuevos usuarios (instructores)...');
        
        $users = [
            [
                'first_name' => 'Roberto',
                'last_name' => 'González',
                'full_name' => 'Roberto González',
                'dni' => '44556677',
                'document' => '44556677',
                'email' => 'roberto.gonzalez@email.com',
                'email_verified_at' => Carbon::now(),
                'phone_number' => '+51 987 654 100',
                'password' => Hash::make('password'),
                'role' => '"instructor"',
                'gender' => 'male',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Laura',
                'last_name' => 'Hernández',
                'full_name' => 'Laura Hernández',
                'dni' => '88990011',
                'document' => '88990011', 
                'email' => 'laura.hernandez@email.com',
                'email_verified_at' => Carbon::now(),
                'phone_number' => '+51 987 654 101',
                'password' => Hash::make('password'),
                'role' => '"instructor"',
                'gender' => 'female',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        // Usamos updateOrInsert para añadir o actualizar sin duplicar
        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']], // Clave para buscar
                $user                       // Datos para insertar o actualizar
            );
        }

        // Obtener los IDs de los usuarios (ya sea que existan o se acaben de insertar)
        $userIds = DB::table('users')
            ->whereIn('email', [
                'roberto.gonzalez@email.com',
                'laura.hernandez@email.com'
            ])
            ->pluck('id', 'email')
            ->toArray();

        $this->command->info('Enlazando perfiles de instructor (compatibilidad Randal)...');
        $instructors = [
            [
                'instructor_id' => 2001,
                'user_id' => $userIds['roberto.gonzalez@email.com'],
                'bio' => 'Profesor con 10 años de experiencia en educación universitaria.',
                'expertise_area' => 'Matemáticas, Física',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'instructor_id' => 2002,
                'user_id' => $userIds['laura.hernandez@email.com'],
                'bio' => 'Especialista en metodologías de enseñanza innovadoras.',
                'expertise_area' => 'Pedagogía, Investigación Educativa',
                'status' => 'active',
                'created_at' => now(),
            ]
        ];

        // Usamos updateOrInsert para añadir o actualizar sin duplicar
        foreach ($instructors as $instructor) {
             DB::table('instructors')->updateOrInsert(
                ['instructor_id' => $instructor['instructor_id']], // Clave para buscar
                $instructor                                        // Datos
            );
        }

        $this->command->info('✅ 2 instructores creados/actualizados exitosamente!');
        $this->command->info('📧 Emails: roberto.gonzalez@email.com, laura.hernandez@email.com');
        $this->command->info('🔑 Contraseña para todos: password');
    }
}