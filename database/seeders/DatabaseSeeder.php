<?php

namespace Database\Seeders;

use App\Models\AcademicSetting;
use App\Models\User;
use App\Models\TeacherProfile;
use App\Models\Program;
use App\Models\Course;
use App\Models\ProgramCourse;
use App\Models\CoursePreviousRequirement;
use App\Models\Group;
use App\Models\Classes;
use App\Models\ClassMaterial;
use App\Models\GroupParticipant;
use App\Models\Attendance;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use App\Models\FinalGrade;
use App\Models\Credential;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\EmploymentProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // -----------------------------------------------------------------
        // 0. CONFIGURACIÓN ACADÉMICA
        // -----------------------------------------------------------------
        $this->command->info('Creando configuración académica...');
        $academicSetting = AcademicSetting::create([
            'base_grade' => 20.00,
            'min_passing_grade' => 11.00,
        ]);
        $minPassingGrade = $academicSetting->min_passing_grade;
        $baseGrade = $academicSetting->base_grade;


        // -----------------------------------------------------------------
        // 1. USUARIOS: ADMIN
        // -----------------------------------------------------------------
        $this->command->info('Creando usuario Administrador...');
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'full_name' => 'Admin Principal',
            'dni' => '11111111',
            'document' => 'DOC111111',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999111111',
            'role' => ['admin', 'student'],
            'gender' => 'male',
            'country' => 'Peru',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // -----------------------------------------------------------------
        // 2. USUARIOS: PROFESORES (Y ALUMNOS)
        // -----------------------------------------------------------------
        $this->command->info('Creando usuarios Profesores...');
        $teacherMirko = User::create([
            'first_name' => 'MIRKO MARTIN',
            'last_name' => 'MANRIQUE RONCEROS',
            'full_name' => 'MIRKO MARTIN MANRIQUE RONCEROS',
            'dni' => '22222222',
            'document' => 'DOC2222222',
            'email' => 'mirko@gmail.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999222222',
            'role' => ['teacher', 'student'],
            'gender' => 'male',
            'country' => 'Peru',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        TeacherProfile::create([
            'user_id' => $teacherMirko->id,
            'professional_title' => 'Ingeniero de Software',
            'specialty' => 'Desarrollo Web Full Stack',
            'experience_years' => 10,
            'biography' => 'Especialista en desarrollo web con más de 10 años de experiencia.'
        ]);

        $teacherCarlosG = User::create([
            'first_name' => 'CARLOS ALFREDO',
            'last_name' => 'GIL NARVAEZ',
            'full_name' => 'CARLOS ALFREDO GIL NARVAEZ',
            'dni' => '33333333',
            'document' => 'DOC3333333',
            'email' => 'carlosgil@gmail.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999333333',
            'role' => ['teacher', 'student'],
            'gender' => 'male',
            'country' => 'Peru',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        TeacherProfile::create([
            'user_id' => $teacherCarlosG->id,
            'professional_title' => 'Master en IA',
            'specialty' => 'Machine Learning',
            'experience_years' => 8,
            'biography' => 'Experto en IA y Machine Learning.'
        ]);

        $teacherGuillermo = User::create([
            'first_name' => 'GUILLERMO EDWARD',
            'last_name' => 'GIL ALBARRAN',
            'full_name' => 'GUILLERMO EDWARD GIL ALBARRAN',
            'dni' => '44444444',
            'document' => 'DOC4444444',
            'email' => 'guillermo@gmail.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999444444',
            'role' => ['teacher', 'student'],
            'gender' => 'male',
            'country' => 'Peru',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        TeacherProfile::create([
            'user_id' => $teacherGuillermo->id,
            'professional_title' => 'Ingeniero en Ciberseguridad',
            'specialty' => 'Ethical Hacking',
            'experience_years' => 6,
            'biography' => 'Especialista en ciberseguridad y hacking ético.'
        ]);

        $teacherHugo = User::create([
            'first_name' => 'HUGO ESTEBAN',
            'last_name' => 'CASELLI GISMONDI',
            'full_name' => 'HUGO ESTEBAN CASELLI GISMONDI',
            'dni' => '55555555',
            'document' => 'DOC5555555',
            'email' => 'hugo.caselli@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999555555',
            'role' => ['teacher', 'student'],
            'gender' => 'male',
            'country' => 'Peru',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        TeacherProfile::create([
            'user_id' => $teacherHugo->id,
            'professional_title' => 'Diseñador UX/UI Senior',
            'specialty' => 'Diseño de Producto Digital',
            'experience_years' => 12,
            'biography' => 'Enfocado en la creación de experiencias de usuario memorables.'
        ]);

        $teacherJohan = User::create([
            'first_name' => 'JOHAN MAX',
            'last_name' => 'LOPEZ HEREDIA',
            'full_name' => 'JOHAN MAX LOPEZ HEREDIA',
            'dni' => '66666666',
            'document' => 'DOC6666666',
            'email' => 'johan.lopez@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999666666',
            'role' => ['teacher', 'student'],
            'gender' => 'male',
            'country' => 'Peru',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        TeacherProfile::create([
            'user_id' => $teacherJohan->id,
            'professional_title' => 'Arquitecto Cloud',
            'specialty' => 'AWS y Azure',
            'experience_years' => 7,
            'biography' => 'Experto en infraestructura cloud y soluciones escalables.'
        ]);

        $teacherJavier = User::create([
            'first_name' => 'JAVIER LUCHO',
            'last_name' => 'UTRILLA CAMONES',
            'full_name' => 'JAVIER LUCHO UTRILLA CAMONES',
            'dni' => '77777777',
            'document' => 'DOC7777777',
            'email' => 'javier.utrilla@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999777777',
            'role' => ['teacher', 'student'],
            'gender' => 'male',
            'country' => 'Peru',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        TeacherProfile::create([
            'user_id' => $teacherJavier->id,
            'professional_title' => 'Data Scientist',
            'specialty' => 'Big Data y Analytics',
            'experience_years' => 9,
            'biography' => 'Especialista en análisis de datos y modelos predictivos.'
        ]);

        $teacherLizbeth = User::create([
            'first_name' => 'LIZBETH DORA',
            'last_name' => 'BRIONES PEREYRA',
            'full_name' => 'LIZBETH DORA BRIONES PEREYRA',
            'dni' => '88888888',
            'document' => 'DOC8888888',
            'email' => 'lizbeth.briones@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999888888',
            'role' => ['teacher', 'student'],
            'gender' => 'female',
            'country' => 'Peru',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        TeacherProfile::create([
            'user_id' => $teacherLizbeth->id,
            'professional_title' => 'Scrum Master Certificada',
            'specialty' => 'Metodologías Ágiles',
            'experience_years' => 8,
            'biography' => 'Líder de equipos ágiles, facilitando la entrega de valor.'
        ]);

        $teacherCarlosV = User::create([
            'first_name' => 'CARLOS EUGENIO',
            'last_name' => 'VEGA MORENO',
            'full_name' => 'CARLOS EUGENIO VEGA MORENO',
            'dni' => '99999999',
            'document' => 'DOC9999999',
            'email' => 'carlos.vega@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999999999',
            'role' => ['teacher', 'student'],
            'gender' => 'male',
            'country' => 'Peru',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        TeacherProfile::create([
            'user_id' => $teacherCarlosV->id,
            'professional_title' => 'Ingeniero DevOps',
            'specialty' => 'CI/CD y Kubernetes',
            'experience_years' => 9,
            'biography' => 'Experto en automatización de despliegues.'
        ]);


        // -----------------------------------------------------------------
        // 3. USUARIOS: ESTUDIANTES
        // -----------------------------------------------------------------
        $this->command->info('Creando usuarios Estudiantes...');
        $allStudents = collect([
            User::create(['first_name' => 'JUAN JOSE', 'last_name' => 'AGUILAR VILLAFANA', 'full_name' => 'JUAN JOSE AGUILAR VILLAFANA', 'dni' => '10101010', 'email' => 'juan.aguilar@example.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'ANDERZON JUNIOR', 'last_name' => 'LUJAN TRUJILLO', 'full_name' => 'ANDERZON JUNIOR LUJAN TRUJILLO', 'dni' => '20202020', 'email' => 'anderzon.lujan@example.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'ANDERZON DICA', 'last_name' => 'PORTAL IBAÑEZ', 'full_name' => 'ANDERZON DICA PORTAL IBAÑEZ', 'dni' => '30303030', 'email' => 'anderzon.portal@example.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'NILTON', 'last_name' => 'RAMOS ENCARNACION', 'full_name' => 'NILTON RAMOS ENCARNACION', 'dni' => '75412099', 'email' => 'niltonencarnacion17@gmail.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'JOSE MANUEL', 'last_name' => 'VASQUEZ RAMOS', 'full_name' => 'JOSE MANUEL VASQUEZ RAMOS', 'dni' => '40404040', 'email' => 'jose.vasquez@example.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'CESAR ALEXANDER', 'last_name' => 'QUEZADA CHORRES', 'full_name' => 'CESAR ALEXANDER QUEZADA CHORRES', 'dni' => '50505050', 'email' => 'cesar.quezada@example.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'RANDALL ANTONIO', 'last_name' => 'LEYTON ROSALES', 'full_name' => 'RANDALL ANTONIO LEYTON ROSALES', 'dni' => '60606060', 'email' => 'randall.leyton@example.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'JOSE ANTONIO', 'last_name' => 'TORRES MILLA', 'full_name' => 'JOSE ANTONIO TORRES MILLA', 'dni' => '70707070', 'email' => 'jose.torres@example.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'RONALD FABRIZIO', 'last_name' => 'HURTADO RAMOS', 'full_name' => 'RONALD FABRIZIO HURTADO RAMOS', 'dni' => '80808080', 'email' => 'ronald.hurtado@example.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'JHOAN ANTONI', 'last_name' => 'CRUZ CASTILLO', 'full_name' => 'JHOAN ANTONI CRUZ CASTILLO', 'dni' => '90909090', 'email' => 'jhoan.cruz@example.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'LUIS ANGEL', 'last_name' => 'GONZALES BERROCAL', 'full_name' => 'LUIS ANGEL GONZALES BERROCAL', 'dni' => '12121212', 'document' => 'DOC-AUDITOR', 'email' => 'auditor@example.com', 'password' => Hash::make('12345678'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'ANGEL WILFREDO', 'last_name' => 'BUSTAMANTE PALACIOS', 'full_name' => 'ANGEL WILFREDO BUSTAMANTE PALACIOS', 'dni' => '13131313', 'email' => 'angel.bustamante@example.com', 'password' => Hash::make('password'), 'role' => ['student'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'EDWIN FIDENCIO', 'last_name' => 'OSORIO JUAQUIN', 'full_name' => 'EDWIN FIDENCIO OSORIO JUAQUIN', 'dni' => '99999999', 'document' => 'DOC-ADMIN', 'email' => 'edwin@example.com', 'password' => Hash::make('12345678'), 'role' => ['student', 'admin'], 'status' => 'active', 'email_verified_at' => now()]),
            User::create(['first_name' => 'RODRIGO EMILIO', 'last_name' => 'TREJO OBREGON', 'full_name' => 'RODRIGO EMILIO TREJO OBREGON', 'dni' => '77777777', 'document' => 'DOC-AUDITOR', 'email' => 'auditor2@example.com', 'password' => Hash::make('12345678'), 'role' => ['student', 'auditor'], 'status' => 'active', 'email_verified_at' => now()]),
        ]);

        // Listas de estudiantes para grupos
        $studentsHalf1 = $allStudents->slice(0, 6);
        $studentsHalf2 = $allStudents->slice(6, 6);
        $studentsQuarter1 = $allStudents->slice(0, 3);
        $studentsQuarter2 = $allStudents->slice(3, 3);
        $studentsLastHalf = $allStudents->slice(6, 6); // La "otra mitad que faltaba"


        // -----------------------------------------------------------------
        // 4. PROGRAMAS, CURSOS Y PRERREQUISITOS
        // -----------------------------------------------------------------
        $this->command->info('Creando Programas y Cursos...');
        $courseMap = []; // Para guardar cursos por nombre y buscar IDs
        $courseNumericId = 100; // <--- CONTADOR NUMÉRICO INICIAL

        // --- Programa 1: Desarrollo de Software y Arquitectura ---
        $programDev = Program::create([
            'name' => 'Desarrollo de Software y Arquitectura',
            'description' => 'Programa sobre diseño y arquitectura de software moderno.',
            'duration_weeks' => 24,
            'max_capacity' => 30,
            'start_date' => '2025-01-15',
            'end_date' => '2025-07-15',
            'price' => 3000.00,
            'modality' => 'hybrid',
            'status' => 'active',
        ]);
        $coursesDev = [
            'PATRONES DE DISEÑO DE SOFTWARE',
            'BASE DE DATOS',
            'APLICACIONES DISTRIBUIDAS',
            'ARQUITECTURA DE SOFTWARE EMPRESARIAL',
            'INGENIERIA DE SOFTWARE',
            'ARQUITECTURA ORIENTADA A SERVICIOS Y MICROSERVICIOS',
            'APLICACIONES MOVILES',
            'GESTION DE LA ARQUITECTURA EMPRESARIAL'
        ];
        foreach ($coursesDev as $courseName) {
            $course = Course::create([
                'course_id' => $courseNumericId++,
                'title' => $courseName,
                'name' => $courseName,
                'description' => "Curso de $courseName",
                'level' => 'intermediate',
                'duration' => 40,
                'sessions' => 10,
                'selling_price' => 500,
                'status' => true
            ]);
            ProgramCourse::create(['program_id' => $programDev->id, 'course_id' => $course->id, 'mandatory' => true]);
            $courseMap[$courseName] = $course;
        }

        // --- Programa 2: Infraestructura Tecnológica y Redes ---
        $programInfra = Program::create([
            'name' => 'Infraestructura Tecnológica y Redes',
            'description' => 'Programa enfocado en redes, seguridad y cloud.',
            'duration_weeks' => 20,
            'max_capacity' => 25,
            'start_date' => '2025-02-01',
            'end_date' => '2025-06-30',
            'price' => 2800.00,
            'modality' => 'virtual',
            'status' => 'active',
        ]);
        $coursesInfra = [
            'SISTEMAS OPERATIVOS',
            'COMUNICACION DE DATOS',
            'REDES DE COMPUTADORAS',
            'SEGURIDAD INFORMATICA',
            'APLICACIONES EN LA NUBE',
            'ADMINISTRACION DE CENTROS DE DATOS'
        ];
        foreach ($coursesInfra as $courseName) {
            $course = Course::create([
                'course_id' => $courseNumericId++,
                'title' => $courseName,
                'name' => $courseName,
                'description' => "Curso de $courseName",
                'level' => 'intermediate',
                'duration' => 40,
                'sessions' => 10,
                'selling_price' => 500,
                'status' => true
            ]);
            ProgramCourse::create(['program_id' => $programInfra->id, 'course_id' => $course->id, 'mandatory' => true]);
            $courseMap[$courseName] = $course;
        }

        // --- Programa 3: Gestión y Sistemas de Información ---
        $programGestion = Program::create([
            'name' => 'Gestión y Sistemas de Información',
            'description' => 'Programa sobre gestión de TI y procesos de negocio.',
            'duration_weeks' => 16,
            'max_capacity' => 20,
            'start_date' => '2025-03-01',
            'end_date' => '2025-06-30',
            'price' => 2500.00,
            'modality' => 'virtual',
            'status' => 'active',
        ]);
        $coursesGestion = [
            'GESTION EMPRESARIAL BASADA EN T.I.',
            'SISTEMAS DE INFORMACION',
            'ADMINISTRACION DE PROCESOS DE NEGOCIO',
            'GESTION DE TECNOLOGIAS DE INFORMACION',
            'GESTION DEL GOBIERNO DE T.I.',
            'MODELO DE SISTEMA VIABLE',
            'AUDITORIA DE SISTEMAS'
        ];
        foreach ($coursesGestion as $courseName) {
            $course = Course::create([
                'course_id' => $courseNumericId++,
                'title' => $courseName,
                'name' => $courseName,
                'description' => "Curso de $courseName",
                'level' => 'basic',
                'duration' => 40,
                'sessions' => 10,
                'selling_price' => 500,
                'status' => true
            ]);
            ProgramCourse::create(['program_id' => $programGestion->id, 'course_id' => $course->id, 'mandatory' => true]);
            $courseMap[$courseName] = $course;
        }

        // --- Definición de Prerrequisitos ---
        $this->command->info('Asignando prerrequisitos...');
        $requirements = [
            'APLICACIONES DISTRIBUIDAS' => ['PATRONES DE DISEÑO DE SOFTWARE', 'BASE DE DATOS'],
            'ARQUITECTURA DE SOFTWARE EMPRESARIAL' => ['PATRONES DE DISEÑO DE SOFTWARE'],
            'INGENIERIA DE SOFTWARE' => ['SISTEMAS DE INFORMACION'],
            'ARQUITECTURA ORIENTADA A SERVICIOS Y MICROSERVICIOS' => ['APLICACIONES DISTRIBUIDAS'],
            'APLICACIONES MOVILES' => ['ARQUITECTURA ORIENTADA A SERVICIOS Y MICROSERVICIOS', 'REDES DE COMPUTADORAS'],
            'GESTION DE LA ARQUITECTURA EMPRESARIAL' => ['INGENIERIA DE SOFTWARE'],
            'COMUNICACION DE DATOS' => ['SISTEMAS OPERATIVOS'],
            'REDES DE COMPUTADORAS' => ['COMUNICACION DE DATOS'],
            'SEGURIDAD INFORMATICA' => ['REDES DE COMPUTADORAS'],
            'APLICACIONES EN LA NUBE' => ['APLICACIONES MOVILES', 'SEGURIDAD INFORMATICA'],
            'ADMINISTRACION DE CENTROS DE DATOS' => ['SEGURIDAD INFORMATICA'],
            'GESTION DE TECNOLOGIAS DE INFORMACION' => ['GESTION EMPRESARIAL BASADA EN T.I.'],
            'GESTION DEL GOBIERNO DE T.I.' => ['GESTION DE TECNOLOGIAS DE INFORMACION'],
            'MODELO DE SISTEMA VIABLE' => ['GESTION DEL GOBIERNO DE T.I.'],
            'AUDITORIA DE SISTEMAS' => ['GESTION DEL GOBIERNO DE T.I.'],
        ];

        foreach ($requirements as $courseName => $reqNames) {
            if (isset($courseMap[$courseName])) {
                $course = $courseMap[$courseName];
                foreach ($reqNames as $reqName) {
                    if (isset($courseMap[$reqName])) {
                        CoursePreviousRequirement::create([
                            'course_id' => $course->id,
                            'previous_course_id' => $courseMap[$reqName]->id,
                        ]);
                    }
                }
            }
        }

        // -----------------------------------------------------------------
        // 5. GRUPOS "COMPLETED"
        // -----------------------------------------------------------------
        $this->command->info('Creando grupos COMPLETADOS (DB 2023 A y B)...');

        // --- GRUPO: BASE DE DATOS - GRUPO 2023 A ---
        $group_DB_A = Group::create([
            'course_id' => $courseMap['BASE DE DATOS']->id,
            'code' => 'DB-2023-A',
            'name' => 'Grupo 2023 A',
            'start_date' => Carbon::now()->subMonths(6),
            'end_date' => Carbon::now()->subMonths(3),
            'status' => 'completed',
        ]);

        // Profesores y Alumnos
        $participants_DB_A_teachers = [
            GroupParticipant::create(['group_id' => $group_DB_A->id, 'user_id' => $teacherHugo->id, 'role' => 'teacher']),
            GroupParticipant::create(['group_id' => $group_DB_A->id, 'user_id' => $teacherJohan->id, 'role' => 'teacher']),
        ];
        $participants_DB_A_students = [];
        foreach ($studentsHalf1 as $student) {
            $participants_DB_A_students[] = GroupParticipant::create(['group_id' => $group_DB_A->id, 'user_id' => $student->id, 'role' => 'student']);
        }

        // Clases (3)
        $classes_DB_A = [
            Classes::create(['group_id' => $group_DB_A->id, 'class_name' => 'Intro a SQL', 'class_date' => Carbon::now()->subMonths(5), 'start_time' => '09:00', 'end_time' => '11:00', 'class_status' => 'FINISHED']),
            Classes::create(['group_id' => $group_DB_A->id, 'class_name' => 'Modelado de Datos', 'class_date' => Carbon::now()->subMonths(5), 'start_time' => '09:00', 'end_time' => '11:00', 'class_status' => 'FINISHED']),
            Classes::create(['group_id' => $group_DB_A->id, 'class_name' => 'SQL Avanzado', 'class_date' => Carbon::now()->subMonths(4), 'start_time' => '09:00', 'end_time' => '11:00', 'class_status' => 'FINISHED']),
        ];

        // Evaluaciones (3)
        $evals_DB_A = [
            Evaluation::create(['group_id' => $group_DB_A->id, 'title' => 'Examen Parcial SQL', 'evaluation_type' => 'Exam', 'due_date' => Carbon::now()->subMonths(5), 'weight' => 1.00, 'teacher_creator_id' => $teacherHugo->id]),
            Evaluation::create(['group_id' => $group_DB_A->id, 'title' => 'Práctica Calificada 2', 'evaluation_type' => 'Quiz', 'due_date' => Carbon::now()->subMonths(4), 'weight' => 1.00, 'teacher_creator_id' => $teacherJohan->id]),
            Evaluation::create(['group_id' => $group_DB_A->id, 'title' => 'Proyecto Final DB', 'evaluation_type' => 'Project', 'due_date' => Carbon::now()->subMonths(3), 'weight' => 2.00, 'teacher_creator_id' => $teacherHugo->id]),
        ];
        $totalWeight_DB_A = 4.00;

        $grades_DB_A = [
            // Desaprobados
            ['g1' => 10, 'g2' => 10, 'g3' => 9],
            ['g1' => 8, 'g2' => 12, 'g3' => 7],
            // Aprobados
            ['g1' => 15, 'g2' => 15, 'g3' => 15],
            ['g1' => 19, 'g2' => 16, 'g3' => 17],
            ['g1' => 13, 'g2' => 12, 'g3' => 17],
            ['g1' => 16, 'g2' => 15, 'g3' => 19],
        ];

        // Registrar data por alumno
        foreach ($participants_DB_A_students as $index => $participant) {
            // Asistencias (se mantiene el random como en el original)
            Attendance::create(['group_participant_id' => $participant->id, 'class_id' => $classes_DB_A[0]->id, 'attended' => true]);
            Attendance::create(['group_participant_id' => $participant->id, 'class_id' => $classes_DB_A[1]->id, 'attended' => true]);
            Attendance::create(['group_participant_id' => $participant->id, 'class_id' => $classes_DB_A[2]->id, 'attended' => rand(0, 1)]);

            // Notas (Asignadas desde el array)
            $grade1 = $grades_DB_A[$index]['g1'];
            $grade2 = $grades_DB_A[$index]['g2'];
            $grade3 = $grades_DB_A[$index]['g3'];
            GradeRecord::create(['evaluation_id' => $evals_DB_A[0]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grade1]);
            GradeRecord::create(['evaluation_id' => $evals_DB_A[1]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grade2]);
            GradeRecord::create(['evaluation_id' => $evals_DB_A[2]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grade3]);

            // Nota Final
            $finalGrade = (($grade1 * $evals_DB_A[0]->weight) + ($grade2 * $evals_DB_A[1]->weight) + ($grade3 * $evals_DB_A[2]->weight)) / $totalWeight_DB_A;
            $status = ($finalGrade >= $minPassingGrade) ? 'Passed' : 'Failed';
            FinalGrade::create([
                'user_id' => $participant->user_id,
                'group_id' => $group_DB_A->id,
                'final_grade' => $finalGrade,
                'program_status' => $status,
            ]);

            // Certificado (si aprobó)
            if ($status == 'Passed') {
                Credential::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $participant->user_id,
                    'group_id' => $group_DB_A->id,
                    'issue_date' => Carbon::now()->subMonths(2),
                ]);
            }
        }

        // --- GRUPO: BASE DE DATOS - GRUPO 2023 B ---
        $group_DB_B = Group::create([
            'course_id' => $courseMap['BASE DE DATOS']->id,
            'code' => 'DB-2023-B',
            'name' => 'Grupo 2023 B',
            'start_date' => Carbon::now()->subMonths(7),
            'end_date' => Carbon::now()->subMonths(4),
            'status' => 'completed',
        ]);

        // Profesores y Alumnos
        GroupParticipant::create(['group_id' => $group_DB_B->id, 'user_id' => $teacherJohan->id, 'role' => 'teacher']);
        $participants_DB_B_students = [];
        foreach ($studentsHalf2 as $student) {
            $participants_DB_B_students[] = GroupParticipant::create(['group_id' => $group_DB_B->id, 'user_id' => $student->id, 'role' => 'student']);
        }

        // Clases (3)
        $classes_DB_B = [
            Classes::create(['group_id' => $group_DB_B->id, 'class_name' => 'Intro', 'class_date' => Carbon::now()->subMonths(6), 'start_time' => '18:00', 'end_time' => '20:00', 'class_status' => 'FINISHED']),
            Classes::create(['group_id' => $group_DB_B->id, 'class_name' => 'Modelado', 'class_date' => Carbon::now()->subMonths(6), 'start_time' => '18:00', 'end_time' => '20:00', 'class_status' => 'FINISHED']),
            Classes::create(['group_id' => $group_DB_B->id, 'class_name' => 'Indices', 'class_date' => Carbon::now()->subMonths(5), 'start_time' => '18:00', 'end_time' => '20:00', 'class_status' => 'FINISHED']),
        ];

        // Evaluaciones (3)
        $evals_DB_B = [
            Evaluation::create(['group_id' => $group_DB_B->id, 'title' => 'PC1', 'evaluation_type' => 'Quiz', 'due_date' => Carbon::now()->subMonths(6), 'weight' => 1.00, 'teacher_creator_id' => $teacherJohan->id]),
            Evaluation::create(['group_id' => $group_DB_B->id, 'title' => 'PC2', 'evaluation_type' => 'Quiz', 'due_date' => Carbon::now()->subMonths(5), 'weight' => 1.00, 'teacher_creator_id' => $teacherJohan->id]),
            Evaluation::create(['group_id' => $group_DB_B->id, 'title' => 'Examen Final', 'evaluation_type' => 'Exam', 'due_date' => Carbon::now()->subMonths(4), 'weight' => 2.00, 'teacher_creator_id' => $teacherJohan->id]),
        ];
        $totalWeight_DB_B = 4.00;

        $grades_DB_B = [
            // Desaprobados
            ['g1' => 10, 'g2' => 10, 'g3' => 10],
            ['g1' => 8, 'g2' => 12, 'g3' => 11],
            // Aprobados
            ['g1' => 15, 'g2' => 15, 'g3' => 15],
            ['g1' => 19, 'g2' => 16, 'g3' => 17],
            ['g1' => 13, 'g2' => 12, 'g3' => 17],
            ['g1' => 16, 'g2' => 15, 'g3' => 19],
        ];

        // Registrar data por alumno
        foreach ($participants_DB_B_students as $index => $participant) {
            // Asistencias
            Attendance::create(['group_participant_id' => $participant->id, 'class_id' => $classes_DB_B[0]->id, 'attended' => true]);
            Attendance::create(['group_participant_id' => $participant->id, 'class_id' => $classes_DB_B[1]->id, 'attended' => rand(0, 1)]);
            Attendance::create(['group_participant_id' => $participant->id, 'class_id' => $classes_DB_B[2]->id, 'attended' => true]);

            // Notas
            $grade1 = $grades_DB_B[$index]['g1'];
            $grade2 = $grades_DB_B[$index]['g2'];
            $grade3 = $grades_DB_B[$index]['g3'];
            GradeRecord::create(['evaluation_id' => $evals_DB_B[0]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grade1]);
            GradeRecord::create(['evaluation_id' => $evals_DB_B[1]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grade2]);
            GradeRecord::create(['evaluation_id' => $evals_DB_B[2]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grade3]);

            // Nota Final
            $finalGrade = (($grade1 * $evals_DB_B[0]->weight) + ($grade2 * $evals_DB_B[1]->weight) + ($grade3 * $evals_DB_B[2]->weight)) / $totalWeight_DB_B;
            $status = ($finalGrade >= $minPassingGrade) ? 'Passed' : 'Failed';
            FinalGrade::create([
                'user_id' => $participant->user_id,
                'group_id' => $group_DB_B->id,
                'final_grade' => $finalGrade,
                'program_status' => $status,
            ]);

            // Certificado (si aprobó)
            if ($status == 'Passed') {
                Credential::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $participant->user_id,
                    'group_id' => $group_DB_B->id,
                    'issue_date' => Carbon::now()->subMonths(3),
                ]);
            }
        }


        // -----------------------------------------------------------------
        // 6. GRUPOS "IN_PROGRESS"
        // -----------------------------------------------------------------
        $this->command->info('Creando grupos IN_PROGRESS (Móviles A, B y Data Center A)...');

        // --- GRUPO: APLICACIONES MOVILES - GRUPO 2025 A ---
        $group_MOV_A = Group::create([
            'course_id' => $courseMap['APLICACIONES MOVILES']->id,
            'code' => 'MOV-2025-A',
            'name' => 'Grupo 2025 A',
            'start_date' => Carbon::now()->subMonth(),
            'end_date' => Carbon::now()->addMonths(2),
            'status' => 'in_progress',
        ]);

        // Profesores y Alumnos
        GroupParticipant::create(['group_id' => $group_MOV_A->id, 'user_id' => $teacherMirko->id, 'role' => 'teacher']);
        $participants_MOV_A_students = [];
        foreach ($studentsQuarter1 as $student) {
            $participants_MOV_A_students[] = GroupParticipant::create(['group_id' => $group_MOV_A->id, 'user_id' => $student->id, 'role' => 'student']);
        }

        // Clases (3)
        $classes_MOV_A = [
            Classes::create(['group_id' => $group_MOV_A->id, 'class_name' => 'Intro a Móviles', 'class_date' => Carbon::now()->subWeeks(2), 'start_time' => '19:00', 'end_time' => '21:00', 'class_status' => 'FINISHED']),
            Classes::create(['group_id' => $group_MOV_A->id, 'class_name' => 'UI/UX Móvil', 'class_date' => Carbon::now()->subWeek(), 'start_time' => '19:00', 'end_time' => '21:00', 'class_status' => 'FINISHED']),
            Classes::create(['group_id' => $group_MOV_A->id, 'class_name' => 'Conexión API', 'class_date' => Carbon::now()->addWeek(), 'start_time' => '19:00', 'end_time' => '21:00', 'class_status' => 'SCHEDULED']),
        ];

        // Evaluaciones (3)
        $evals_MOV_A = [
            Evaluation::create(['group_id' => $group_MOV_A->id, 'title' => 'Tarea 1: Mockup', 'evaluation_type' => 'Assignment', 'due_date' => Carbon::now()->subWeek(), 'weight' => 1.00, 'teacher_creator_id' => $teacherMirko->id]),
            Evaluation::create(['group_id' => $group_MOV_A->id, 'title' => 'Tarea 2: Primera App', 'evaluation_type' => 'Assignment', 'due_date' => Carbon::now()->addWeek(), 'weight' => 1.00, 'teacher_creator_id' => $teacherMirko->id]),
            Evaluation::create(['group_id' => $group_MOV_A->id, 'title' => 'Examen Parcial', 'evaluation_type' => 'Exam', 'due_date' => Carbon::now()->addMonths(1), 'weight' => 2.00, 'teacher_creator_id' => $teacherMirko->id]),
        ];

        // Notas para 3 alumnos. 
        $grades_MOV_A = [
            // Desaprobado
            ['g1' => 7, 'g2' => 8, 'g3' => 10],
            // Aprobados
            ['g1' => 15, 'g2' => 14, 'g3' => 16],
            ['g1' => 18, 'g2' => 16, 'g3' => 19],
        ];

        // Registrar data por alumno
        foreach ($participants_MOV_A_students as $index => $participant) {
            // Asistencias
            Attendance::create(['group_participant_id' => $participant->id, 'class_id' => $classes_MOV_A[0]->id, 'attended' => true]);
            Attendance::create(['group_participant_id' => $participant->id, 'class_id' => $classes_MOV_A[1]->id, 'attended' => rand(0, 1)]);
            // Asistencia para clase futura no se registra aún

            // Notas (3)
            GradeRecord::create(['evaluation_id' => $evals_MOV_A[0]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grades_MOV_A[$index]['g1']]);
            GradeRecord::create(['evaluation_id' => $evals_MOV_A[1]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grades_MOV_A[$index]['g2']]);
            GradeRecord::create(['evaluation_id' => $evals_MOV_A[2]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grades_MOV_A[$index]['g3']]);
            // SIN NOTA FINAL NI CERTIFICADO
        }

        // --- GRUPO: APLICACIONES MOVILES - GRUPO 2025 B ---
        $group_MOV_B = Group::create([
            'course_id' => $courseMap['APLICACIONES MOVILES']->id,
            'code' => 'MOV-2025-B',
            'name' => 'Grupo 2025 B',
            'start_date' => Carbon::now()->subWeeks(2),
            'end_date' => Carbon::now()->addMonths(2),
            'status' => 'in_progress',
        ]);

        // Profesores y Alumnos
        GroupParticipant::create(['group_id' => $group_MOV_B->id, 'user_id' => $teacherMirko->id, 'role' => 'teacher']);
        GroupParticipant::create(['group_id' => $group_MOV_B->id, 'user_id' => $teacherJohan->id, 'role' => 'teacher']);
        $participants_MOV_B_students = [];
        foreach ($studentsQuarter2 as $student) {
            $participants_MOV_B_students[] = GroupParticipant::create(['group_id' => $group_MOV_B->id, 'user_id' => $student->id, 'role' => 'student']);
        }

        // Clases y Evals (similar al anterior)
        $classes_MOV_B = [
            Classes::create(['group_id' => $group_MOV_B->id, 'class_name' => 'Intro', 'class_date' => Carbon::now()->subWeek(), 'start_time' => '09:00', 'end_time' => '11:00', 'class_status' => 'FINISHED']),
        ];
        $evals_MOV_B = [
            Evaluation::create(['group_id' => $group_MOV_B->id, 'title' => 'T1', 'evaluation_type' => 'Assignment', 'due_date' => Carbon::now()->addWeek(), 'weight' => 1.00, 'teacher_creator_id' => $teacherMirko->id]),
            Evaluation::create(['group_id' => $group_MOV_B->id, 'title' => 'T2', 'evaluation_type' => 'Assignment', 'due_date' => Carbon::now()->addWeeks(2), 'weight' => 1.00, 'teacher_creator_id' => $teacherJohan->id]),
            Evaluation::create(['group_id' => $group_MOV_B->id, 'title' => 'T3', 'evaluation_type' => 'Assignment', 'due_date' => Carbon::now()->addWeeks(3), 'weight' => 1.00, 'teacher_creator_id' => $teacherMirko->id]),
        ];

        $grades_MOV_B = [
            // Desaprobado
            ['g1' => 10, 'g2' => 7, 'g3' => 9],
            // Aprobados
            ['g1' => 15, 'g2' => 15, 'g3' => 15],
            ['g1' => 19, 'g2' => 17, 'g3' => 16],
        ];

        // Registrar data por alumno
        foreach ($participants_MOV_B_students as $index => $participant) {
            Attendance::create(['group_participant_id' => $participant->id, 'class_id' => $classes_MOV_B[0]->id, 'attended' => true]);

            // Notas (3)
            GradeRecord::create(['evaluation_id' => $evals_MOV_B[0]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grades_MOV_B[$index]['g1']]);
            GradeRecord::create(['evaluation_id' => $evals_MOV_B[1]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grades_MOV_B[$index]['g2']]);
            GradeRecord::create(['evaluation_id' => $evals_MOV_B[2]->id, 'user_id' => $participant->user_id, 'obtained_grade' => $grades_MOV_B[$index]['g3']]);
            // SIN NOTA FINAL NI CERTIFICADO
        }

        // --- GRUPO: ADMINISTRACION DE CENTROS DE DATOS - GRUPO 2025 A ---
        $group_DC_A = Group::create([
            'course_id' => $courseMap['ADMINISTRACION DE CENTROS DE DATOS']->id,
            'code' => 'DC-2025-A',
            'name' => 'Grupo 2025 A',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'status' => 'in_progress',
        ]);

        // Profesores y Alumnos
        GroupParticipant::create(['group_id' => $group_DC_A->id, 'user_id' => $teacherGuillermo->id, 'role' => 'teacher']);
        $participants_DC_A_students = [];
        foreach ($studentsLastHalf as $student) {
            $participants_DC_A_students[] = GroupParticipant::create(['group_id' => $group_DC_A->id, 'user_id' => $student->id, 'role' => 'student']);
        }

        // Clases (3)
        $classes_DC_A = [
            Classes::create(['group_id' => $group_DC_A->id, 'class_name' => 'Intro a Data Centers', 'class_date' => Carbon::now()->addWeek(), 'start_time' => '19:00', 'end_time' => '21:00', 'class_status' => 'SCHEDULED']),
            Classes::create(['group_id' => $group_DC_A->id, 'class_name' => 'Virtualización', 'class_date' => Carbon::now()->addWeeks(2), 'start_time' => '19:00', 'end_time' => '21:00', 'class_status' => 'SCHEDULED']),
            Classes::create(['group_id' => $group_DC_A->id, 'class_name' => 'Storage', 'class_date' => Carbon::now()->addWeeks(3), 'start_time' => '19:00', 'end_time' => '21:00', 'class_status' => 'SCHEDULED']),
        ];


        ClassMaterial::create([
            'class_id' => 1,
            'material_url' => 'https://drive.google.com/file/d/1cHLRtqRQ75mhjIXoBHioxTovixZtBs5q/view?usp=drivesdk',
            'type' => 'PDF',
        ]);

        ClassMaterial::create([
            'class_id' => 1,
            'material_url' => 'https://drive.google.com/file/d/1fqXj6xmvEUTVH27tENFASpH_caYn6Mbj/view?usp=drivesdk',
            'type' => 'PDF',
        ]);

        ClassMaterial::create([
            'class_id' => 1,
            'material_url' => 'https://res.cloudinary.com/dshi5w2wt/video/upload/v1761192975/Introducci%C3%B3n_a_HTML_Qu%C3%A9_es_y_c%C3%B3mo_funciona_HTML_-_DropCoding_360p_h264_atctpm.mp4',
            'type' => 'VIDEO',
        ]);

        ClassMaterial::create([
            'class_id' => 1,
            'material_url' => 'https://res.cloudinary.com/dshi5w2wt/image/upload/v1761195457/xjjtjo1hym5bqcfrxwlj.png',
            'type' => 'IMAGEN',
        ]);

        ClassMaterial::create([
            'class_id' => 1,
            'material_url' => 'https://docs.google.com/presentation/d/1n7cUzRp63lgiNbYQrUbPqVGVkVYH9Hnf/edit?usp=drivesdk&ouid=103623631906412399344&rtpof=true&sd=true',
            'type' => 'PPTX',
        ]);

        ClassMaterial::create([
            'class_id' => 1,
            'material_url' => 'https://docs.google.com/document/d/1nxP1NbhPdhRrQRan_2ofztIPPLuQnK_G/edit?usp=drivesdk&ouid=103623631906412399344&rtpof=true&sd=true',
            'type' => 'DOCX',
        ]);

        ClassMaterial::create([
            'class_id' => 1,
            'material_url' => 'https://docs.google.com/spreadsheets/d/1YW1OmkV91AyiXeRHU5aC6eHbEIED1LZg/edit?usp=drivesdk&ouid=103623631906412399344&rtpof=true&sd=true',
            'type' => 'XLSX',
        ]);

        ClassMaterial::create([
            'class_id' => 1,
            'material_url' => 'https://github.com/josevasquezramos/icv-procesos-academicos-backend',
            'type' => 'ENLACE',
        ]);

        ClassMaterial::create([
            'class_id' => 2,
            'material_url' => 'https://docs.google.com/presentation/d/1EVtQAxvBnDuiGPtUCmnzwACQEFeA7VWm/edit?usp=drivesdk&ouid=103623631906412399344&rtpof=true&sd=true',
            'type' => 'PPTX',
        ]);

        // Buscar un usuario admin (role es un array JSON)
        $adminUser = User::whereJsonContains('role', 'admin')
            ->orWhereJsonContains('role', 'administrador')
            ->first();

        // Si no hay admin, usar el primer usuario
        if (!$adminUser) {
            $adminUser = User::first();
        }

        // Crear una encuesta de ejemplo
        $survey = Survey::create([
            'title' => 'Encuesta de Satisfacción del Programa',
            'target_type' => 'graduates',
            'created_by_user_id' => $adminUser?->id,
        ]);

        // Crear preguntas para la encuesta
        $questions = [
            [
                'question_text' => '¿Cómo calificarías la calidad general del programa?',
                'question_type' => 'rating',
            ],
            [
                'question_text' => '¿Los contenidos del curso fueron relevantes para tu desarrollo profesional?',
                'question_type' => 'text',
            ],
            [
                'question_text' => '¿Recomendarías este programa a otros profesionales?',
                'question_type' => 'multiple_choice',
            ],
            [
                'question_text' => '¿Qué aspectos del programa te parecieron más valiosos?',
                'question_type' => 'text',
            ],
        ];

        foreach ($questions as $questionData) {
            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'question_text' => $questionData['question_text'],
                'question_type' => $questionData['question_type'],
            ]);
        }

        // Crear perfiles laborales de ejemplo para usuarios existentes
        // Como role es array, buscamos usuarios que tengan 'student' o 'estudiante' en su role
        $users = User::whereJsonContains('role', 'student')
            ->orWhereJsonContains('role', 'estudiante')
            ->take(10)
            ->get();

        // Si no hay usuarios con ese role, tomar los primeros 10
        if ($users->isEmpty()) {
            $users = User::take(10)->get();
        }

        $employmentStatuses = ['empleado', 'independiente', 'emprendedor', 'buscando', 'estudiando'];
        $industries = ['tecnologia', 'educacion', 'salud', 'finanzas', 'retail'];
        $salaryRanges = ['1000-2000', '2000-3000', '3000-5000', '5000-8000'];

        foreach ($users as $user) {
            EmploymentProfile::create([
                'user_id' => $user->id,
                'employment_status' => $employmentStatuses[array_rand($employmentStatuses)],
                'company_name' => 'Empresa ' . fake()->company(),
                'position' => fake()->jobTitle(),
                'start_date' => fake()->dateTimeBetween('-2 years', 'now'),
                'salary_range' => $salaryRanges[array_rand($salaryRanges)],
                'industry' => $industries[array_rand($industries)],
                'is_related_to_studies' => fake()->boolean(75), // 75% trabajan en área relacionada
            ]);
        }


        // -----------------------------------------------------------------
        // 7. REPORTE FINAL
        // -----------------------------------------------------------------
        $this->command->info('✅ Seeder ejecutado exitosamente!');
        $this->command->info('📊 Registros creados:');
        $this->command->info('   - Configuración Académica: ' . AcademicSetting::count());
        $this->command->info('   - Usuarios: ' . User::count());
        $this->command->info('   - Perfiles de Profesor: ' . TeacherProfile::count());
        $this->command->info('   - Programas: ' . Program::count());
        $this->command->info('   - Cursos: ' . Course::count());
        $this->command->info('   - Cursos de Programas: ' . ProgramCourse::count());
        $this->command->info('   - Requisitos Previos: ' . CoursePreviousRequirement::count());
        $this->command->info('   - Grupos: ' . Group::count());
        $this->command->info('   - Clases: ' . Classes::count());
        $this->command->info('   - Participantes de Grupo: ' . GroupParticipant::count());
        $this->command->info('   - Asistencias: ' . Attendance::count());
        $this->command->info('   - Evaluaciones: ' . Evaluation::count());
        $this->command->info('   - Registros de Calificaciones: ' . GradeRecord::count());
        // $this->command->info('   - Calificaciones Finales: ' . FinalGrade::count());
        // $this->command->info('   - Credenciales: ' . Credential::count());
        $this->command->info('   - Program-Courses: ' . ProgramCourse::count());
        $this->command->info('   - Class-Material: ' . ClassMaterial::count());
        $this->command->info('   - Requisitos Previos: ' . CoursePreviousRequirement::count());
        $this->command->info('✅ Módulo de Egresados: Encuesta y perfiles laborales creados exitosamente');

    }
}