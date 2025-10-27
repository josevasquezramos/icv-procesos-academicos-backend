<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PantaSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Iniciando PantaSeeder... 🚀');

        // 1. Tablas básicas sin dependencias
        $this->command->line('--- 1. Tablas Básicas ---');
        $this->run_departments();
        $this->run_companies();
        $this->run_academic_periods();
        $this->run_courses();
        $this->run_subjects();
        $this->run_payment_methods();
        $this->run_revenue_sources();
        $this->run_accounts();
        $this->run_blocked_ips();

        // 2. Tablas que dependen de las básicas (especialmente Users)
        $this->command->line('--- 2. Tablas de Usuarios y Staff ---');
        $this->run_users(); // Incluye DefaultUsersSeeder y UsersSeeder
        $this->run_positions(); // Incluye PositionSeeder y PositionsSeeder
        $this->run_instructors();
        $this->run_employees(); // Incluye EmployeesSeeder (EmployeeSeeder descartado por IDs fijos)
        
        // 3. Tablas académicas
        $this->command->line('--- 3. Tablas Académicas ---');
        $this->run_course_offerings();
        $this->run_course_instructors(); // Nuevo, referencial
        $this->run_groups();
        $this->run_evaluations();
        
        // 4. Tablas de estudiantes
        $this->command->line('--- 4. Tablas de Estudiantes y Matrículas ---');
        $this->run_students();
        $this->run_enrollments();
        $this->run_enrollment_details();
        
        // 5. Tablas de grupos y clases
        $this->command->line('--- 5. Tablas de Grupos y Clases ---');
        $this->run_group_participants();
        $this->run_classes();
        
        // 6. Tablas principales de registros
        $this->command->line('--- 6. Tablas de Registros (Asistencias, Notas, Finanzas) ---');
        $this->run_attendances();
        $this->run_grade_records();
        $this->run_final_grades();
        $this->run_invoices();
        $this->run_payments();
        $this->run_financial_transactions();
        
        // 7. Tablas de Soporte y Seguridad
        $this->command->line('--- 7. Tablas de Soporte y Seguridad ---');
        $this->run_tickets();
        $this->run_escalations();
        $this->run_security_logs();
        $this->run_security_alerts();
        $this->run_incidents();
        
        // 8. Formularios Web
        $this->command->line('--- 8. Formularios Web ---');
        $this->run_contact_forms(); // Convertido a DB::table y referencial

        $this->command->info('PantaSeeder completado exitosamente. ✅');
    }

    // ----------------------------------------------------------------
    // MÉTODOS PRIVADOS DE SEEDING
    // ----------------------------------------------------------------

    private function run_academic_periods(): void
    {
        $this->command->info('--- Ejecutando Academic Periods ---');
        $periods = [
            [
                'academic_period_id' => 202401, // Esto es un Business Key, no el PK
                'name' => 'Ciclo 2024-1',
                'start_date' => '2024-01-15',
                'end_date' => '2024-06-15',
                'status' => 'open',
                'created_at' => Carbon::now(),
            ],
            [
                'academic_period_id' => 202402, // Esto es un Business Key, no el PK
                'name' => 'Ciclo 2024-2',
                'start_date' => '2024-07-15',
                'end_date' => '2024-12-15',
                'status' => 'open',
                'created_at' => Carbon::now(),
            ],
        ];

        foreach ($periods as $period) {
            DB::table('academic_periods')->updateOrInsert(
                ['academic_period_id' => $period['academic_period_id']],
                $period
            );
        }
    }

    private function run_accounts(): void
    {
        $this->command->info('--- Ejecutando Accounts ---');
        $accounts = [
            [
                'code' => '1001', // Business Key
                'description' => 'Cuenta Corriente Principal',
                'account_type' => 'Asset',
                'current_balance' => 50000.00,
            ],
            [
                'code' => '4001', // Business Key
                'description' => 'Ingresos por Matrículas',
                'account_type' => 'Income',
                'current_balance' => 0.00,
            ],
            [
                'code' => '4002', // Business Key
                'description' => 'Ingresos por Cursos',
                'account_type' => 'Income',
                'current_balance' => 0.00,
            ],
            [
                'code' => '5001', // Business Key
                'description' => 'Gastos Operativos',
                'account_type' => 'Expense',
                'current_balance' => 0.00,
            ],
            [
                'code' => '2001', // Business Key
                'description' => 'Obligaciones Financieras',
                'account_type' => 'Liability',
                'current_balance' => 0.00,
            ],
        ];
        
        foreach ($accounts as $account) {
            DB::table('accounts')->updateOrInsert(
                ['code' => $account['code']],
                $account
            );
        }
    }

    private function run_attendances(): void
    {
        $this->command->info('--- Ejecutando Attendances ---');
        $groupParticipants = DB::table('group_participants')->where('role', 'student')->get();
        $classes = DB::table('classes')->get();

        if ($groupParticipants->isEmpty() || $classes->isEmpty()) {
            $this->command->warn('No hay participantes de grupo o clases para crear asistencias.');
            return;
        }

        $attendances = [];

        foreach ($groupParticipants as $participant) {
            $randomClasses = $classes->random(min(5, $classes->count()));
            
            foreach ($randomClasses as $class) {
                $attended = (bool)rand(0, 1);
                
                $attendances[] = [
                    'group_participant_id' => $participant->id,
                    'class_id' => $class->id,
                    'attended' => $attended,
                    'observations' => $attended ? 'Asistencia registrada correctamente' : 'Inasistencia justificada',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        $uniqueAttendances = collect($attendances)->unique(function ($item) {
            return $item['group_participant_id'] . '-' . $item['class_id'];
        })->values()->all();

        if (!empty($uniqueAttendances)) {
            DB::table('attendances')->insert($uniqueAttendances);
            $this->command->info('Asistencias creadas: ' . count($uniqueAttendances));
        }
    }

    private function run_blocked_ips(): void
    {
        $this->command->info('--- Ejecutando Blocked IPs ---');
        $blockedIPs = [];
        $reasons = [
            'Intento de acceso no autorizado',
            'Actividad sospechosa repetitiva',
            'Ataque de fuerza bruta detectado',
            'Comportamiento malicioso identificado'
        ];

        for ($i = 0; $i < 10; $i++) {
            $blockedIPs[] = [
                // 'id_blocked_ip' => $blockId + $i, // REMOVIDO: Dejar que la BD asigne el ID
                'ip_address' => '192.168.' . rand(1, 255) . '.' . rand(1, 255),
                'reason' => $reasons[rand(0, 3)],
                'block_date' => Carbon::now()->subDays(rand(1, 60)),
                'active' => (bool)rand(0, 1),
            ];
        }

        DB::table('blocked_ips')->insert($blockedIPs);
    }

    private function run_classes(): void
    {
        $this->command->info('--- Ejecutando Classes ---');
        $groups = DB::table('groups')->get();
        $classes = [];

        foreach ($groups as $group) {
            for ($i = 1; $i <= 10; $i++) {
                $classDate = Carbon::now()->addDays($i * 2);
                
                $classes[] = [
                    'group_id' => $group->id,
                    'class_name' => 'Clase ' . $i . ' - ' . $group->name,
                    'description' => 'Sesión ' . $i . ' del curso ' . $group->name,
                    'class_date' => $classDate->format('Y-m-d'),
                    'start_time' => '18:00:00',
                    'end_time' => '20:00:00',
                    'meeting_url' => 'https://zoom.us/j/' . rand(100000000, 999999999),
                    'class_status' => 'SCHEDULED',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('classes')->insert($classes);
        $this->command->info('Clases creadas: ' . count($classes));
    }

    private function run_companies(): void
    {
        $this->command->info('--- Ejecutando Companies ---');
        $companies = [
            [
                'name' => 'Tech Solutions SAC',
                'industry' => 'Tecnología',
                'contact_name' => 'Roberto Silva',
                'contact_email' => 'r.silva@techsolutions.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Innovate Perú EIRL',
                'industry' => 'Consultoría',
                'contact_name' => 'Laura Mendoza',
                'contact_email' => 'l.mendoza@innovateperu.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        foreach ($companies as $company) {
            DB::table('companies')->updateOrInsert(
                ['name' => $company['name']],
                $company
            );
        }
    }

    private function run_contact_forms(): void
    {
        $this->command->info('--- Ejecutando Contact Forms (Convertido a DB::table) ---');
        
        // Obtenemos usuarios reales para asignar
        $user1 = DB::table('users')->where('email', 'admin@email.com')->first();
        $user2 = DB::table('users')->where('email', 'maria.garcia@email.com')->first();
        
        $assignee1 = $user1 ? $user1->id : null;
        $assignee2 = $user2 ? $user2->id : null;

        $contactForms = [
            [
                // 'id_contact' => 1, // REMOVIDO
                'full_name' => 'María López',
                'email' => 'maria.lopez@ejemplo.com',
                'phone' => '+51 987654321',
                'company' => 'Tech Solutions SAC',
                'subject' => 'Consulta sobre cursos corporativos',
                'message' => 'Me gustaría información sobre los cursos de desarrollo web...',
                'form_type' => 'sales',
                'status' => 'pending',
                'submission_date' => now()->subDays(2)
            ],
            [
                // 'id_contact' => 2, // REMOVIDO
                'full_name' => 'Carlos Ramírez',
                'email' => 'carlos.ramirez@empresa.com',
                'phone' => '+51 987654322',
                'company' => 'Innovatech Perú',
                'subject' => 'Solicitud de cotización',
                'message' => 'Necesito una cotización para el desarrollo...',
                'form_type' => 'quote',
                'status' => 'in_progress',
                'assigned_to' => $assignee1, // CORREGIDO: ID real
                'submission_date' => now()->subDays(1)
            ],
            [
                // 'id_contact' => 3, // REMOVIDO
                'full_name' => 'Laura Mendoza',
                'email' => 'laura.mendoza@gmail.com',
                'phone' => '+51 987654323',
                'company' => null,
                'subject' => 'Problema con el certificado',
                'message' => 'He completado el curso pero no puedo descargar mi certificado.',
                'form_type' => 'support',
                'status' => 'responded',
                'assigned_to' => $assignee2, // CORREGIDO: ID real
                'response' => 'Estimada Laura, hemos verificado tu caso...',
                'response_date' => now()->subHours(5),
                'submission_date' => now()->subDays(3)
            ],
            [
                // 'id_contact' => 4, // REMOVIDO
                'full_name' => 'Roberto Silva',
                'email' => 'roberto.silva@outlook.com',
                'phone' => '+51 987654324',
                'company' => 'Digital Solutions',
                'subject' => 'Alianza estratégica',
                'message' => 'Estamos interesados en establecer una alianza...',
                'form_type' => 'partnership',
                'status' => 'pending',
                'submission_date' => now()->subHours(12)
            ],
            [
                // 'id_contact' => 5, // REMOVIDO
                'full_name' => 'Promoción Gratuita',
                'email' => 'spam@fake.com',
                'phone' => null,
                'company' => 'Spam Company',
                'subject' => 'Gana dinero fácil',
                'message' => '¡Promoción especial! Gana $5000...',
                'form_type' => 'general',
                'status' => 'spam',
                'submission_date' => now()->subDays(1)
            ]
        ];

        // Insertar solo si no existen (basado en email y fecha de envío)
        foreach ($contactForms as $contactForm) {
            DB::table('contact_forms')->updateOrInsert(
                [
                    'email' => $contactForm['email'], 
                    'submission_date' => $contactForm['submission_date']
                ],
                $contactForm
            );
        }
    }

    private function run_course_instructors(): void
    {
        $this->command->info('--- Ejecutando Course Instructors (Referencial) ---');
        
        // Obtenemos cursos por un identificador único (título)
        $courseFS = DB::table('courses')->where('title', 'Desarrollo Web Full Stack')->first();
        $courseDS = DB::table('courses')->where('title', 'Data Science con Python')->first();
        $courseIntro = DB::table('courses')->where('title', 'Introducción a la Programación')->first();

        // Obtenemos instructores buscando por el email del usuario asociado
        $userCarlos = DB::table('users')->where('email', 'roberto.rodriguez@email.com')->first();
        $userLaura = DB::table('users')->where('email', 'laura.silva@email.com')->first();

        if (!$userCarlos || !$userLaura) {
            $this->command->warn('Usuarios instructores base (roberto.rodriguez/laura.silva) no encontrados. Saltando CourseInstructors.');
            return;
        }

        $instCarlos = DB::table('instructors')->where('user_id', $userCarlos->id)->first();
        $instLaura = DB::table('instructors')->where('user_id', $userLaura->id)->first();

        if (!$instCarlos || !$instLaura) {
            $this->command->warn('Registros de Instructores no encontrados. Saltando CourseInstructors.');
            return;
        }

        $assignments = [];
        
        // Asignación 1: Full Stack - Carlos
        if ($courseFS) {
            $assignments[] = [
                // 'id_course_inst' => ..., // REMOVIDO
                'instructor_id' => $instCarlos->id,
                'course_id' => $courseFS->id,
                'assigned_date' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }
        
        // Asignación 2: Data Science - Laura
        if ($courseDS) {
            $assignments[] = [
                // 'id_course_inst' => ..., // REMOVIDO
                'instructor_id' => $instLaura->id,
                'course_id' => $courseDS->id,
                'assigned_date' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }
        
        // Asignación 3 y 4: Intro Prog - Carlos y Laura
        if ($courseIntro) {
            $assignments[] = [
                // 'id_course_inst' => ..., // REMOVIDO
                'instructor_id' => $instCarlos->id,
                'course_id' => $courseIntro->id,
                'assigned_date' => Carbon::now()->subDays(rand(1, 30)),
            ];
            $assignments[] = [
                // 'id_course_inst' => ..., // REMOVIDO
                'instructor_id' => $instLaura->id,
                'course_id' => $courseIntro->id,
                'assigned_date' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }
        
        if (!empty($assignments)) {
            // Insertar solo si no existe la combinación
            foreach ($assignments as $assignment) {
                DB::table('course_instructors')->updateOrInsert(
                    [
                        'instructor_id' => $assignment['instructor_id'], 
                        'course_id' => $assignment['course_id']
                    ],
                    $assignment
                );
            }
            $this->command->info('Asignaciones curso-instructor creadas: ' . count($assignments));
        }
    }

    private function run_course_offerings(): void
    {
        $this->command->info('--- Ejecutando Course Offerings ---');
        $courses = DB::table('courses')->get();
        $academicPeriods = DB::table('academic_periods')->get();
        $instructors = DB::table('instructors')->get();

        if ($instructors->isEmpty()) {
            $this->command->warn('No hay instructores. Creando instructor temporal...');
            
            $user = DB::table('users')->where('email', 'instructor.temporal@email.com')->first();
            if (!$user) {
                $userId = DB::table('users')->insertGetId([
                    'first_name' => 'Instructor', 'last_name' => 'Temporal', 'full_name' => 'Instructor Temporal',
                    'dni' => '99999999', 'document' => '99999999', 'email' => 'instructor.temporal@email.com',
                    'phone_number' => '+51999999999', 'password' => bcrypt('password123'),
                    'gender' => 'male', 'country' => 'Perú', 'role' => json_encode(['instructor']),
                    'status' => 'active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                ]);
            } else {
                $userId = $user->id;
            }

            DB::table('instructors')->insert([
                // 'instructor_id' => 9999, // REMOVIDO
                'user_id' => $userId,
                'bio' => 'Instructor temporal para seeders',
                'expertise_area' => 'Desarrollo General',
                'status' => 'active',
                'created_at' => Carbon::now(),
            ]);

            $instructors = DB::table('instructors')->get();
        }

        if ($courses->isEmpty() || $academicPeriods->isEmpty()) {
            $this->command->warn('No hay Cursos o Periodos Académicos. Saltando CourseOfferings.');
            return;
        }

        $courseOfferings = [];
        foreach ($courses as $course) {
            $courseOfferings[] = [
                // 'course_offering_id' => ..., // REMOVIDO
                'course_id' => $course->id,
                'academic_period_id' => $academicPeriods->random()->id,
                'instructor_id' => $instructors->random()->id,
                'schedule' => 'Lunes y Miércoles 18:00-20:00',
                'delivery_method' => 'virtual',
                'created_at' => Carbon::now(),
            ];
        }

        DB::table('course_offerings')->insert($courseOfferings);
        $this->command->info('Course offerings creados: ' . count($courseOfferings));
    }

    private function run_courses(): void
    {
        $this->command->info('--- Ejecutando Courses ---');
        $courses = [
            [
                // 'course_id' => 1001, // REMOVIDO
                'title' => 'Desarrollo Web Full Stack',
                'name' => 'Full Stack Developer',
                'description' => 'Curso completo de desarrollo web frontend y backend',
                'level' => 'intermediate',
                'duration' => 120.50, 'sessions' => 40, 'selling_price' => 1500.00, 'discount_price' => 1200.00,
                'prerequisites' => 'Conocimientos básicos de programación',
                'certificate_name' => true, 'certificate_issuer' => 'Academia Tech',
                'bestseller' => true, 'featured' => true, 'highest_rated' => true, 'status' => true,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                // 'course_id' => 1002, // REMOVIDO
                'title' => 'Data Science con Python',
                'name' => 'Data Science Professional',
                'description' => 'Aprende análisis de datos y machine learning con Python',
                'level' => 'advanced',
                'duration' => 180.25, 'sessions' => 60, 'selling_price' => 2000.00, 'discount_price' => 1600.00,
                'prerequisites' => 'Conocimientos de Python y estadística',
                'certificate_name' => true, 'certificate_issuer' => 'Academia Tech',
                'bestseller' => true, 'featured' => false, 'highest_rated' => true, 'status' => true,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                // 'course_id' => 1003, // REMOVIDO
                'title' => 'Introducción a la Programación',
                'name' => 'Programming Fundamentals',
                'description' => 'Fundamentos de programación para principiantes',
                'level' => 'basic',
                'duration' => 80.00, 'sessions' => 30, 'selling_price' => 800.00, 'discount_price' => 600.00,
                'prerequisites' => 'Ninguno',
                'certificate_name' => true, 'certificate_issuer' => 'Academia Tech',
                'bestseller' => false, 'featured' => true, 'highest_rated' => false, 'status' => true,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ]
        ];

        foreach ($courses as $course) {
            DB::table('courses')->updateOrInsert(
                ['title' => $course['title']], // Usamos 'title' como clave única
                $course
            );
        }
    }

    private function run_departments(): void
    {
        $this->command->info('--- Ejecutando Departments (fusionado) ---');
        $allDepartments = [
            // From DepartmentsSeeder (plural)
            ['department_name' => 'Tecnología', 'description' => 'Departamento de desarrollo y soporte técnico'],
            ['department_name' => 'Académico', 'description' => 'Departamento de gestión académica'],
            ['department_name' => 'Finanzas', 'description' => 'Departamento de gestión financiera'],
            ['department_name' => 'Recursos Humanos', 'description' => 'Departamento de gestión de personal'],
            // From DepartmentSeeder (singular)
            ['department_name' => 'Desarrollo Web', 'description' => 'Departamento encargado del desarrollo y mantenimiento de aplicaciones web'],
            ['department_name' => 'Soporte Técnico', 'description' => 'Departamento de atención al cliente y soporte técnico'],
            ['department_name' => 'Administración', 'description' => 'Departamento administrativo y de gestión'],
            ['department_name' => 'Marketing', 'description' => 'Departamento de marketing y comunicaciones']
        ];

        foreach ($allDepartments as $dept) {
            DB::table('departments')->updateOrInsert(
                ['department_name' => $dept['department_name']],
                $dept
            );
        }
    }

    private function run_employees(): void
    {
        $this->command->info('--- Ejecutando Employees ---');
        $users = DB::table('users')->get()->filter(function ($u) {
            $roles = json_decode($u->role, true);
            return is_array($roles) && (in_array('employee', $roles) || in_array('technician', $roles));
        });

        $positions = DB::table('positions')->get();
        $departments = DB::table('departments')->get();

        if ($users->isEmpty() || $positions->isEmpty() || $departments->isEmpty()) {
            $this->command->warn('⚠️ No hay usuarios (employee/technician), posiciones o departamentos. Saltando Employees.');
            return;
        }

        $employees = [];
        foreach ($users as $user) {
            $position = $positions->random();
            $department = $departments->where('id', $position->department_id)->first();
            
            // Si no encontramos depto por ID de posición, tomamos uno al azar
            if (!$department) {
                $department = $departments->random();
            }

            $speciality = 'Soporte Técnico';
            if (str_contains(strtolower($user->full_name), 'desarrollador')) {
                $speciality = 'Desarrollo Web';
            } elseif (str_contains(strtolower($user->full_name), 'data')) {
                $speciality = 'Análisis de Datos';
            }

            $employees[] = [
                // 'employee_id' => ..., // REMOVIDO
                'hire_date' => Carbon::now()->subYears(rand(1, 3)),
                'position_id' => $position->id,
                'department_id' => $department->id,
                'user_id' => $user->id,
                'employment_status' => 'Active',
                'schedule' => 'Lunes a Viernes 9:00-18:00',
                'speciality' => $speciality,
                'salary' => rand(3500, 9500) + (rand(0, 99) / 100),
                'created_at' => Carbon::now(),
            ];
        }

        foreach ($employees as $employee) {
            DB::table('employees')->updateOrInsert(
                ['user_id' => $employee['user_id']], // Un usuario solo puede ser un empleado
                $employee
            );
        }
        $this->command->info('✅ Empleados creados/actualizados: ' . count($employees));
    }

    private function run_enrollment_details(): void
    {
        $this->command->info('--- Ejecutando Enrollment Details ---');
        $enrollments = DB::table('enrollments')->get();
        $subjects = DB::table('subjects')->get();
        $courseOfferings = DB::table('course_offerings')->get();
        
        if ($enrollments->isEmpty() || $subjects->isEmpty() || $courseOfferings->isEmpty()) {
            $this->command->warn('Faltan datos (enrollments, subjects, courseOfferings). Saltando EnrollmentDetails.');
            return;
        }

        $enrollmentDetails = [];

        foreach ($enrollments as $enrollment) {
            $enrollmentDetails[] = [
                'enrollment_id' => $enrollment->id,
                'subject_id' => $subjects->random()->id,
                'course_offering_id' => $courseOfferings->random()->id,
                'status' => 'active',
                'created_at' => Carbon::now(),
            ];
        }
        
        // Evitar duplicados
        $uniqueDetails = collect($enrollmentDetails)->unique(function ($item) {
            return $item['enrollment_id'] . '-' . $item['subject_id'] . '-' . $item['course_offering_id'];
        })->values()->all();

        DB::table('enrollment_details')->insert($uniqueDetails);
    }

    private function run_enrollments(): void
    {
        $this->command->info('--- Ejecutando Enrollments ---');
        $students = DB::table('students')->get();
        $academicPeriods = DB::table('academic_periods')->get();

        if ($students->isEmpty() || $academicPeriods->isEmpty()) {
            $this->command->warn('No hay Estudiantes o Periodos Académicos. Saltando Enrollments.');
            return;
        }

        $enrollments = [];
        foreach ($students as $student) {
            $enrollments[] = [
                // 'enrollment_id' => ..., // REMOVIDO
                'student_id' => $student->id,
                'academic_period_id' => $academicPeriods->random()->id,
                'enrollment_type' => 'new',
                'enrollment_date' => Carbon::now()->subDays(rand(1, 30)),
                'status' => 'active',
                'created_at' => Carbon::now(),
            ];
        }

        // Evitar duplicados (un estudiante por periodo)
        $uniqueEnrollments = collect($enrollments)->unique(function ($item) {
            return $item['student_id'] . '-' . $item['academic_period_id'];
        })->values()->all();
        
        DB::table('enrollments')->insert($uniqueEnrollments);
    }

    private function run_escalations(): void
    {
        $this->command->info('--- Ejecutando Escalations ---');
        $tickets = DB::table('tickets')->whereIn('status', ['en_progreso', 'resuelto'])->get();
        $employees = DB::table('employees')->get();

        if ($employees->count() < 2) {
            $this->command->warn('No hay suficientes empleados (mínimo 2) para crear escalaciones.');
            return;
        }
        if ($tickets->isEmpty()) {
            $this->command->warn('No hay tickets para escalar. Saltando Escalations.');
            return;
        }

        $escalations = [];
        $reasons = [
            'Requiere especialización técnica avanzada',
            'Cliente solicita cambio de técnico',
            'Tiempo de resolución excedido',
            'Problema complejo que requiere mayor experiencia'
        ];

        foreach ($tickets as $ticket) {
            $technicians = $employees->random(2);
            
            $escalations[] = [
                // 'escalation_id' => ..., // REMOVIDO
                'ticket_id' => $ticket->id,
                'technician_origin_id' => $technicians[0]->id,
                'technician_destiny_id' => $technicians[1]->id,
                'escalation_reason' => $reasons[rand(0, 3)],
                'observations' => 'Escalación realizada por complejidad del caso.',
                'escalation_date' => Carbon::now()->subDays(rand(1, 20)),
                'approved' => (bool)rand(0, 1),
            ];
        }
        
        // Evitar escalar el mismo ticket dos veces
        $uniqueEscalations = collect($escalations)->unique('ticket_id')->values()->all();
        DB::table('escalations')->insert($uniqueEscalations);
        $this->command->info('Escalaciones creadas: ' . count($uniqueEscalations));
    }

    private function run_evaluations(): void
    {
        $this->command->info('--- Ejecutando Evaluations ---');
        $groups = DB::table('groups')->get();
        $teachers = DB::table('users')->where('role', 'like', '%instructor%')->get();

        if ($groups->isEmpty() || $teachers->isEmpty()) {
            $this->command->warn('No hay grupos o instructores. Saltando Evaluations.');
            return;
        }

        $evaluations = [];
        $evaluationTypes = ['Exam', 'Quiz', 'Project', 'Assignment', 'Final'];

        foreach ($groups as $group) {
            foreach ($evaluationTypes as $type) {
                $teacher = $teachers->random();
                
                $evaluations[] = [
                    'group_id' => $group->id,
                    'title' => $type . ' - ' . $group->name,
                    'description' => 'Evaluación de tipo ' . $type . ' para el grupo ' . $group->name,
                    'evaluation_type' => $type,
                    'due_date' => Carbon::now()->addDays(rand(10, 30)),
                    'weight' => rand(5, 25) / 10,
                    'teacher_creator_id' => $teacher->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        
        // Evitar duplicados (mismo tipo de eval en mismo grupo)
        $uniqueEvals = collect($evaluations)->unique(function ($item) {
            return $item['group_id'] . '-' . $item['title'];
        })->values()->all();

        DB::table('evaluations')->insert($uniqueEvals);
        $this->command->info('Evaluaciones creadas: ' . count($uniqueEvals));
    }

    private function run_final_grades(): void
    {
        $this->command->info('--- Ejecutando Final Grades ---');
        $studentUsers = DB::table('users')->where('role', 'like', '%student%')->get();
        $groups = DB::table('groups')->get();

        if ($studentUsers->isEmpty() || $groups->isEmpty()) {
            $this->command->warn('No hay estudiantes o grupos. Saltando FinalGrades.');
            return;
        }

        $finalGrades = [];
        foreach ($studentUsers as $user) {
            $randomGroups = $groups->random(min(3, $groups->count()));
            
            foreach ($randomGroups as $group) {
                $finalGrade = rand(5000, 10000) / 100; // Nota entre 50.00 y 100.00
                $programStatus = $finalGrade >= 70 ? 'Passed' : 'Failed';

                $finalGrades[] = [
                    'user_id' => $user->id,
                    'group_id' => $group->id,
                    'final_grade' => $finalGrade,
                    'program_status' => $programStatus,
                    'calculation_date' => Carbon::now()->subDays(rand(1, 60)),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        $uniqueFinalGrades = collect($finalGrades)->unique(function ($item) {
            return $item['user_id'] . '-' . $item['group_id'];
        })->values()->all();

        if (!empty($uniqueFinalGrades)) {
            DB::table('final_grades')->insert($uniqueFinalGrades);
            $this->command->info('Calificaciones finales creadas: ' . count($uniqueFinalGrades));
        }
    }

    private function run_financial_transactions(): void
    {
        $this->command->info('--- Ejecutando Financial Transactions ---');
        $accounts = DB::table('accounts')->get();
        $invoices = DB::table('invoices')->where('status', 'Paid')->get();
        $payments = DB::table('payments')->get();

        if ($accounts->isEmpty()) {
            $this->command->warn('No hay cuentas. Saltando FinancialTransactions.');
            return;
        }

        $transactions = [];
        $accountIncome = $accounts->where('account_type', 'Income')->random();
        $accountExpense = $accounts->where('account_type', 'Expense')->random();

        foreach ($payments as $payment) {
            $invoice = $invoices->where('id', $payment->invoice_id)->first();
            
            if ($invoice) {
                $transactions[] = [
                    'account_id' => $accountIncome->id,
                    'amount' => $payment->amount,
                    'transaction_date' => $payment->payment_date,
                    'description' => 'Pago de matrícula - ' . $invoice->invoice_number,
                    'transaction_type' => 'income',
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        $transactions[] = [
            'account_id' => $accountExpense->id,
            'amount' => 500.00,
            'transaction_date' => Carbon::now()->subDays(15),
            'description' => 'Pago de servicios de nube',
            'transaction_type' => 'expense',
            'invoice_id' => null,
            'payment_id' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        
        // Evitar duplicados (basado en payment_id)
        $uniqueTransactions = collect($transactions)->unique(function ($item) {
            return $item['payment_id'] ?? $item['description'] . $item['transaction_date'];
        })->values()->all();

        DB::table('financial_transactions')->insert($uniqueTransactions);
    }

    private function run_grade_records(): void
    {
        $this->command->info('--- Ejecutando Grade Records ---');
        $studentUsers = DB::table('users')->where('role', 'like', '%student%')->get();
        $evaluations = DB::table('evaluations')->get();

        if ($studentUsers->isEmpty() || $evaluations->isEmpty()) {
            $this->command->warn('No hay estudiantes o evaluaciones. Saltando GradeRecords.');
            return;
        }

        $gradeRecords = [];

        foreach ($studentUsers as $user) {
            $randomEvaluations = $evaluations->random(min(8, $evaluations->count()));
            
            foreach ($randomEvaluations as $evaluation) {
                $grade = rand(5000, 10000) / 100;
                $gradeRecords[] = [
                    'evaluation_id' => $evaluation->id,
                    'user_id' => $user->id,
                    'obtained_grade' => $grade,
                    'feedback' => $this->generateFeedback($grade),
                    'record_date' => Carbon::now()->subDays(rand(1, 30)),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        $uniqueGradeRecords = collect($gradeRecords)->unique(function ($item) {
            return $item['evaluation_id'] . '-' . $item['user_id'];
        })->values()->all();

        if (!empty($uniqueGradeRecords)) {
            DB::table('grade_records')->insert($uniqueGradeRecords);
            $this->command->info('Registros de calificaciones creados: ' . count($uniqueGradeRecords));
        }
    }

    private function generateFeedback($grade): string
    {
        if ($grade >= 90) return 'Excelente trabajo, demuestra dominio completo del tema.';
        if ($grade >= 80) return 'Buen desempeño, comprende bien los conceptos.';
        if ($grade >= 70) return 'Desempeño satisfactorio, puede mejorar en algunos aspectos.';
        if ($grade >= 60) return 'Necesita reforzar algunos conceptos clave.';
        return 'Requiere estudio adicional y práctica.';
    }

    private function run_group_participants(): void
    {
        $this->command->info('--- Ejecutando Group Participants ---');
        $groups = DB::table('groups')->get();
        $users = DB::table('users')->get();
        $instructors = DB::table('instructors')->get();

        if ($groups->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay grupos o usuarios. Saltando GroupParticipants.');
            return;
        }

        $participants = [];

        // Agregar instructores como profesores
        foreach ($groups as $group) {
            if ($instructors->isNotEmpty()) {
                $instructor = $instructors->random();
                $instructorUser = $users->where('id', $instructor->user_id)->first();
                
                if ($instructorUser) {
                    $participants[] = [
                        'group_id' => $group->id,
                        'user_id' => $instructorUser->id,
                        'role' => 'teacher',
                        'enrollment_status' => 'active',
                        'assignment_date' => Carbon::now()->subDays(rand(1, 30)),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }

        // Agregar estudiantes
        $studentUsers = $users->filter(function ($user) {
            $roles = json_decode($user->role, true);
            return is_array($roles) && in_array('student', $roles);
        });

        if ($studentUsers->isNotEmpty()) {
            foreach ($groups as $group) {
                $randomStudents = $studentUsers->random(min(5, $studentUsers->count()));
                
                foreach ($randomStudents as $student) {
                    $participants[] = [
                        'group_id' => $group->id,
                        'user_id' => $student->id,
                        'role' => 'student',
                        'enrollment_status' => ['pending', 'approved', 'active', 'active'][rand(0, 3)],
                        'assignment_date' => Carbon::now()->subDays(rand(1, 60)),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }

        $uniqueParticipants = collect($participants)->unique(function ($item) {
            return $item['group_id'] . '-' . $item['user_id'];
        })->values()->all();

        if (!empty($uniqueParticipants)) {
            // CAMBIO: Usar insertOrIgnore() en lugar de insert()
            // Esto evitará el error de 'Duplicate entry' al
            // simplemente ignorar las filas que ya existen.
            $rowsInserted = DB::table('group_participants')->insertOrIgnore($uniqueParticipants);
            
            $this->command->info('Participantes de grupo procesados (potenciales): ' . count($uniqueParticipants));
            $this->command->info('Nuevos participantes insertados: ' . $rowsInserted); // Te dirá cuántos SÍ se insertaron
            $this->command->info(' - Teachers (procesados): ' . collect($uniqueParticipants)->where('role', 'teacher')->count());
            $this->command->info(' - Students (procesados): ' . collect($uniqueParticipants)->where('role', 'student')->count());
            
        } else {
            $this->command->warn('No se generaron participantes de grupo únicos.');
        }
    }

    private function run_groups(): void
    {
        $this->command->info('--- Ejecutando Groups ---');
        $courses = DB::table('courses')->get();
        
        if ($courses->isEmpty()) {
            $this->command->warn('No hay cursos. Saltando Groups.');
            return;
        }

        $groups = [];
        $groupCodes = ['G1', 'G2', 'G3', 'G4'];

        foreach ($courses as $course) {
            foreach ($groupCodes as $codeIndex => $code) {
                $groupName = $course->name . ' - Grupo ' . $code;
                $groups[] = [
                    'course_id' => $course->id,
                    'code' => $course->id . '-' . $code, // Business Key
                    'name' => $groupName,
                    'start_date' => Carbon::now()->addDays($codeIndex * 7),
                    'end_date' => Carbon::now()->addDays(120 + ($codeIndex * 7)),
                    'status' => ['draft', 'approved', 'open', 'in_progress'][rand(0, 3)],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        
        foreach ($groups as $group) {
            DB::table('groups')->updateOrInsert(
                ['name' => $group['name']], // Usar nombre como clave única
                $group
            );
        }
    }

    private function run_incidents(): void
    {
        $this->command->info('--- Ejecutando Incidents ---');
        $securityAlerts = DB::table('security_alerts')->whereIn('severity', ['high', 'critical'])->get();
        $employees = DB::table('employees')->get();
        
        if ($securityAlerts->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('No hay alertas de seguridad o empleados. Saltando Incidents.');
            return;
        }

        $incidents = [];
        foreach ($securityAlerts as $alert) {
            $incidents[] = [
                // 'id_incident' => ..., // REMOVIDO
                'alert_id' => $alert->id,
                'responsible_id' => $employees->random()->id,
                'title' => 'Incidente de Seguridad - ' . $alert->threat_type,
                'status' => ['open', 'in_progress', 'resolved'][rand(0, 2)],
                'report_date' => $alert->detection_date,
            ];
        }
        
        $uniqueIncidents = collect($incidents)->unique('alert_id')->values()->all();
        DB::table('incidents')->insert($uniqueIncidents);
    }

    private function run_instructors(): void
    {
        $this->command->info('--- Ejecutando Instructors ---');
        $instructorUsers = DB::table('users')->where('role', 'like', '%instructor%')->get();

        if ($instructorUsers->isEmpty()) {
            $this->command->warn('No hay usuarios instructores, creando...');
            $instructorData = [
                [
                    'first_name' => 'Carlos', 'last_name' => 'Rodríguez', 'full_name' => 'Carlos Rodríguez',
                    'dni' => '11223344', 'document' => '11223344', 'email' => 'carlos.rodriguez@email.com',
                    'phone_number' => '+51987654323', 'password' => bcrypt('password123'),
                    'gender' => 'male', 'country' => 'Perú', 'role' => json_encode(['instructor']),
                    'status' => 'active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                ],
                [
                    'first_name' => 'Laura', 'last_name' => 'Martínez', 'full_name' => 'Laura Martínez',
                    'dni' => '22334455', 'document' => '22334455', 'email' => 'laura.martinez@email.com',
                    'phone_number' => '+51987654325', 'password' => bcrypt('password123'),
                    'gender' => 'female', 'country' => 'Perú', 'role' => json_encode(['instructor']),
                    'status' => 'active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                ]
            ];

            foreach ($instructorData as $userData) {
                $user = DB::table('users')->where('email', $userData['email'])->first();
                if (!$user) {
                    $userId = DB::table('users')->insertGetId($userData);
                    $instructorUsers->push((object)array_merge($userData, ['id' => $userId]));
                }
            }
        }

        $instructors = [];
        foreach ($instructorUsers as $index => $user) {
            $instructors[] = [
                // 'instructor_id' => ..., // REMOVIDO
                'user_id' => $user->id,
                'bio' => 'Instructor con amplia experiencia en ' . ['desarrollo web', 'ciencia de datos', 'programación'][$index % 3],
                'expertise_area' => ['Full Stack Development', 'Data Science', 'Programming Fundamentals'][$index % 3],
                'status' => 'active',
                'created_at' => Carbon::now(),
            ];
        }

        foreach ($instructors as $instructor) {
            DB::table('instructors')->updateOrInsert(
                ['user_id' => $instructor['user_id']],
                $instructor
            );
        }
        $this->command->info('Instructores creados/actualizados: ' . count($instructors));
    }

    private function run_invoices(): void
    {
        $this->command->info('--- Ejecutando Invoices ---');
        $enrollments = DB::table('enrollments')->get();
        $revenueSources = DB::table('revenue_sources')->get();
        
        if ($enrollments->isEmpty() || $revenueSources->isEmpty()) {
            $this->command->warn('No hay Enrollments o Revenue Sources. Saltando Invoices.');
            return;
        }

        $invoices = [];
        $invoiceNumber = 10000;

        foreach ($enrollments as $index => $enrollment) {
            $totalAmount = rand(500, 2000) + (rand(0, 99) / 100);
            
            $invoices[] = [
                'enrollment_id' => $enrollment->id,
                'revenue_source_id' => $revenueSources->random()->id,
                'invoice_number' => 'INV-' . ($invoiceNumber + $index), // Business Key
                'issue_date' => Carbon::now()->subDays(rand(1, 60)),
                'total_amount' => $totalAmount,
                'status' => ['Pending', 'Paid', 'Cancelled'][rand(0, 2)],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        foreach ($invoices as $invoice) {
            DB::table('invoices')->updateOrInsert(
                ['invoice_number' => $invoice['invoice_number']],
                $invoice
            );
        }
    }

    private function run_payment_methods(): void
    {
        $this->command->info('--- Ejecutando Payment Methods ---');
        $methods = [
            ['name' => 'Tarjeta de Crédito', 'description' => 'Pago con tarjeta de crédito Visa/Mastercard'],
            ['name' => 'Transferencia Bancaria', 'description' => 'Transferencia interbancaria'],
            ['name' => 'PayPal', 'description' => 'Pago a través de PayPal'],
            ['name' => 'Yape', 'description' => 'Pago con Yape'],
        ];

        foreach ($methods as $method) {
            DB::table('payment_methods')->updateOrInsert(
                ['name' => $method['name']],
                array_merge($method, ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()])
            );
        }
    }

    private function run_payments(): void
    {
        $this->command->info('--- Ejecutando Payments ---');
        $invoices = DB::table('invoices')->where('status', 'Paid')->get();
        $paymentMethods = DB::table('payment_methods')->get();

        if ($invoices->isEmpty() || $paymentMethods->isEmpty()) {
            $this->command->warn('No hay Invoices pagadas o Métodos de Pago. Saltando Payments.');
            return;
        }

        $payments = [];
        foreach ($invoices as $invoice) {
            $payments[] = [
                'invoice_id' => $invoice->id,
                'payment_method_id' => $paymentMethods->random()->id,
                'amount' => $invoice->total_amount,
                'payment_date' => Carbon::now()->subDays(rand(1, 30)),
                'status' => 'Completed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        $uniquePayments = collect($payments)->unique('invoice_id')->values()->all();
        DB::table('payments')->insert($uniquePayments);
    }

    private function run_positions(): void
    {
        $this->command->info('--- Ejecutando Positions (fusionado) ---');
        $departments = DB::table('departments')->get()->keyBy('department_name');

        $getDeptId = function ($name) use ($departments) {
            return $departments->get($name)->id ?? null;
        };

        $positions = [
            // From PositionsSeeder (plural)
            ['name' => 'Desarrollador Senior', 'dept_name' => 'Tecnología'],
            ['name' => 'Soporte Técnico', 'dept_name' => 'Tecnología'],
            ['name' => 'Coordinador Académico', 'dept_name' => 'Académico'],
            ['name' => 'Analista Financiero', 'dept_name' => 'Finanzas'],
            // From PositionSeeder (singular)
            ['name' => 'Desarrollador Web Senior', 'dept_name' => 'Desarrollo Web'],
            ['name' => 'Desarrollador Web Junior', 'dept_name' => 'Desarrollo Web'],
            ['name' => 'Diseñador UX/UI', 'dept_name' => 'Desarrollo Web'],
            ['name' => 'Especialista en Soporte Técnico', 'dept_name' => 'Soporte Técnico'],
            ['name' => 'Coordinador de Soporte', 'dept_name' => 'Soporte Técnico'],
            ['name' => 'Gerente Administrativo', 'dept_name' => 'Administración'],
            ['name' => 'Asistente Administrativo', 'dept_name' => 'Administración'],
            ['name' => 'Especialista en Marketing Digital', 'dept_name' => 'Marketing']
        ];
        
        foreach ($positions as $pos) {
            $deptId = $getDeptId($pos['dept_name']);
            if (!is_null($deptId)) {
                DB::table('positions')->updateOrInsert(
                    ['position_name' => $pos['name']],
                    ['position_name' => $pos['name'], 'department_id' => $deptId]
                );
            } else {
                $this->command->warn("No se encontró el departamento '{$pos['dept_name']}' para la posición: " . $pos['name']);
            }
        }
    }

    private function run_revenue_sources(): void
    {
        $this->command->info('--- Ejecutando Revenue Sources ---');
        $sources = [
            ['name' => 'Matrículas', 'description' => 'Ingresos por concepto de matrículas estudiantiles'],
            ['name' => 'Mensualidades', 'description' => 'Ingresos por mensualidades de cursos'],
            ['name' => 'Certificaciones', 'description' => 'Ingresos por emisión de certificados'],
            ['name' => 'Cursos Corporativos', 'description' => 'Ingresos por cursos para empresas'],
        ];

        foreach ($sources as $source) {
            DB::table('revenue_sources')->updateOrInsert(
                ['name' => $source['name']],
                array_merge($source, ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()])
            );
        }
    }

    private function run_security_alerts(): void
    {
        $this->command->info('--- Ejecutando Security Alerts ---');
        $blockedIPs = DB::table('blocked_ips')->get();
        
        if ($blockedIPs->isEmpty()) {
            $this->command->warn('No hay IPs bloqueadas. Saltando SecurityAlerts.');
            return;
        }

        $securityAlerts = [];
        $threatTypes = ['Brute Force Attack', 'SQL Injection Attempt', 'XSS Attack', 'DDoS Attempt', 'Unauthorized Access'];
        $severities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['new', 'investigating', 'resolved', 'false_positive'];

        foreach ($blockedIPs as $blockedIP) {
            $securityAlerts[] = [
                // 'id_security_alert' => ..., // REMOVIDO
                'threat_type' => $threatTypes[rand(0, 4)],
                'severity' => $severities[rand(0, 3)],
                'status' => $statuses[rand(0, 3)],
                'blocked_ip_id' => $blockedIP->id,
                'detection_date' => $blockedIP->block_date,
            ];
        }
        
        $uniqueAlerts = collect($securityAlerts)->unique('blocked_ip_id')->values()->all();
        DB::table('security_alerts')->insert($uniqueAlerts);
    }

    private function run_security_logs(): void
    {
        $this->command->info('--- Ejecutando Security Logs ---');
        $users = DB::table('users')->get();
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios. Saltando SecurityLogs.');
            return;
        }

        $securityLogs = [];
        $eventTypes = ['login_success', 'login_failed', 'password_change', 'profile_update', 'access_denied', 'session_timeout'];

        foreach ($users as $user) {
            for ($i = 0; $i < rand(1, 5); $i++) {
                $securityLogs[] = [
                    // 'id_security_log' => ..., // REMOVIDO
                    'user_id' => $user->id,
                    'event_type' => $eventTypes[rand(0, 5)],
                    'description' => 'Evento de seguridad registrado para el usuario',
                    'source_ip' => '192.168.1.' . rand(1, 255),
                    'event_date' => Carbon::now()->subDays(rand(1, 90))->subHours(rand(1, 24)),
                ];
            }
        }

        DB::table('security_logs')->insert($securityLogs);
    }

    private function run_students(): void
    {
        $this->command->info('--- Ejecutando Students ---');
        $users = DB::table('users')->where('role', 'like', '%student%')->get();
        $companies = DB::table('companies')->get();
        
        if ($users->isEmpty() || $companies->isEmpty()) {
            $this->command->warn('No hay usuarios Estudiantes o Compañías. Saltando Students.');
            return;
        }

        $students = [];
        foreach ($users as $user) {
            $students[] = [
                // 'student_id' => ..., // REMOVIDO
                'user_id' => $user->id,
                'company_id' => $companies->random()->id,
                'document_number' => $user->dni,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone_number,
                'status' => 'active',
                'created_at' => Carbon::now(),
            ];
        }

        foreach ($students as $student) {
            DB::table('students')->updateOrInsert(
                ['user_id' => $student['user_id']],
                $student
            );
        }
    }

    private function run_subjects(): void
    {
        $this->command->info('--- Ejecutando Subjects ---');
        $subjects = [
            ['subject_code' => 'PROG101', 'subject_name' => 'Programación Básica', 'credits' => 4, 'status' => 'active'],
            ['subject_code' => 'WEB202', 'subject_name' => 'Desarrollo Web', 'credits' => 5, 'status' => 'active'],
            ['subject_code' => 'DATA301', 'subject_name' => 'Ciencia de Datos', 'credits' => 6, 'status' => 'active'],
            ['subject_code' => 'DB402', 'subject_name' => 'Bases de Datos', 'credits' => 4, 'status' => 'active'],
        ];
        
        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['subject_code' => $subject['subject_code']],
                $subject
            );
        }
    }

    private function run_tickets(): void
    {
        $this->command->info('--- Ejecutando Tickets ---');
        $users = DB::table('users')->get();
        $employees = DB::table('employees')->get();
        
        if ($users->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('No hay Usuarios o Empleados. Saltando Tickets.');
            return;
        }

        $tickets = [];
        $priorities = ['baja', 'media', 'alta', 'critica'];
        $statuses = ['abierto', 'en_progreso', 'resuelto', 'cerrado'];
        $categories = ['tecnico', 'academico', 'financiero', 'general'];

        foreach ($users as $user) {
            $tickets[] = [
                // 'ticket_id' => ..., // REMOVIDO
                'assigned_technician' => $employees->random()->id,
                'user_id' => $user->id,
                'title' => 'Problema con ' . ['acceso al aula virtual', 'subida de archivos', 'conexión VPN', 'certificado'][rand(0, 3)],
                'description' => 'Necesito ayuda para resolver un problema técnico.',
                'priority' => $priorities[rand(0, 3)],
                'status' => $statuses[rand(0, 3)],
                'creation_date' => Carbon::now()->subDays(rand(1, 60)),
                'assignment_date' => Carbon::now()->subDays(rand(1, 30)),
                'resolution_date' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 15)) : null,
                'close_date' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 10)) : null,
                'category' => $categories[rand(0, 3)],
                'notes' => 'Cliente contactado, se requiere seguimiento.',
            ];
        }

        DB::table('tickets')->insert($tickets);
    }

    private function run_users(): void
    {
        $this->command->info('--- Ejecutando Users (fusionado) ---');
        
        // De DefaultUsersSeeder
        $defaultUsers = [
            [
                'first_name' => 'Admin', 'last_name' => 'Developer', 'full_name' => 'Admin Developer',
                'email' => 'developer@incadev.com', 'password' => Hash::make('password'),
                'role' => json_encode(['admin']), 'status' => 'active', 'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Content', 'last_name' => 'Manager', 'full_name' => 'Content Manager',
                'email' => 'content@incadev.com', 'password' => Hash::make('password'),
                'role' => json_encode(['content_manager']), 'status' => 'active', 'email_verified_at' => now(),
            ]
        ];

        foreach ($defaultUsers as $user) {
            if (!DB::table('users')->where('email', $user['email'])->exists()) {
                DB::table('users')->insert(array_merge($user, ['created_at' => now(), 'updated_at' => now()]));
                $this->command->info('Usuario ' . $user['email'] . ' creado.');
            }
        }

        // De UsersSeeder
        $users = [
            [
                'first_name' => 'admin', 'last_name' => 'Pérez', 'full_name' => 'Juan Pérez',
                'dni' => '12345678', 'document' => '12345678', 'email' => 'admin@email.com',
                'phone_number' => '+51987654321', 'password' => Hash::make('password123'),
                'gender' => 'male', 'country' => 'Perú', 'role' => json_encode(['admin']), 'status' => 'active',
            ],
            [
                'first_name' => 'María', 'last_name' => 'García', 'full_name' => 'María García',
                'dni' => '87654321', 'document' => '87654321', 'email' => 'maria.garcia@email.com',
                'phone_number' => '+51987654322', 'password' => Hash::make('password123'),
                'gender' => 'female', 'country' => 'Perú', 'role' => json_encode(['student']), 'status' => 'active',
            ],
            [
                'first_name' => 'Carlos', 'last_name' => 'López', 'full_name' => 'Carlos López',
                'dni' => '23456789', 'document' => '23456789', 'email' => 'carlos.lopez@email.com',
                'phone_number' => '+51987654328', 'password' => Hash::make('password123'),
                'gender' => 'male', 'country' => 'Perú', 'role' => json_encode(['student']), 'status' => 'active',
            ],
            [
                'first_name' => 'Ana', 'last_name' => 'Martínez', 'full_name' => 'Ana Martínez',
                'dni' => '34567890', 'document' => '34567890', 'email' => 'ana.martinez@email.com',
                'phone_number' => '+51987654329', 'password' => Hash::make('password123'),
                'gender' => 'female', 'country' => 'Perú', 'role' => json_encode(['student']), 'status' => 'active',
            ],
            [
                'first_name' => 'Luis', 'last_name' => 'González', 'full_name' => 'Luis González',
                'dni' => '45678901', 'document' => '45678901', 'email' => 'luis.gonzalez@email.com',
                'phone_number' => '+51987654330', 'password' => Hash::make('password123'),
                'gender' => 'male', 'country' => 'Perú', 'role' => json_encode(['student']), 'status' => 'active',
            ],
            [
                'first_name' => 'Roberto', 'last_name' => 'Rodríguez', 'full_name' => 'Roberto Rodríguez',
                'dni' => '11223344', 'document' => '11223344', 'email' => 'roberto.rodriguez@email.com',
                'phone_number' => '+51987654323', 'password' => Hash::make('password123'),
                'gender' => 'male', 'country' => 'Perú', 'role' => json_encode(['instructor']), 'status' => 'active',
            ],
            [
                'first_name' => 'Laura', 'last_name' => 'Silva', 'full_name' => 'Laura Silva',
                'dni' => '22334455', 'document' => '22334455', 'email' => 'laura.silva@email.com',
                'phone_number' => '+51987654325', 'password' => Hash::make('password123'),
                'gender' => 'female', 'country' => 'Perú', 'role' => json_encode(['instructor']), 'status' => 'active',
            ],
            [
                'first_name' => 'Ana', 'last_name' => 'López', 'full_name' => 'Ana López',
                'dni' => '44332211', 'document' => '44332211', 'email' => 'ana.lopez@email.com',
                'phone_number' => '+51987654324', 'password' => Hash::make('password123'),
                'gender' => 'female', 'country' => 'Perú', 'role' => json_encode(['employee', 'technician']), 'status' => 'active',
            ],
            [
                'first_name' => 'Pedro', 'last_name' => 'Gómez', 'full_name' => 'Pedro Gómez',
                'dni' => '55443322', 'document' => '55443322', 'email' => 'pedro.gomez@email.com',
                'phone_number' => '+51987654326', 'password' => Hash::make('password123'),
                'gender' => 'male', 'country' => 'Perú', 'role' => json_encode(['employee', 'technician']), 'status' => 'active',
            ],
            [
                'first_name' => 'Lucía', 'last_name' => 'Ramírez', 'full_name' => 'Lucía Ramírez',
                'dni' => '66554433', 'document' => '66554433', 'email' => 'lucia.ramirez@email.com',
                'phone_number' => '+51987654327', 'password' => Hash::make('password123'),
                'gender' => 'female', 'country' => 'Perú', 'role' => json_encode(['employee', 'technician']), 'status' => 'active',
            ],
            [
                'first_name' => 'Desarrollador', 'last_name' => 'Web', 'full_name' => 'Desarrollador Web',
                'dni' => '66554436', 'document' => '66554436', 'email' => 'developer.web@email.com',
                'phone_number' => '+51987654328', 'password' => Hash::make('devweb123'),
                'gender' => 'male', 'country' => 'Perú', 'role' => json_encode(['web','employee', 'technician']), 'status' => 'active',
            ],
            [
                'first_name' => 'Data', 'last_name' => 'Analyst', 'full_name' => 'Data Analyst',
                'dni' => '66554430', 'document' => '66554430', 'email' => 'data.analyst@email.com',
                'phone_number' => '+51987654320', 'password' => Hash::make('data123'),
                'gender' => 'male', 'country' => 'Perú', 'role' => json_encode(['data', 'employee', 'technician']), 'status' => 'active',
            ]
        ];
        
        $count = 0;
        foreach ($users as $user) {
            if (!DB::table('users')->where('email', $user['email'])->exists()) {
                DB::table('users')->insert(array_merge($user, ['created_at' => now(), 'updated_at' => now()]));
                $count++;
            }
        }
        $this->command->info('Usuarios adicionales creados: ' . $count);
    }
}