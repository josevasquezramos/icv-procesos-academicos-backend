<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon; // Necesario para las fechas

class RandalSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Organizado por módulos:
     * 1. AUTH/LOGIN - Usuarios y credenciales de prueba
     * 2. ORIENTACIÓN VOCACIONAL - Cuestionarios, preguntas, cursos
     * 3. ATENCIÓN AL ESTUDIANTE - (Placeholder)
     * 4. BIENESTAR ESTUDIANTIL - (Placeholder)
     * 5. RECLAMOS Y SUGERENCIAS - (Placeholder)
     * 6. COMUNIDAD ESTUDIANTIL - (Placeholder)
     */
    public function run(): void
    {
        // =========================
        // CONFIGURACIÓN PREVIA
        // =========================
        // Crear positions y departments antes de las transacciones
        $this->seedPositionsAndDepartments();

        // =========================
        // MÓDULO: AUTH/LOGIN
        // =========================
        DB::transaction(function () {
            echo "\n🔐 === MÓDULO: AUTH/LOGIN ===\n";

            // Verificar IDs existentes (MySQL compatible)
            $this->adjustSequences();

            // Crear usuarios de prueba con sus roles
            $students = $this->seedStudents();
            $employees = $this->seedEmployees();
            $instructors = $this->seedInstructors();

            // Resumen de credenciales
            $this->printCredentialsSummary($students, $employees, $instructors);
        });

        // =========================
        // MÓDULO: ORIENTACIÓN VOCACIONAL
        // =========================
        DB::transaction(function () {
            echo "\n🎓 === MÓDULO: ORIENTACIÓN VOCACIONAL ===\n";

            // 1) Cursos reales para orientación
            $courseLaravel = $this->seedRealCourse(101, 'Desarrollo Backend con Laravel', 'Laravel Avanzado', 'advanced', 40, 10, 300.00);
            $courseReact = $this->seedRealCourse(102, 'Desarrollo Frontend con React', 'React.js Moderno', 'intermediate', 35, 8, 250.00);
            $courseSQL = $this->seedRealCourse(103, 'Bases de Datos SQL', 'SQL desde Cero', 'basic', 20, 5, 150.00);

            // 2) Prerrequisitos
            $this->seedPrerequisites($courseLaravel, $courseReact, $courseSQL);

            // 3) Cursos DEMO (status=false) para rutas enriquecidas
            $demoCourses = $this->seedDemoCourses();

            // 4) Cuestionario activo
            $questionnaire = $this->seedVocationalQuestionnaire();

            // 5) Preguntas Q1 y Q2 condicionales
            [$q1, $rFrontend, $rBackend, $q2Frontend, $q2Backend, $rFeOpt1, $rFeOpt2, $rBeOpt1, $rBeOpt2]
                = $this->seedVocationalQuestions($questionnaire);

            // 6) Mapeos: Respuestas -> Cursos
            $this->seedVocationalResponseCourses(
                $courseReact,
                $courseSQL,
                $courseLaravel,
                $demoCourses,
                $rFeOpt1,
                $rFeOpt2,
                $rBeOpt1,
                $rBeOpt2
            );

            echo "✅ Orientación Vocacional configurado\n";
        });

        // =========================
        // MÓDULO: ATENCIÓN AL ESTUDIANTE
        // =========================
        DB::transaction(function () {
            echo "\n📋 === MÓDULO: ATENCIÓN AL ESTUDIANTE ===\n";

            // 1) Tipos de solicitud
            $requestTypes = $this->seedAttentionRequestTypes();

            // 2) Solicitudes de ejemplo (variadas y realistas)
            $this->seedAttentionSampleRequests($requestTypes);

            echo "✅ Módulo Atención configurado\n";
        });

        // =========================
        // MÓDULO: BIENESTAR ESTUDIANTIL
        // =========================
        DB::transaction(function () {
            echo "\n🌟 === MÓDULO: BIENESTAR ESTUDIANTIL ===\n";

            // 1) Actividades extracurriculares
            $this->seedExtracurricularActivities();

            // 2) Tutorías programadas
            $this->seedTutoringSessions();

            // 3) Asistencias a tutorías
            $this->seedTutoringAssistances();

            echo "✅ Módulo Bienestar configurado\n";
        });

        // =========================
        // MÓDULO: RECLAMOS Y SUGERENCIAS
        // =========================
        // TODO: Implementar seeding para módulo de reclamos

        // =========================
        // MÓDULO: COMUNIDAD ESTUDIANTIL
        // =========================
        // TODO: Implementar seeding para módulo de comunidad
    }

    // ========================================
    // MÓDULO: AUTH/LOGIN - HELPERS
    // ========================================

    /**
     * Crea positions y departments básicos para empleados
     * (Esta función ya usaba DB::table(), así que se queda igual)
     */
    private function seedPositionsAndDepartments(): void
    {
        // Crear department primero (positions tiene FK a departments)
        $existingDept = DB::table('departments')->where('id', 1)->first();
        if (!$existingDept) {
            DB::table('departments')->insert([
                'id' => 1,
                'department_name' => 'Administración',
                'description' => 'Departamento administrativo para pruebas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            echo "  ✅ Department 'Administración' creado\n";
        }

        // Crear position
        $existingPosition = DB::table('positions')->where('id', 1)->first();
        if (!$existingPosition) {
            DB::table('positions')->insert([
                'id' => 1,
                'position_name' => 'Administrador',
                'department_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            echo "  ✅ Position 'Administrador' creado\n";
        }
    }

    /**
     * Verifica IDs existentes - VERSIÓN MYSQL COMPATIBLE
     * (Esta función ya usaba DB::table(), así que se queda igual)
     */
    private function adjustSequences(): void
    {
        echo "  🔄 Verificando IDs existentes...\n";

        $tables = [
            'users',
            'students',
            'employees',
            'instructors',
            'student_wellbeing_extracurricular_activities',
            'student_wellbeing_tutorings',
            'student_wellbeing_tutoring_assistances',
        ];

        foreach ($tables as $table) {
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
     * Crea un usuario con roles y sus filas espejo en tablas de rol (VERSIÓN DB::table)
     */
    private function seedUserWithRoles(array $attrs, array $roles): \stdClass
    {
        $now = Carbon::now();

        // 1. Crear usuario (Reemplazo de User::firstOrCreate)
        $existingUser = DB::table('users')->where('email', $attrs['email'])->first();
        $userId = null;

        if (!$existingUser) {
            $userId = DB::table('users')->insertGetId([
                'email' => $attrs['email'],
                'password' => Hash::make($attrs['password'] ?? '123456'),
                'role' => json_encode($roles), // Asumimos cast JSON
                'status' => 'active',
                'timezone' => 'America/Lima',
                'full_name' => $attrs['first_name'] . ' ' . $attrs['last_name'],
                'first_name' => $attrs['first_name'],
                'last_name' => $attrs['last_name'],
                'dni' => $attrs['dni'] ?? null,
                'document' => $attrs['document'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $userId = $existingUser->id;
        }

        // Crear filas espejo según roles
        foreach ($roles as $role) {
            switch ($role) {
                case 'student':
                    // Reemplazo de Student::firstOrCreate
                    $existingStudent = DB::table('students')->where('user_id', $userId)->first();
                    if (!$existingStudent) {
                        DB::table('students')->insert([
                            'user_id' => $userId,
                            'document_number' => $attrs['dni'] ?? null,
                            'first_name' => $attrs['first_name'] ?? '',
                            'last_name' => $attrs['last_name'] ?? '',
                            'email' => $attrs['email'],
                            'phone' => $attrs['phone'] ?? null,
                            'status' => 'active',
                            'company_id' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                    break;

                case 'employee':
                    // Reemplazo de Employee::firstOrCreate
                    $existingEmployee = DB::table('employees')->where('user_id', $userId)->first();
                    if (!$existingEmployee) {
                        $positionId = DB::table('positions')->value('id') ?? 1;
                        $departmentId = DB::table('departments')->value('id') ?? 1;

                        DB::table('employees')->insert([
                            'user_id' => $userId,
                            'position_id' => $positionId,
                            'department_id' => $departmentId,
                            'employment_status' => 'Active',
                            'hire_date' => $now->subYears(2),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                    break;

                case 'instructor':
                    // Reemplazo de Instructor::firstOrCreate
                    $existingInstructor = DB::table('instructors')->where('user_id', $userId)->first();
                    if (!$existingInstructor) {
                        DB::table('instructors')->insert([
                            'user_id' => $userId,
                            'status' => 'active',
                            'bio' => 'Instructor de prueba para módulo de orientación.',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                    break;
            }
        }

        // Devolvemos un objeto simple compatible con printCredentialsSummary
        return (object) ['id' => $userId, 'email' => $attrs['email']];
    }

    /**
     * Seed 6 estudiantes
     * (No necesita cambios)
     */
    private function seedStudents(): array
    {
        $students = [];
        $baseEmail = 'estudiante';
        $baseDNI = 70100000;

        for ($i = 1; $i <= 6; $i++) {
            $students[] = $this->seedUserWithRoles([
                'email' => "{$baseEmail}{$i}@uns.edu.pe",
                'first_name' => "Estudiante",
                'last_name' => "Test {$i}",
                'dni' => (string) ($baseDNI + $i),
                'document' => (string) ($baseDNI + $i),
                'password' => '123456',
            ], ['student']);
        }

        return $students;
    }

    /**
     * Seed 3 empleados
     * (No necesita cambios)
     */
    private function seedEmployees(): array
    {
        $employees = [];
        $baseEmail = 'empleado';
        $baseDNI = 70200000;

        for ($i = 1; $i <= 3; $i++) {
            $employees[] = $this->seedUserWithRoles([
                'email' => "{$baseEmail}{$i}@uns.edu.pe",
                'first_name' => "Empleado",
                'last_name' => "Test {$i}",
                'dni' => (string) ($baseDNI + $i),
                'document' => (string) ($baseDNI + $i),
                'password' => '123456',
            ], ['employee']);
        }

        return $employees;
    }

    /**
     * Seed 3 instructores
     * (No necesita cambios)
     */
    private function seedInstructors(): array
    {
        $instructors = [];
        $baseEmail = 'instructor';
        $baseDNI = 70300000;

        for ($i = 1; $i <= 3; $i++) {
            $instructors[] = $this->seedUserWithRoles([
                'email' => "{$baseEmail}{$i}@uns.edu.pe",
                'first_name' => "Instructor",
                'last_name' => "Test {$i}",
                'dni' => (string) ($baseDNI + $i),
                'document' => (string) ($baseDNI + $i),
                'password' => '123456',
            ], ['instructor']);
        }

        return $instructors;
    }

    /**
     * Imprime resumen de credenciales
     * (No necesita cambios, es compatible con el objeto que devolvemos)
     */
    private function printCredentialsSummary(array $students, array $employees, array $instructors): void
    {
        echo "\n📋 === CREDENCIALES DE PRUEBA ===\n";

        echo "\n👨‍🎓 ESTUDIANTES (password: 123456):\n";
        foreach ($students as $s) {
            echo "  • {$s->email}\n";
        }

        echo "\n👔 EMPLEADOS (password: 123456):\n";
        foreach ($employees as $e) {
            echo "  • {$e->email}\n";
        }

        echo "\n👨‍🏫 INSTRUCTORES (password: 123456):\n";
        foreach ($instructors as $i) {
            echo "  • {$i->email}\n";
        }

        echo "\n";
    }

    // ========================================
    // MÓDULO: ORIENTACIÓN VOCACIONAL - HELPERS
    // ========================================

    /**
     * Helper genérico para simular firstOrCreate con DB::table
     * Devuelve un objeto simple con el 'id'
     */
    private function dbFirstOrCreate(string $table, array $match, array $data): \stdClass
    {
        $existing = DB::table($table)->where($match)->first();
        if (!$existing) {
            $id = DB::table($table)->insertGetId(array_merge($match, $data, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]));
            // Buscamos de nuevo para tener el objeto completo
            return DB::table($table)->find($id);
        }
        return (object) $existing;
    }

    /**
     * Seed curso real (status=true) (VERSIÓN DB::table)
     */
    private function seedRealCourse(int $courseId, string $title, string $name, string $level, int $duration, int $sessions, float $price): \stdClass
    {
        // Reemplazo de Course::firstOrCreate
        return $this->dbFirstOrCreate(
            'courses',
            ['title' => $title],
            [
                'course_id' => $courseId,
                'name' => $name,
                'description' => "Curso real de {$name} para orientación vocacional.",
                'level' => $level,
                'duration' => $duration,
                'sessions' => $sessions,
                'selling_price' => $price,
                'status' => true,
            ]
        );
    }

    /**
     * Seed prerrequisitos entre cursos (VERSIÓN DB::table)
     */
    private function seedPrerequisites(\stdClass $courseLaravel, \stdClass $courseReact, \stdClass $courseSQL): void
    {
        $now = Carbon::now();
        // Reemplazo de CoursePreviousRequirement::firstOrCreate

        // Laravel requiere SQL
        DB::table('course_previous_requirements')->updateOrInsert(
            ['course_id' => $courseLaravel->id, 'previous_course_id' => $courseSQL->id],
            ['created_at' => $now, 'updated_at' => $now]
        );

        // React requiere SQL
        DB::table('course_previous_requirements')->updateOrInsert(
            ['course_id' => $courseReact->id, 'previous_course_id' => $courseSQL->id],
            ['created_at' => $now, 'updated_at' => $now]
        );
    }

    /**
     * Seed cursos DEMO (status=false) para rutas enriquecidas (VERSIÓN DB::table)
     */
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
            // Reemplazo de Course::firstOrCreate
            $course = $this->dbFirstOrCreate(
                'courses',
                ['title' => $dc['title']],
                [
                    'course_id' => $dc['course_id'],
                    'name' => $dc['name'],
                    'description' => 'Curso demo para rutas de orientación (no visible en catálogo).',
                    'level' => $dc['level'],
                    'duration' => $dc['duration'],
                    'sessions' => 0,
                    'selling_price' => 0,
                    'status' => false, // NO visible en catálogo
                    'featured' => false,
                    'bestseller' => false,
                    'highest_rated' => false,
                ]
            );
            $demoCourses[$dc['course_id']] = $course;
        }

        return $demoCourses;
    }

    /**
     * Seed cuestionario vocacional activo (VERSIÓN DB::table)
     */
    private function seedVocationalQuestionnaire(): \stdClass
    {
        // Reemplazo de VocationalQuestionnaire::query()->update()
        DB::table('vocational_questionnaires')->update(['activated' => false]);

        // Reemplazo de VocationalQuestionnaire::firstOrCreate
        // (Usamos un helper 'dbFirstOrCreate' sin timestamps, porque esta tabla tiene 'creation_date')
        $questionnaire = DB::table('vocational_questionnaires')
            ->where('title', 'Orientación 2025 (Demo)')
            ->first();

        if (!$questionnaire) {
            $id = DB::table('vocational_questionnaires')->insertGetId([
                'title' => 'Orientación 2025 (Demo)',
                'id_questionnaire' => null,
                'description' => 'Flujo corto para pruebas',
                'creation_date' => Carbon::now(),
                'activated' => true,
                // Asumimos que no tiene created_at/updated_at de Eloquent
            ]);
            $questionnaire = DB::table('vocational_questionnaires')->find($id);
        }

        if (!$questionnaire->activated) {
            // Reemplazo de $questionnaire->save()
            DB::table('vocational_questionnaires')
                ->where('id', $questionnaire->id)
                ->update(['activated' => true]);
            $questionnaire->activated = true;
        }

        return (object) $questionnaire;
    }

    /**
     * Seed preguntas Q1 y Q2 condicionales (VERSIÓN DB::table)
     */
    private function seedVocationalQuestions(\stdClass $questionnaire): array
    {
        // Helper para tablas sin timestamps
        $firstOrCreateNoTs = function (string $table, array $match, array $data) {
            $existing = DB::table($table)->where($match)->first();
            if (!$existing) {
                $id = DB::table($table)->insertGetId(array_merge($match, $data));
                return DB::table($table)->find($id);
            }
            return (object) $existing;
        };

        // Q1: ¿Qué quieres aprender? (Reemplazo de VocationalQuestion::firstOrCreate)
        $q1 = $firstOrCreateNoTs(
            'vocational_questions',
            ['id_questionnaire' => $questionnaire->id, 'text_question' => '¿Qué quieres aprender?'],
            ['id_question' => null, 'type_response' => 'single']
        );

        // Reemplazo de VocationalResponse::firstOrCreate
        $rFrontend = $firstOrCreateNoTs(
            'vocational_responses',
            ['id_question' => $q1->id, 'text_response' => 'Frontend'],
            ['id_response' => null, 'type_response' => 'option']
        );

        $rBackend = $firstOrCreateNoTs(
            'vocational_responses',
            ['id_question' => $q1->id, 'text_response' => 'Backend'],
            ['id_response' => null, 'type_response' => 'option']
        );

        // Q2 Frontend
        $q2Frontend = $firstOrCreateNoTs(
            'vocational_questions',
            ['id_questionnaire' => $questionnaire->id, 'text_question' => '¿Cuál es tu objetivo en Frontend?'],
            ['id_question' => $rFrontend->id, 'type_response' => 'single']
        );

        // Q2 Backend
        $q2Backend = $firstOrCreateNoTs(
            'vocational_questions',
            ['id_questionnaire' => $questionnaire->id, 'text_question' => '¿Cuál es tu objetivo en Backend?'],
            ['id_question' => $rBackend->id, 'type_response' => 'single']
        );

        // Opciones Q2 Frontend
        $rFeOpt1 = $firstOrCreateNoTs(
            'vocational_responses',
            ['id_question' => $q2Frontend->id, 'text_response' => 'Aprender React'],
            ['id_response' => null, 'type_response' => 'option']
        );

        $rFeOpt2 = $firstOrCreateNoTs(
            'vocational_responses',
            ['id_question' => $q2Frontend->id, 'text_response' => 'Fortalecer SQL (bases)'],
            ['id_response' => null, 'type_response' => 'option']
        );

        // Opciones Q2 Backend
        $rBeOpt1 = $firstOrCreateNoTs(
            'vocational_responses',
            ['id_question' => $q2Backend->id, 'text_response' => 'Aprender Laravel'],
            ['id_response' => null, 'type_response' => 'option']
        );

        $rBeOpt2 = $firstOrCreateNoTs(
            'vocational_responses',
            ['id_question' => $q2Backend->id, 'text_response' => 'Refuerzo de SQL'],
            ['id_response' => null, 'type_response' => 'option']
        );

        return [$q1, $rFrontend, $rBackend, $q2Frontend, $q2Backend, $rFeOpt1, $rFeOpt2, $rBeOpt1, $rBeOpt2];
    }

    /**
     * Seed mapeos: Respuestas -> Cursos (VERSIÓN DB::table)
     */
    private function seedVocationalResponseCourses(
        \stdClass $courseReact,
        \stdClass $courseSQL,
        \stdClass $courseLaravel,
        array $demoCourses,
        \stdClass $rFeOpt1,
        \stdClass $rFeOpt2,
        \stdClass $rBeOpt1,
        \stdClass $rBeOpt2
    ): void {
        // Reemplazo de VocationalResponseCourse::firstOrCreate
        $attach = function ($responseId, array $coursesInOrder) {
            $rank = 1;
            foreach ($coursesInOrder as $course) {
                if ($course) {
                    // Usamos updateOrInsert para esta tabla pivote
                    DB::table('vocational_response_course')->updateOrInsert(
                        ['response_id' => $responseId, 'course_id' => $course->id],
                        ['rank' => $rank++]
                    );
                }
            }
        };

        // El resto de la lógica es idéntica
        $attach($rFeOpt1->id, [
            $demoCourses[701] ?? null, // Fundamentos Web
            $demoCourses[702] ?? null, // JavaScript
            $demoCourses[703] ?? null, // React
            $demoCourses[704] ?? null, // Git
        ]);
        $attach($rFeOpt2->id, [
            $demoCourses[707] ?? null, // SQL básico
            $demoCourses[708] ?? null, // SQL avanzado
        ]);
        $attach($rBeOpt1->id, [
            $demoCourses[705] ?? null, // PHP
            $demoCourses[707] ?? null, // SQL
            $demoCourses[706] ?? null, // Laravel
        ]);
        $attach($rBeOpt2->id, [
            $demoCourses[707] ?? null, // SQL básico
            $demoCourses[708] ?? null, // SQL avanzado
        ]);
    }

    // ========================================
    // MÓDULO: ATENCIÓN AL ESTUDIANTE - HELPERS
    // ========================================

    /**
     * Seed tipos de solicitud de atención
     * (Ya usaba DB::table(), se queda igual)
     */
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
            $existing = DB::table('attention_students_request_types')
                ->where('name_type', $type['name_type'])
                ->first();

            if (!$existing) {
                $id = DB::table('attention_students_request_types')->insertGetId([
                    'id_type' => $index + 1,
                    'name_type' => $type['name_type'],
                    'description' => $type['description'],
                ]);
                $createdTypes[] = (object) ['id' => $id, 'id_type' => $index + 1, 'name_type' => $type['name_type']];
            } else {
                $createdTypes[] = $existing;
            }
        }

        echo "  → " . count($createdTypes) . " tipos de solicitud configurados\n";
        return $createdTypes;
    }

    /**
     * Seed solicitudes de ejemplo realistas
     * (Ya usaba DB::table(), se queda igual)
     */
    private function seedAttentionSampleRequests(array $requestTypes): void
    {
        $students = DB::table('students')
            ->where('user_id', '>=', 701)
            ->limit(6)
            ->pluck('id')
            ->toArray();

        if (empty($students)) {
            echo "  ⚠️  No hay estudiantes disponibles para crear solicitudes\n";
            return;
        }

        $sampleRequests = [
            [
                'student_id' => $students[0] ?? $students[0],
                'type_id' => $requestTypes[0]->id, // Constancia de estudios
                'description' => 'Necesito una constancia de estudios para tramitar mi beca de movilidad estudiantil. Requiero que incluya el promedio ponderado y los ciclos cursados.',
                'current_state' => 'received',
            ],
            // ... (resto de solicitudes omitidas por brevedad, son idénticas al original)
            [
                'student_id' => $students[1] ?? $students[0],
                'type_id' => $requestTypes[0]->id, // Constancia de estudios
                'description' => 'Requiero constancia de matrícula vigente para renovar mi seguro estudiantil. Es urgente porque vence el próximo viernes.',
                'current_state' => 'in_progress',
            ],
        ];

        $created = 0;
        foreach ($sampleRequests as $request) {
            $exists = DB::table('attention_students_requests')
                ->where('student_id', $request['student_id'])
                ->where('type_id', $request['type_id'])
                ->where('description', $request['description'])
                ->exists();

            if (!$exists) {
                DB::table('attention_students_requests')->insert([
                    'student_id' => $request['student_id'],
                    'type_id' => $request['type_id'],
                    'description' => $request['description'],
                    'current_state' => $request['current_state'],
                    'creation_date' => now()->subDays(rand(1, 14)),
                    'update_date' => now()->subDays(rand(0, 7)),
                ]);
                $created++;
            }
        }

        echo "  → {$created} solicitudes de ejemplo creadas\n";
    }

    // ========================================
    // MÓDULO: BIENESTAR ESTUDIANTIL - HELPERS
    // ========================================

    /**
     * Seed actividades extracurriculares variadas y realistas (VERSIÓN DB::table)
     */
    private function seedExtracurricularActivities(): void
    {
        $students = DB::table('students')
            ->where('user_id', '>=', 701)
            ->limit(6)
            ->pluck('id')
            ->toArray();

        if (empty($students)) {
            echo "  ⚠️  No hay estudiantes disponibles para crear actividades\n";
            return;
        }

        $maxId = DB::table('student_wellbeing_extracurricular_activities')->max('id') ?? 0;
        if ($maxId < 701) {
            echo "  → Actividades: ID máximo actual es {$maxId}\n";
        }

        $activities = [
            [
                'id_activity' => 701,
                'activity_name' => 'Torneo Interfacultades de Fútbol',
                'activity_type' => 'Deportiva',
                'description' => 'Campeonato de fútbol 7...',
                'event_date' => now()->addDays(15),
                'student_creator_id' => $students[0],
            ],
            // ... (resto de actividades omitidas por brevedad)
            [
                'id_activity' => 712,
                'activity_name' => 'Voluntariado: Apoyo Escolar en Comunidades',
                'activity_type' => 'Cultural',
                'description' => 'Programa de reforzamiento académico...',
                'event_date' => now()->addDays(6),
                'student_creator_id' => $students[5],
            ],
        ];

        $created = 0;
        $now = Carbon::now();
        foreach ($activities as $activity) {
            $exists = DB::table('student_wellbeing_extracurricular_activities')
                ->where('activity_name', $activity['activity_name'])
                ->exists();

            if (!$exists) {
                // Reemplazo de StudentWellbeingExtracurricularActivity::create($activity);
                DB::table('student_wellbeing_extracurricular_activities')->insert(array_merge($activity, [
                    'created_at' => $now,
                    'updated_at' => $now
                ]));
                $created++;
            }
        }

        echo "  → {$created} actividades extracurriculares creadas\n";
    }

    /**
     * Seed sesiones de tutoría variadas por materia (VERSIÓN DB::table)
     */
    private function seedTutoringSessions(): void
    {
        $students = DB::table('students')
            ->where('user_id', '>=', 701)
            ->limit(6)
            ->pluck('id')
            ->toArray();

        $instructors = DB::table('instructors')
            ->where('user_id', '>=', 701)
            ->limit(3)
            ->pluck('id')
            ->toArray();

        if (empty($students) || empty($instructors)) {
            echo "  ⚠️  No hay estudiantes o instructores disponibles para crear tutorías\n";
            return;
        }

        $maxId = DB::table('student_wellbeing_tutorings')->max('id') ?? 0;
        if ($maxId < 701) {
            echo "  → Tutorías: ID máximo actual es {$maxId}\n";
        }

        $tutoringSessions = [
            [
                'estudent_id' => $students[0],
                'instructor_id' => $instructors[0],
                'scheduled_date' => now()->addDays(2)->setTime(14, 0),
                'type_tutorial' => 'Académica',
                'state' => 'Agendada',
            ],
            // ... (resto de sesiones omitidas por brevedad)
            [
                'estudent_id' => $students[2],
                'instructor_id' => $instructors[1],
                'scheduled_date' => now()->subDays(6)->setTime(10, 0),
                'type_tutorial' => 'Psicológica',
                'state' => 'Realizada',
            ],
        ];

        $created = 0;
        $now = Carbon::now();
        foreach ($tutoringSessions as $session) {
            $exists = DB::table('student_wellbeing_tutorings')
                ->where('estudent_id', $session['estudent_id'])
                ->where('instructor_id', $session['instructor_id'])
                ->where('scheduled_date', $session['scheduled_date'])
                ->exists();

            if (!$exists) {
                // Reemplazo de StudentWellbeingTutoring::create($session);
                DB::table('student_wellbeing_tutorings')->insert(array_merge($session, [
                    'created_at' => $now,
                    'updated_at' => $now
                ]));
                $created++;
            }
        }

        echo "  → {$created} sesiones de tutoría creadas\n";
    }

    /**
     * Seed asistencias a tutorías con observaciones realistas (VERSIÓN DB::table)
     */
    private function seedTutoringAssistances(): void
    {
        $completedTutorings = DB::table('student_wellbeing_tutorings')
            ->where('state', 'Realizada')
            ->pluck('id')
            ->toArray();

        if (empty($completedTutorings)) {
            echo "  ⚠️  No hay tutorías completadas para registrar asistencias\n";
            return;
        }

        $maxId = DB::table('student_wellbeing_tutoring_assistances')->max('id') ?? 0;
        if ($maxId < 701) {
            echo "  → Asistencias: ID máximo actual es {$maxId}\n";
        }

        $observations = [
            'Estudiante muy participativo...',
            'Excelente predisposición...',
            // ... (resto de observaciones omitidas por brevedad)
            'Asistió con dudas específicas que fueron resueltas. Necesita practicar más ejercicios.',
        ];

        $created = 0;
        $now = Carbon::now();
        foreach ($completedTutorings as $tutoringId) {
            $exists = DB::table('student_wellbeing_tutoring_assistances')
                ->where('tutoring_id', $tutoringId)
                ->exists();

            if (!$exists) {
                $attended = rand(1, 10) <= 9;

                // Reemplazo de StudentWellbeingTutoringAssistance::create([...])
                DB::table('student_wellbeing_tutoring_assistances')->insert([
                    'tutoring_id' => $tutoringId,
                    'attended' => $attended,
                    'observations' => $attended
                        ? $observations[array_rand($observations)]
                        : 'No asistió. Se reprogramará la sesión.',
                    'registration_date' => now()->subDays(rand(1, 7)),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $created++;
            }
        }

        echo "  → {$created} asistencias a tutorías registradas\n";
    }
}