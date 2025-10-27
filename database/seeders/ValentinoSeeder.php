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
        $validGraduates = array_filter($graduatesData, function ($g) {
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

        /**
         * PARTE DE TREJO
         */


        // Insertar usuarios instructores - ESTRUCTURA EXACTA
        $users = [
            [
                'first_name' => 'Mirko Martin',
                'last_name' => 'Manrique Ronceros',
                'full_name' => 'Mirko Martin Manrique Ronceros',
                'dni' => '70000001',
                'document' => '70000001',
                'email' => 'mirko@uns.edu.pe',
                'email_verified_at' => now(),
                'phone_number' => '+51 987 654 301',
                'password' => Hash::make('password'),
                'role' => '"instructor"',
                'gender' => 'male',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Carlos Alfredo',
                'last_name' => 'Gil Narvaez',
                'full_name' => 'Carlos Alfredo Gil Narvaez',
                'dni' => '70000002',
                'document' => '70000002',
                'email' => 'carlosgil@uns.edu.pe',
                'email_verified_at' => now(),
                'phone_number' => '+51 987 654 302',
                'password' => Hash::make('password'),
                'role' => '"instructor"',
                'gender' => 'male',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Guillermo Edward',
                'last_name' => 'Gil Albarran',
                'full_name' => 'Guillermo Edward Gil Albarran',
                'dni' => '70000003',
                'document' => '70000003',
                'email' => 'guillermo@uns.edu.pe',
                'email_verified_at' => now(),
                'phone_number' => '+51 987 654 303',
                'password' => Hash::make('password'),
                'role' => '"instructor"',
                'gender' => 'male',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Hugo Esteban',
                'last_name' => 'Caselli Gismondi',
                'full_name' => 'Hugo Esteban Caselli Gismondi',
                'dni' => '70000004',
                'document' => '70000004',
                'email' => 'hugo.caselli@uns.edu.pe',
                'email_verified_at' => now(),
                'phone_number' => '+51 987 654 304',
                'password' => Hash::make('password'),
                'role' => '"instructor"',
                'gender' => 'male',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Johan Max',
                'last_name' => 'Lopez Heredia',
                'full_name' => 'Johan Max Lopez Heredia',
                'dni' => '70000005',
                'document' => '70000005',
                'email' => 'johan.lopez@uns.edu.pe',
                'email_verified_at' => now(),
                'phone_number' => '+51 987 654 305',
                'password' => Hash::make('password'),
                'role' => '"instructor"',
                'gender' => 'male',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Javier Lucho',
                'last_name' => 'Utrilla Camones',
                'full_name' => 'Javier Lucho Utrilla Camones',
                'dni' => '70000006',
                'document' => '70000006',
                'email' => 'javier.utrilla@uns.edu.pe',
                'email_verified_at' => now(),
                'phone_number' => '+51 987 654 306',
                'password' => Hash::make('password'),
                'role' => '"instructor"',
                'gender' => 'male',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Lizbeth Dora',
                'last_name' => 'Briones Pereyra',
                'full_name' => 'Lizbeth Dora Briones Pereyra',
                'dni' => '70000007',
                'document' => '70000007',
                'email' => 'lizbeth.briones@uns.edu.pe',
                'email_verified_at' => now(),
                'phone_number' => '+51 987 654 307',
                'password' => Hash::make('password'),
                'role' => '"instructor"',
                'gender' => 'female',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Carlos Eugenio',
                'last_name' => 'Vega Moreno',
                'full_name' => 'Carlos Eugenio Vega Moreno',
                'dni' => '70000008',
                'document' => '70000008',
                'email' => 'carlos.vega@uns.edu.pe',
                'email_verified_at' => now(),
                'phone_number' => '+51 987 654 308',
                'password' => Hash::make('password'),
                'role' => '"instructor"',
                'gender' => 'male',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        // Insertar usuarios instructores
        DB::table('users')->insert($users);

        // Obtener los IDs de los usuarios recién insertados
        $userIds = DB::table('users')
            ->whereIn('email', [
                'mirko@uns.edu.pe',
                'carlosgil@uns.edu.pe',
                'guillermo@uns.edu.pe',
                'hugo.caselli@uns.edu.pe',
                'johan.lopez@uns.edu.pe',
                'javier.utrilla@uns.edu.pe',
                'lizbeth.briones@uns.edu.pe',
                'carlos.vega@uns.edu.pe'
            ])
            ->pluck('id', 'email')
            ->toArray();

        // Insertar instructores - ESTRUCTURA EXACTA
        $instructors = [
            [
                'instructor_id' => 2001,
                'user_id' => $userIds['mirko@uns.edu.pe'],
                'bio' => 'Profesor con experiencia en educación universitaria.',
                'expertise_area' => 'Matemáticas, Ciencias',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'instructor_id' => 2002,
                'user_id' => $userIds['carlosgil@uns.edu.pe'],
                'bio' => 'Especialista en metodologías de enseñanza.',
                'expertise_area' => 'Pedagogía, Investigación',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'instructor_id' => 2003,
                'user_id' => $userIds['guillermo@uns.edu.pe'],
                'bio' => 'Docente con amplia experiencia académica.',
                'expertise_area' => 'Ingeniería, Tecnología',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'instructor_id' => 2004,
                'user_id' => $userIds['hugo.caselli@uns.edu.pe'],
                'bio' => 'Profesor especializado en ciencias básicas.',
                'expertise_area' => 'Física, Matemáticas',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'instructor_id' => 2005,
                'user_id' => $userIds['johan.lopez@uns.edu.pe'],
                'bio' => 'Docente con enfoque en investigación aplicada.',
                'expertise_area' => 'Investigación, Metodología',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'instructor_id' => 2006,
                'user_id' => $userIds['javier.utrilla@uns.edu.pe'],
                'bio' => 'Profesor con experiencia en educación superior.',
                'expertise_area' => 'Humanidades, Ciencias Sociales',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'instructor_id' => 2007,
                'user_id' => $userIds['lizbeth.briones@uns.edu.pe'],
                'bio' => 'Especialista en procesos educativos.',
                'expertise_area' => 'Educación, Psicología',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'instructor_id' => 2008,
                'user_id' => $userIds['carlos.vega@uns.edu.pe'],
                'bio' => 'Docente con amplia trayectoria académica.',
                'expertise_area' => 'Administración, Gestión',
                'status' => 'active',
                'created_at' => now(),
            ]
        ];

        DB::table('instructors')->insert($instructors);

        $this->command->info('✅ 8 instructores creados exitosamente!');
        $this->command->info('📧 Emails:');
        $this->command->info('   - mirko@uns.edu.pe');
        $this->command->info('   - carlosgil@uns.edu.pe');
        $this->command->info('   - guillermo@uns.edu.pe');
        $this->command->info('   - hugo.caselli@uns.edu.pe');
        $this->command->info('   - johan.lopez@uns.edu.pe');
        $this->command->info('   - javier.utrilla@uns.edu.pe');
        $this->command->info('   - lizbeth.briones@uns.edu.pe');
        $this->command->info('   - carlos.vega@uns.edu.pe');
        $this->command->info('🔑 Contraseña para todos: password');
    }
}