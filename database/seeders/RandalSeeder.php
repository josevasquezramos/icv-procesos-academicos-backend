<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon; // Necesario para las fechas
use Illuminate\Support\Facades\Schema; // Necesario para TRUNCATE

class RandalSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- INICIO: BLOQUE DE LIMPIEZA ---
        $this->command->info('Deshabilitando llaves foráneas...');
        Schema::disableForeignKeyConstraints();

        $this->command->info('Vaciando tablas (TRUNCATE) específicas de RandalSeeder...');

        // Lista de tablas que ESTE seeder maneja.
        // Se respeta tu lista de "no tocar" (ej. users, courses, etc. NO están aquí).
        
        // Tablas "nietas" (las más dependientes primero)
        DB::table('vocational_response_course')->truncate();
        DB::table('vocational_responses')->truncate();
        DB::table('vocational_questions')->truncate();
        DB::table('attention_students_requests')->truncate();
        DB::table('student_wellbeing_tutoring_assistances')->truncate();
        DB::table('student_wellbeing_tutorings')->truncate();
        DB::table('student_wellbeing_extracurricular_activities')->truncate();

        // Tablas "hijas" (dependen de 'users', 'departments', etc.)
        DB::table('students')->truncate();
        DB::table('employees')->truncate();
        DB::table('instructors')->truncate();
        
        // Tablas "padre" (de este módulo)
        DB::table('positions')->truncate();
        DB::table('departments')->truncate();
        DB::table('vocational_questionnaires')->truncate();
        DB::table('attention_students_request_types')->truncate();
        
        // NOTA: 'users' y 'courses' NO se tocan, tal como pediste.

        $this->command->info('Tablas vaciadas. Reactivando llaves foráneas...');
        Schema::enableForeignKeyConstraints();
        // --- FIN: BLOQUE DE LIMPIEZA ---


        // =========================
        // CONFIGURACIÓN PREVIA
        // =========================
        // (El seeder ahora recreará departments y positions)
        $this->seedPositionsAndDepartments();

        // =========================
        // MÓDULO: AUTH/LOGIN
        // =========================
        DB::transaction(function () {
            echo "\n🔐 === MÓDULO: AUTH/LOGIN ===\n";
            $this->adjustSequences();
            
            // Esta lógica sigue buscando usuarios existentes (en la tabla 'users' que NO se tocó)
            // pero SÍ creará 'students', 'employees', 'instructors' (que SÍ se vaciaron).
            $students = $this->seedStudents();
            $employees = $this->seedEmployees();
            $instructors = $this->seedInstructors();
            
            $this->printCredentialsSummary($students, $employees, $instructors);
        });

        // =========================
        // MÓDULO: ORIENTACIÓN VOCACIONAL
        // =========================
        DB::transaction(function () {
            echo "\n🎓 === MÓDULO: ORIENTACIÓN VOCACIONAL ===\n";
            
            // Esta lógica busca 'courses' (que NO se tocó)
            $courseLaravel = $this->seedRealCourse(101, 'Desarrollo Backend con Laravel', 'Laravel Avanzado', 'advanced', 40, 10, 300.00);
            $courseReact = $this->seedRealCourse(102, 'Desarrollo Frontend con React', 'React.js Moderno', 'intermediate', 35, 8, 250.00);
            $courseSQL = $this->seedRealCourse(103, 'Bases de Datos SQL', 'SQL desde Cero', 'basic', 20, 5, 150.00);
            
            // 'course_previous_requirements' tampoco se tocó
            $this->seedPrerequisites($courseLaravel, $courseReact, $courseSQL);
            
            // Los cursos Demo SÍ usan la tabla 'courses', pero el seeder
            // está hecho con firstOrCreate, así que no hay problema.
            $demoCourses = $this->seedDemoCourses();
            
            // El resto de tablas (questionnaires, questions, etc.) SÍ se vaciaron
            $questionnaire = $this->seedVocationalQuestionnaire();
            
            [$q1, $rFrontend, $rBackend, $q2Frontend, $q2Backend, $rFeOpt1, $rFeOpt2, $rBeOpt1, $rBeOpt2] 
                = $this->seedVocationalQuestions($questionnaire);
            
            $this->seedVocationalResponseCourses(
                $courseReact, $courseSQL, $courseLaravel,
                $demoCourses,
                $rFeOpt1, $rFeOpt2, $rBeOpt1, $rBeOpt2
            );
            
            echo "✅ Orientación Vocacional configurado\n";
        });

        // =========================
        // MÓDULO: ATENCIÓN AL ESTUDIANTE
        // =========================
        DB::transaction(function () {
            echo "\n📋 === MÓDULO: ATENCIÓN AL ESTUDIANTE ===\n";
            // Estas tablas SÍ se vaciaron
            $requestTypes = $this->seedAttentionRequestTypes();
            $this->seedAttentionSampleRequests($requestTypes);
            echo "✅ Módulo Atención configurado\n";
        });
        
        // =========================
        // MÓDULO: BIENESTAR ESTUDIANTIL
        // =========================
        DB::transaction(function () {
            echo "\n🌟 === MÓDULO: BIENESTAR ESTUDIANTIL ===\n";
            // Estas tablas SÍ se vaciaron
            $this->seedExtracurricularActivities();
            $this->seedTutoringSessions();
            $this->seedTutoringAssistances();
            echo "✅ Módulo Bienestar configurado\n";
        });
    }

    // ========================================
    // MÓDULO: AUTH/LOGIN - HELPERS
    // ========================================

    /**
     * Crea positions y departments (CORREGIDO: sin timestamps)
     */
    private function seedPositionsAndDepartments(): void
    {
        // Como 'departments' se vació, 'first()' siempre será null la primera vez.
        $existingDept = DB::table('departments')->where('id', 1)->first();
        if (!$existingDept) {
            DB::table('departments')->insert([
                'id' => 1,
                'department_name' => 'Administración',
                'description' => 'Departamento administrativo para pruebas',
            ]);
            echo "  ✅ Department 'Administración' creado\n";
        }

        // Como 'positions' se vació, 'first()' siempre será null la primera vez.
        $existingPosition = DB::table('positions')->where('id', 1)->first();
        if (!$existingPosition) {
            DB::table('positions')->insert([
                'id' => 1,
                'position_name' => 'Administrador',
                'department_id' => 1,
            ]);
            echo "  ✅ Position 'Administrador' creado\n";
        }
    }

    /**
     * Verifica IDs existentes
     */
    private function adjustSequences(): void
    {
        echo "  🔄 Verificando IDs existentes...\n";
        $tables = [
            'users', 'students', 'employees', 'instructors',
            'student_wellbeing_extracurricular_activities',
            'student_wellbeing_tutorings',
            'student_wellbeing_tutoring_assistances',
        ];
        foreach ($tables as $table) {
            // 'users' mostrará su ID real (ej. 23)
            // El resto mostrará 0 (porque se vaciaron)
            $maxId = DB::table($table)->max('id') ?? 0;
            if ($maxId < 701) {
                echo "  → {$table}: ID máximo actual es {$maxId} (se crearán desde 701)\n";
            } else {
                echo "  → {$table}: ID máximo actual es {$maxId}\n";
            }
        }
        echo "  ✅ MySQL: Verificación de IDs completada\n";
    }

    /**
     * Crea un usuario con roles (CORREGIDO: sin timestamps para students, employees, instructors)
     */
    private function seedUserWithRoles(array $attrs, array $roles): \stdClass
    {
        $now = Carbon::now();
        
        // 1. Crear usuario (la tabla 'users' NO se tocó, así que firstOrCreate es vital)
        $existingUser = DB::table('users')->where('email', $attrs['email'])->first();
        $userId = null;

        if (!$existingUser) {
            $userId = DB::table('users')->insertGetId([
                'email' => $attrs['email'],
                'password' => Hash::make($attrs['password'] ?? '123456'),
                'role' => json_encode($roles),
                'status' => 'active',
                'timezone' => 'America/Lima',
                'full_name' => $attrs['first_name'] . ' ' . $attrs['last_name'],
                'first_name' => $attrs['first_name'],
                'last_name' => $attrs['last_name'],
                'dni' => $attrs['dni'] ?? null,
                'document' => $attrs['document'] ?? null,
                'created_at' => $now, // 'users' sí los tiene
                'updated_at' => $now, // 'users' sí los tiene
            ]);
        } else {
            $userId = $existingUser->id;
        }

        // Crear filas espejo (estas tablas SÍ se vaciaron)
        foreach ($roles as $role) {
            switch ($role) {
                case 'student':
                    // $existingStudent = DB::table('students')->where('user_id', $userId)->first(); // Ya no es necesario
                    // if (!$existingStudent) {
                        DB::table('students')->insert([
                            'user_id' => $userId,
                            'document_number' => $attrs['dni'] ?? null,
                            'first_name' => $attrs['first_name'] ?? '',
                            'last_name' => $attrs['last_name'] ?? '',
                            'email' => $attrs['email'],
                            'phone' => $attrs['phone'] ?? null,
                            'status' => 'active',
                            'company_id' => null,
                        ]);
                    // }
                    break;

                case 'employee':
                    // $existingEmployee = DB::table('employees')->where('user_id', $userId)->first(); // Ya no es necesario
                    // if (!$existingEmployee) {
                        $positionId = DB::table('positions')->value('id') ?? 1;
                        $departmentId = DB::table('departments')->value('id') ?? 1;
                        
                        DB::table('employees')->insert([
                            'user_id' => $userId,
                            'position_id' => $positionId,
                            'department_id' => $departmentId,
                            'employment_status' => 'Active',
                            'hire_date' => $now->subYears(2),
                        ]);
                    // }
                    break;

                case 'instructor':
                    // $existingInstructor = DB::table('instructors')->where('user_id', $userId)->first(); // Ya no es necesario
                    // if (!$existingInstructor) {
                        DB::table('instructors')->insert([
                            'user_id' => $userId,
                            'status' => 'active',
                            'bio' => 'Instructor de prueba para módulo de orientación.',
                        ]);
                    // }
                    break;
            }
        }
        return (object)['id' => $userId, 'email' => $attrs['email']];
    }

    /** Seed 6 estudiantes */
    private function seedStudents(): array
    {
        $students = [];
        $baseEmail = 'estudiante';
        $baseDNI = 70100000;
        for ($i = 1; $i <= 6; $i++) {
            $students[] = $this->seedUserWithRoles([
                'email' => "{$baseEmail}{$i}@uns.edu.pe", 'first_name' => "Estudiante",
                'last_name' => "Test {$i}", 'dni' => (string)($baseDNI + $i),
                'document' => (string)($baseDNI + $i), 'password' => '123456',
            ], ['student']);
        }
        return $students;
    }

    /** Seed 3 empleados */
    private function seedEmployees(): array
    {
        $employees = [];
        $baseEmail = 'empleado';
        $baseDNI = 70200000;
        for ($i = 1; $i <= 3; $i++) {
            $employees[] = $this->seedUserWithRoles([
                'email' => "{$baseEmail}{$i}@uns.edu.pe", 'first_name' => "Empleado",
                'last_name' => "Test {$i}", 'dni' => (string)($baseDNI + $i),
                'document' => (string)($baseDNI + $i), 'password' => '123456',
            ], ['employee']);
        }
        return $employees;
    }

    /** Seed 3 instructores */
    private function seedInstructors(): array
    {
        $instructors = [];
        $baseEmail = 'instructor';
        $baseDNI = 70300000;
        for ($i = 1; $i <= 3; $i++) {
            $instructors[] = $this->seedUserWithRoles([
                'email' => "{$baseEmail}{$i}@uns.edu.pe", 'first_name' => "Instructor",
                'last_name' => "Test {$i}", 'dni' => (string)($baseDNI + $i),
                'document' => (string)($baseDNI + $i), 'password' => '123456',
            ], ['instructor']);
        }
        return $instructors;
    }

    /** Imprime resumen de credenciales */
    private function printCredentialsSummary(array $students, array $employees, array $instructors): void
    {
        echo "\n📋 === CREDENCIALES DE PRUEBA ===\n";
        echo "\n👨‍🎓 ESTUDIANTES (password: 123456):\n";
        foreach ($students as $s) { echo "  • {$s->email}\n"; }
        echo "\n👔 EMPLEADOS (password: 123456):\n";
        foreach ($employees as $e) { echo "  • {$e->email}\n"; }
        echo "\n👨‍🏫 INSTRUCTORES (password: 123456):\n";
        foreach ($instructors as $i) { echo "  • {$i->email}\n"; }
        echo "\n";
    }

    // ========================================
    // MÓDULO: ORIENTACIÓN VOCACIONAL - HELPERS
    // ========================================

    /**
     * Helper genérico firstOrCreate (CORREGIDO: sin timestamps)
     * La tabla 'courses' NO se vacía, así que el 'firstOrCreate' es vital aquí.
     */
    private function dbFirstOrCreate(string $table, array $match, array $data): \stdClass
    {
        $existing = DB::table($table)->where($match)->first();
        if (!$existing) {
            $id = DB::table($table)->insertGetId(array_merge($match, $data));
            return DB::table($table)->find($id);
        }
        return (object)$existing;
    }

    /** Seed curso real (status=true) */
    private function seedRealCourse(int $courseId, string $title, string $name, string $level, int $duration, int $sessions, float $price): \stdClass
    {
        return $this->dbFirstOrCreate(
            'courses',
            ['title' => $title],
            [
                'course_id' => $courseId, 'name' => $name,
                'description' => "Curso real de {$name} para orientación vocacional.",
                'level' => $level, 'duration' => $duration, 'sessions' => $sessions,
                'selling_price' => $price, 'status' => true,
            ]
        );
    }

    /** Seed prerrequisitos (la tabla NO se vació) */
    private function seedPrerequisites(\stdClass $courseLaravel, \stdClass $courseReact, \stdClass $courseSQL): void
    {
        DB::table('course_previous_requirements')->updateOrInsert(
            ['course_id' => $courseLaravel->id, 'previous_course_id' => $courseSQL->id]
        );
        DB::table('course_previous_requirements')->updateOrInsert(
            ['course_id' => $courseReact->id, 'previous_course_id' => $courseSQL->id]
        );
    }

    /** Seed cursos DEMO (la tabla 'courses' NO se vació) */
    private function seedDemoCourses(): array
    {
        $demoCoursesData = [
            ['course_id' => 701, 'title' => 'Fundamentos Web (demo)', 'name' => 'HTML+CSS Básico (demo)', 'level' => 'basic', 'duration' => 12],
            ['course_id' => 702, 'title' => 'JavaScript desde Cero (demo)', 'name' => 'JS Básico (demo)', 'level' => 'basic', 'duration' => 16],
            ['course_id' => 703, 'title' => 'React desde Cero (demo)', 'name' => 'React Fundamentals (demo)', 'level' => 'basic', 'duration' => 18],
            ['course_id' => 704, 'title' => 'Git y GitHub (demo)', 'name' => 'Control de versiones (demo)', 'level' => 'basic', 'duration' => 10],
            ['course_id' => 705, 'title' => 'PHP desde Cero (demo)', 'name' => 'PHP Basics (demo)', 'level' => 'basic', 'duration' => 14],
            ['course_id' => 706, 'title' => 'Laravel Básico (demo)', 'name' => 'Laravel Intro (demo)', 'level' => 'intermediate', 'duration' => 18],
            ['course_id' => 707, 'title' => 'SQL desde Cero (demo)', 'name' => 'SQL Fundamentals (demo)', 'level' => 'basic', 'duration' => 12],
            ['course_id' => 708, 'title' => 'SQL Consultas Avanzadas (demo)', 'name' => 'SQL Avanzado (demo)', 'level' => 'intermediate', 'duration' => 16],
        ];

        $demoCourses = [];
        foreach ($demoCoursesData as $dc) {
            $course = $this->dbFirstOrCreate(
                'courses',
                ['title' => $dc['title']],
                [
                    'course_id' => $dc['course_id'], 'name' => $dc['name'],
                    'description' => 'Curso demo para rutas de orientación (no visible en catálogo).',
                    'level' => $dc['level'], 'duration' => $dc['duration'],
                    'sessions' => 0, 'selling_price' => 0, 'status' => false,
                    'featured' => false, 'bestseller' => false, 'highest_rated' => false,
                ]
            );
            $demoCourses[$dc['course_id']] = $course;
        }
        return $demoCourses;
    }

    /** Seed cuestionario vocacional (Esta tabla SÍ se vació) */
    private function seedVocationalQuestionnaire(): \stdClass
    {
        // DB::table('vocational_questionnaires')->update(['activated' => false]); // No es necesario, la tabla está vacía

        // $questionnaire = DB::table('vocational_questionnaires')->where('title', 'Orientación 2025 (Demo)')->first(); // Estará vacía
        // if (!$questionnaire) {
            $id = DB::table('vocational_questionnaires')->insertGetId([
                'title' => 'Orientación 2025 (Demo)',
                'id_questionnaire' => null,
                'description' => 'Flujo corto para pruebas',
                'creation_date' => Carbon::now(),
                'activated' => true,
            ]);
            $questionnaire = DB::table('vocational_questionnaires')->find($id);
        // }
        // ... (la lógica de re-activación ya no es necesaria)
        return (object)$questionnaire;
    }

    /** Seed preguntas Q1 y Q2 (Estas tablas SÍ se vaciaron) */
    private function seedVocationalQuestions(\stdClass $questionnaire): array
    {
        // Helper para insertar y obtener ID (no necesitamos 'firstOrCreate' porque las tablas están vacías)
        $insertAndGet = function(string $table, array $data) {
            $id = DB::table($table)->insertGetId($data);
            return DB::table($table)->find($id);
        };

        $q1 = $insertAndGet('vocational_questions', ['id_questionnaire' => $questionnaire->id, 'text_question' => '¿Qué quieres aprender?', 'id_question' => null, 'type_response' => 'single']);
        $rFrontend = $insertAndGet('vocational_responses', ['id_question' => $q1->id, 'text_response' => 'Frontend', 'id_response' => null, 'type_response' => 'option']);
        $rBackend = $insertAndGet('vocational_responses', ['id_question' => $q1->id, 'text_response' => 'Backend', 'id_response' => null, 'type_response' => 'option']);
        $q2Frontend = $insertAndGet('vocational_questions', ['id_questionnaire' => $questionnaire->id, 'text_question' => '¿Cuál es tu objetivo en Frontend?', 'id_question' => $rFrontend->id, 'type_response' => 'single']);
        $q2Backend = $insertAndGet('vocational_questions', ['id_questionnaire' => $questionnaire->id, 'text_question' => '¿Cuál es tu objetivo en Backend?', 'id_question' => $rBackend->id, 'type_response' => 'single']);
        $rFeOpt1 = $insertAndGet('vocational_responses', ['id_question' => $q2Frontend->id, 'text_response' => 'Aprender React'], ['id_response' => null, 'type_response' => 'option']);
        $rFeOpt2 = $insertAndGet('vocational_responses', ['id_question' => $q2Frontend->id, 'text_response' => 'Fortalecer SQL (bases)'], ['id_response' => null, 'type_response' => 'option']);
        $rBeOpt1 = $insertAndGet('vocational_responses', ['id_question' => $q2Backend->id, 'text_response' => 'Aprender Laravel'], ['id_response' => null, 'type_response' => 'option']);
        $rBeOpt2 = $insertAndGet('vocational_responses', ['id_question' => $q2Backend->id, 'text_response' => 'Refuerzo de SQL'], ['id_response' => null, 'type_response' => 'option']);

        return [$q1, $rFrontend, $rBackend, $q2Frontend, $q2Backend, $rFeOpt1, $rFeOpt2, $rBeOpt1, $rBeOpt2];
    }

    /** Seed mapeos: Respuestas -> Cursos (Esta tabla SÍ se vació) */
    private function seedVocationalResponseCourses(
        \stdClass $courseReact, \stdClass $courseSQL, \stdClass $courseLaravel,
        array $demoCourses, \stdClass $rFeOpt1, \stdClass $rFeOpt2,
        \stdClass $rBeOpt1, \stdClass $rBeOpt2
    ): void {
        $attach = function ($responseId, array $coursesInOrder) {
            $rank = 1;
            foreach ($coursesInOrder as $course) {
                if ($course) {
                    // Usamos 'insert' porque la tabla está vacía
                    DB::table('vocational_response_course')->insert(
                        ['response_id' => $responseId, 'course_id' => $course->id, 'rank' => $rank++]
                    );
                }
            }
        };
        $attach($rFeOpt1->id, [$demoCourses[701] ?? null, $demoCourses[702] ?? null, $demoCourses[703] ?? null, $demoCourses[704] ?? null]);
        $attach($rFeOpt2->id, [$demoCourses[707] ?? null, $demoCourses[708] ?? null]);
        $attach($rBeOpt1->id, [$demoCourses[705] ?? null, $demoCourses[707] ?? null, $demoCourses[706] ?? null]);
        $attach($rBeOpt2->id, [$demoCourses[707] ?? null, $demoCourses[708] ?? null]);
    }

    // ========================================
    // MÓDULO: ATENCIÓN AL ESTUDIANTE - HELPERS
    // ========================================
    
    /** Seed tipos de solicitud (Esta tabla SÍ se vació) */
    private function seedAttentionRequestTypes(): array
    {
        $types = [
            ['name_type' => 'Constancia de estudios', 'description' => 'Solicitud de documento que acredita matrícula vigente'],
            ['name_type' => 'Certificado de notas', 'description' => 'Documento oficial con historial académico'],
            ['name_type' => 'Rectificación de datos', 'description' => 'Corrección de información personal en sistema'],
            ['name_type' => 'Consulta académica', 'description' => 'Dudas sobre cursos, horarios, prerrequisitos'],
            ['name_type' => 'Problema técnico', 'description' => 'Inconvenientes con plataformas o accesos digitales'],
            ['name_type' => 'Información de trámites', 'description' => 'Consultas sobre procedimientos administrativos'],
            ['name_type' => 'Solicitud de tutoría', 'description' => 'Petición de apoyo académico personalizado'],
        ];
        $createdTypes = [];
        foreach ($types as $index => $type) {
            // $existing = DB::table('attention_students_request_types')->where('name_type', $type['name_type'])->first(); // Tabla vacía
            // if (!$existing) {
                $id = DB::table('attention_students_request_types')->insertGetId(['id_type' => $index + 1, 'name_type' => $type['name_type'], 'description' => $type['description']]);
                $createdTypes[] = (object)['id' => $id, 'id_type' => $index + 1, 'name_type' => $type['name_type']];
            // } else { $createdTypes[] = $existing; }
        }
        echo "  → " . count($createdTypes) . " tipos de solicitud configurados\n";
        return $createdTypes;
    }
    
    /** Seed solicitudes de ejemplo (Esta tabla SÍ se vació) */
    private function seedAttentionSampleRequests(array $requestTypes): void
    {
        // 'students' se vació y se volvió a llenar, así que esta consulta funciona
        $students = DB::table('students')->where('user_id', '>=', 701)->limit(6)->pluck('id')->toArray();
        if (empty($students)) {
            echo "  ⚠️  No hay estudiantes disponibles para crear solicitudes\n";
            return;
        }
        $sampleRequests = [
            ['student_id' => $students[0] ?? $students[0], 'type_id' => $requestTypes[0]->id, 'description' => 'Necesito una constancia de estudios para tramitar mi beca...', 'current_state' => 'received'],
            ['student_id' => $students[1] ?? $students[0], 'type_id' => $requestTypes[1]->id, 'description' => 'Solicito certificado oficial de notas del periodo 2024-I y 2024-II...', 'current_state' => 'in_progress'],
            ['student_id' => $students[2] ?? $students[0], 'type_id' => $requestTypes[2]->id, 'description' => 'Mi dirección de domicilio está incorrecta en el sistema...', 'current_state' => 'completed'],
            ['student_id' => $students[3] ?? $students[0], 'type_id' => $requestTypes[3]->id, 'description' => '¿Es posible llevar el curso de Algoritmos II si tengo pendiente Matemática Discreta?...', 'current_state' => 'received'],
            ['student_id' => $students[4] ?? $students[0], 'type_id' => $requestTypes[4]->id, 'description' => 'No puedo acceder al campus virtual desde hace 3 días...', 'current_state' => 'in_progress'],
            ['student_id' => $students[5] ?? $students[0], 'type_id' => $requestTypes[5]->id, 'description' => 'Quisiera información sobre el proceso de convalidación de cursos...', 'current_state' => 'received'],
            ['student_id' => $students[0] ?? $students[0], 'type_id' => $requestTypes[6]->id, 'description' => 'Estoy teniendo dificultades con Cálculo Integral...', 'current_state' => 'completed'],
            ['student_id' => $students[1] ?? $students[0], 'type_id' => $requestTypes[0]->id, 'description' => 'Requiero constancia de matrícula vigente para renovar mi seguro estudiantil...', 'current_state' => 'in_progress'],
        ];
        $created = 0;
        foreach ($sampleRequests as $request) {
            // $exists = ... // Tabla vacía
            // if (!$exists) {
                DB::table('attention_students_requests')->insert([
                    'student_id' => $request['student_id'], 'type_id' => $request['type_id'],
                    'description' => $request['description'], 'current_state' => $request['current_state'],
                    'creation_date' => now()->subDays(rand(1, 14)), 'update_date' => now()->subDays(rand(0, 7)),
                ]);
                $created++;
            // }
        }
        echo "  → {$created} solicitudes de ejemplo creadas\n";
    }

    // ========================================
    // MÓDULO: BIENESTAR ESTUDIANTIL - HELPERS
    // ========================================
    
    /** Seed actividades extracurriculares (Esta tabla SÍ se vació) */
    private function seedExtracurricularActivities(): void
    {
        $students = DB::table('students')->where('user_id', '>=', 701)->limit(6)->pluck('id')->toArray();
        if (empty($students)) { echo "  ⚠️  No hay estudiantes disponibles para crear actividades\n"; return; }
        $maxId = DB::table('student_wellbeing_extracurricular_activities')->max('id') ?? 0;
        if ($maxId < 701) { echo "  → Actividades: ID máximo actual es {$maxId}\n"; }

        $activities = [
            ['id_activity' => 701, 'activity_name' => 'Torneo Interfacultades de Fútbol', 'activity_type' => 'Deportiva', 'description' => 'Campeonato de fútbol 7...', 'event_date' => now()->addDays(15), 'student_creator_id' => $students[0]],
            ['id_activity' => 702, 'activity_name' => 'Maratón Universitaria 5K', 'activity_type' => 'Deportiva', 'description' => 'Carrera de 5 kilómetros por el campus...', 'event_date' => now()->addDays(20), 'student_creator_id' => $students[1]],
            ['id_activity' => 703, 'activity_name' => 'Taller de Yoga y Mindfulness', 'activity_type' => 'Deportiva', 'description' => 'Sesiones de yoga y meditación...', 'event_date' => now()->addDays(3), 'student_creator_id' => $students[2]],
            ['id_activity' => 704, 'activity_name' => 'Festival de Música Andina', 'activity_type' => 'Cultural', 'description' => 'Presentación de grupos folclóricos...', 'event_date' => now()->addDays(10), 'student_creator_id' => $students[3]],
            ['id_activity' => 705, 'activity_name' => 'Concurso de Fotografía "Campus en Colores"', 'activity_type' => 'Cultural', 'description' => 'Concurso de fotografía sobre la vida universitaria...', 'event_date' => now()->addDays(25), 'student_creator_id' => $students[4]],
            ['id_activity' => 706, 'activity_name' => 'Ciclo de Cine Latinoamericano', 'activity_type' => 'Cultural', 'description' => 'Proyección de películas latinoamericanas...', 'event_date' => now()->addDays(7), 'student_creator_id' => $students[5]],
            ['id_activity' => 707, 'activity_name' => 'Hackathon: Soluciones Tecnológicas para la Ciudad', 'activity_type' => 'Integración', 'description' => '24 horas de programación intensiva...', 'event_date' => now()->addDays(30), 'student_creator_id' => $students[0]],
            ['id_activity' => 708, 'activity_name' => 'Charla: Inteligencia Artificial y el Futuro del Trabajo', 'activity_type' => 'Integración', 'description' => 'Conferencia magistral con expertos en IA...', 'event_date' => now()->addDays(12), 'student_creator_id' => $students[1]],
            ['id_activity' => 709, 'activity_name' => 'Taller de Oratoria y Debate Universitario', 'activity_type' => 'Integración', 'description' => 'Curso intensivo de 4 sesiones...', 'event_date' => now()->addDays(5), 'student_creator_id' => $students[2]],
            ['id_activity' => 710, 'activity_name' => 'Campaña de Donación de Sangre', 'activity_type' => 'Integración', 'description' => 'Jornada de donación voluntaria de sangre...', 'event_date' => now()->addDays(8), 'student_creator_id' => $students[3]],
            ['id_activity' => 711, 'activity_name' => 'Campaña de Reciclaje "Campus Verde"', 'activity_type' => 'Deportiva', 'description' => 'Recolección de plásticos, papel y vidrio...', 'event_date' => now()->addDays(14), 'student_creator_id' => $students[4]],
            ['id_activity' => 712, 'activity_name' => 'Voluntariado: Apoyo Escolar en Comunidades', 'activity_type' => 'Cultural', 'description' => 'Programa de reforzamiento académico...', 'event_date' => now()->addDays(6), 'student_creator_id' => $students[5]],
        ];
        $created = 0;
        foreach ($activities as $activity) {
            // $exists = ... // Tabla vacía
            // if (!$exists) {
                DB::table('student_wellbeing_extracurricular_activities')->insert($activity);
                $created++;
            // }
        }
        echo "  → {$created} actividades extracurriculares creadas\n";
    }
    
    /** Seed sesiones de tutoría (Esta tabla SÍ se vació) */
    private function seedTutoringSessions(): void
    {
        $students = DB::table('students')->where('user_id', '>=', 701)->limit(6)->pluck('id')->toArray();
        $instructors = DB::table('instructors')->where('user_id', '>=', 701)->limit(3)->pluck('id')->toArray();
        if (empty($students) || empty($instructors)) { echo "  ⚠️  No hay estudiantes o instructores disponibles para crear tutorías\n"; return; }
        $maxId = DB::table('student_wellbeing_tutorings')->max('id') ?? 0;
        if ($maxId < 701) { echo "  → Tutorías: ID máximo actual es {$maxId}\n"; }

        $tutoringSessions = [
            ['estudent_id' => $students[0], 'instructor_id' => $instructors[0], 'scheduled_date' => now()->addDays(2)->setTime(14, 0), 'type_tutorial' => 'Académica', 'state' => 'Agendada'],
            ['estudent_id' => $students[1], 'instructor_id' => $instructors[0], 'scheduled_date' => now()->addDays(3)->setTime(16, 0), 'type_tutorial' => 'Académica', 'state' => 'Agendada'],
            ['estudent_id' => $students[2], 'instructor_id' => $instructors[0], 'scheduled_date' => now()->subDays(5)->setTime(10, 0), 'type_tutorial' => 'Académica', 'state' => 'Realizada'],
            ['estudent_id' => $students[3], 'instructor_id' => $instructors[1], 'scheduled_date' => now()->addDays(1)->setTime(15, 0), 'type_tutorial' => 'Académica', 'state' => 'Agendada'],
            ['estudent_id' => $students[4], 'instructor_id' => $instructors[1], 'scheduled_date' => now()->subDays(3)->setTime(14, 0), 'type_tutorial' => 'Académica', 'state' => 'Realizada'],
            ['estudent_id' => $students[5], 'instructor_id' => $instructors[1], 'scheduled_date' => now()->addDays(4)->setTime(11, 0), 'type_tutorial' => 'Académica', 'state' => 'Agendada'],
            ['estudent_id' => $students[0], 'instructor_id' => $instructors[1], 'scheduled_date' => now()->subDays(7)->setTime(16, 0), 'type_tutorial' => 'Académica', 'state' => 'Realizada'],
            ['estudent_id' => $students[1], 'instructor_id' => $instructors[2], 'scheduled_date' => now()->addDays(5)->setTime(9, 0), 'type_tutorial' => 'Académica', 'state' => 'Agendada'],
            ['estudent_id' => $students[2], 'instructor_id' => $instructors[2], 'scheduled_date' => now()->subDays(2)->setTime(13, 0), 'type_tutorial' => 'Académica', 'state' => 'Realizada'],
            ['estudent_id' => $students[3], 'instructor_id' => $instructors[2], 'scheduled_date' => now()->addDays(6)->setTime(10, 0), 'type_tutorial' => 'Psicológica', 'state' => 'Agendada'],
            ['estudent_id' => $students[4], 'instructor_id' => $instructors[0], 'scheduled_date' => now()->subDays(1)->setTime(15, 0), 'type_tutorial' => 'Psicológica', 'state' => 'Realizada'],
            ['estudent_id' => $students[5], 'instructor_id' => $instructors[1], 'scheduled_date' => now()->addDays(7)->setTime(17, 0), 'type_tutorial' => 'Psicológica', 'state' => 'Agendada'],
            ['estudent_id' => $students[0], 'instructor_id' => $instructors[2], 'scheduled_date' => now()->subDays(4)->setTime(11, 0), 'type_tutorial' => 'Psicológica', 'state' => 'Realizada'],
            ['estudent_id' => $students[1], 'instructor_id' => $instructors[0], 'scheduled_date' => now()->addDays(8)->setTime(14, 0), 'type_tutorial' => 'Académica', 'state' => 'Agendada'],
            ['estudent_id' => $students[2], 'instructor_id' => $instructors[1], 'scheduled_date' => now()->subDays(6)->setTime(10, 0), 'type_tutorial' => 'Psicológica', 'state' => 'Realizada'],
        ];
        $created = 0;
        foreach ($tutoringSessions as $session) {
            // $exists = ... // Tabla vacía
            // if (!$exists) {
                DB::table('student_wellbeing_tutorings')->insert($session);
                $created++;
            // }
        }
        echo "  → {$created} sesiones de tutoría creadas\n";
    }
    
    /** Seed asistencias a tutorías (Esta tabla SÍ se vació) */
    private function seedTutoringAssistances(): void
    {
        $completedTutorings = DB::table('student_wellbeing_tutorings')->where('state', 'Realizada')->pluck('id')->toArray();
        if (empty($completedTutorings)) { echo "  ⚠️  No hay tutorías completadas para registrar asistencias\n"; return; }
        $maxId = DB::table('student_wellbeing_tutoring_assistances')->max('id') ?? 0;
        if ($maxId < 701) { echo "  → Asistencias: ID máximo actual es {$maxId}\n"; }

        $observations = [
            'Estudiante muy participativo, resolvió todos los ejercicios propuestos. Muestra gran mejora.',
            'Excelente predisposición. Comprende los conceptos y hace preguntas pertinentes.',
            'Asistió puntualmente. Logró entender los temas tratados y completó la práctica satisfactoriamente.',
            'Muy motivado/a. Se recomienda continuar con las sesiones de reforzamiento.',
            'Resolvió ejercicios de nivel intermedio sin dificultad. Avance notable desde la última sesión.',
            'Buena actitud y compromiso. Está listo/a para el examen parcial.',
            'Asistió pero llegó 15 minutos tarde. Se recomienda puntualidad para aprovechar toda la sesión.',
            'Mostró dificultades en algunos temas. Se sugiere una sesión adicional antes del examen.',
            'Poco participativo/a en esta sesión. Se recomienda repasar conceptos básicos en casa.',
            'Asistió con dudas específicas que fueron resueltas. Necesita practicar más ejercicios.',
        ];
        $created = 0;
        foreach ($completedTutorings as $tutoringId) {
            // $exists = ... // Tabla vacía
            // if (!$exists) {
                $attended = rand(1, 10) <= 9;
                DB::table('student_wellbeing_tutoring_assistances')->insert([
                    'tutoring_id' => $tutoringId,
                    'attended' => $attended,
                    'observations' => $attended ? $observations[array_rand($observations)] : 'No asistió. Se reprogramará la sesión.',
                    'registration_date' => now()->subDays(rand(1, 7)),
                ]);
                $created++;
            // }
        }
        echo "  → {$created} asistencias a tutorías registradas\n";
    }
}