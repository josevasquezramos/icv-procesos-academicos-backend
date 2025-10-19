<?php

namespace Database\Seeders;

use App\Models\AcademicSetting;
use App\Models\User;
use App\Models\Program;
use App\Models\Course;
use App\Models\Group;
use App\Models\Classes;
use App\Models\GroupParticipant;
use App\Models\Evaluation;
use App\Models\Attendance;
use App\Models\CoursePreviousRequirement;
use App\Models\FinalGrade;
use App\Models\GradeRecord;
use App\Models\ProgramCourse;
use App\Models\TeacherProfile;
use App\Models\Credential;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        AcademicSetting::create([
            'base_grade' => 20.00,
            'min_passing_grade' => 11.00,
        ]);

        // -----------------------------------------------------------------
        // 1. CREACIÓN DE USUARIOS
        // -----------------------------------------------------------------

        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'full_name' => 'Admin Principal',
            'dni' => '11111111',
            'document' => 'DOC111111',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999111111',
            'address' => 'Av. Admin 123',
            'birth_date' => '1980-01-01',
            'role' => ['admin'],
            'gender' => 'male',
            'country' => 'Peru',
            'country_location' => 'Lima',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        $teacher1 = User::create([
            'first_name' => 'Carlos',
            'last_name' => 'García',
            'full_name' => 'Carlos García',
            'dni' => '22222222',
            'document' => 'DOC222222',
            'email' => 'carlos.garcia@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999222222',
            'address' => 'Av. Docente 456',
            'birth_date' => '1985-05-10',
            'role' => ['teacher', 'student'],
            'gender' => 'male',
            'country' => 'Peru',
            'country_location' => 'Arequipa',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        $teacher2 = User::create([
            'first_name' => 'María',
            'last_name' => 'López',
            'full_name' => 'María López',
            'dni' => '33333333',
            'document' => 'DOC333333',
            'email' => 'maria.lopez@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999333333',
            'address' => 'Jr. Educación 789',
            'birth_date' => '1988-08-15',
            'role' => ['teacher'],
            'gender' => 'female',
            'country' => 'Peru',
            'country_location' => 'Cusco',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        $student1 = User::create([
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'full_name' => 'Juan Pérez',
            'dni' => '44444444',
            'document' => 'DOC444444',
            'email' => 'juan.perez@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999444444',
            'address' => 'Av. Estudiante 101',
            'birth_date' => '2000-03-20',
            'role' => ['student'],
            'gender' => 'male',
            'country' => 'Peru',
            'country_location' => 'Lima',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        $student2 = User::create([
            'first_name' => 'Ana',
            'last_name' => 'Martínez',
            'full_name' => 'Ana Martínez',
            'dni' => '55555555',
            'document' => 'DOC555555',
            'email' => 'ana.martinez@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999555555',
            'address' => 'Jr. Aprendizaje 202',
            'birth_date' => '2001-07-12',
            'role' => ['student'],
            'gender' => 'female',
            'country' => 'Peru',
            'country_location' => 'Trujillo',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        $student3 = User::create([
            'first_name' => 'Pedro',
            'last_name' => 'Sánchez',
            'full_name' => 'Pedro Sánchez',
            'dni' => '66666666',
            'document' => 'DOC666666',
            'email' => 'pedro.sanchez@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999666666',
            'address' => 'Av. Conocimiento 303',
            'birth_date' => '1999-11-25',
            'role' => ['student'],
            'gender' => 'male',
            'country' => 'Peru',
            'country_location' => 'Piura',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        // -----------------------------------------------------------------
        // 2. PERFILES DE PROFESORES
        // -----------------------------------------------------------------

        $teacherProfile1 = TeacherProfile::create([
            'user_id' => $teacher1->id,
            'professional_title' => 'Ingeniero de Software',
            'specialty' => 'Desarrollo Web',
            'experience_years' => 10,
            'biography' => 'Especialista en desarrollo web con más de 10 años de experiencia en proyectos empresariales.',
        ]);

        $teacherProfile2 = TeacherProfile::create([
            'user_id' => $teacher2->id,
            'professional_title' => 'Master en Inteligencia Artificial',
            'specialty' => 'Machine Learning',
            'experience_years' => 8,
            'biography' => 'Experta en IA y Machine Learning, con publicaciones en conferencias internacionales.',
        ]);

        // Creamos un tercer profesor
        $teacher3 = User::create([
            'first_name' => 'Luis',
            'last_name' => 'Rodríguez',
            'full_name' => 'Luis Rodríguez',
            'dni' => '77777777',
            'document' => 'DOC777777',
            'email' => 'luis.rodriguez@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999777777',
            'address' => 'Calle Profesor 505',
            'birth_date' => '1990-02-28',
            'role' => ['teacher'],
            'gender' => 'male',
            'country' => 'Peru',
            'country_location' => 'Chiclayo',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        $teacherProfile3 = TeacherProfile::create([
            'user_id' => $teacher3->id,
            'professional_title' => 'Ingeniero en Ciberseguridad',
            'specialty' => 'Seguridad Informática',
            'experience_years' => 5,
            'biography' => 'Especialista en ciberseguridad y hacking ético, certificado en CEH y OSCP.',
        ]);

        // -----------------------------------------------------------------
        // 3. PROGRAMAS
        // -----------------------------------------------------------------

        $program1 = Program::create([
            'name' => 'Diplomado en Desarrollo Web Full Stack',
            'description' => 'Programa completo de desarrollo web con tecnologías modernas',
            'duration_weeks' => 24,
            'max_capacity' => 30,
            'start_date' => '2025-01-15',
            'end_date' => '2025-07-15',
            'price' => 2500.00,
            'currency' => 'PEN',
            'image_url' => 'https://example.com/programs/fullstack.jpg',
            'modality' => 'hybrid',
            'required_devices' => 'Laptop con mínimo 8GB RAM',
            'status' => 'active',
        ]);

        $program2 = Program::create([
            'name' => 'Diplomado en Data Science',
            'description' => 'Programa especializado en ciencia de datos y análisis',
            'duration_weeks' => 20,
            'max_capacity' => 25,
            'start_date' => '2025-02-01',
            'end_date' => '2025-06-30',
            'price' => 3000.00,
            'currency' => 'PEN',
            'image_url' => 'https://example.com/programs/datascience.jpg',
            'modality' => 'virtual',
            'required_devices' => 'PC con GPU recomendado',
            'status' => 'active',
        ]);

        $program3 = Program::create([
            'name' => 'Diplomado en Ciberseguridad',
            'description' => 'Programa avanzado de seguridad informática y ethical hacking',
            'duration_weeks' => 16,
            'max_capacity' => 20,
            'start_date' => '2025-03-01',
            'end_date' => '2025-06-30',
            'price' => 2800.00,
            'currency' => 'PEN',
            'image_url' => 'https://example.com/programs/cybersec.jpg',
            'modality' => 'hybrid',
            'required_devices' => 'Laptop con virtualización habilitada',
            'status' => 'active',
        ]);

        // -----------------------------------------------------------------
        // 4. CURSOS
        // -----------------------------------------------------------------

        $course1 = Course::create([
            'course_id' => '10',
            'title' => 'Introducción al Desarrollo Web',
            'name' => 'Fundamentos de HTML, CSS y JavaScript',
            'description' => 'Curso básico de desarrollo web para principiantes',
            'level' => 'basic',
            'course_image' => 'https://example.com/courses/web101.jpg',
            'video_url' => 'https://example.com/videos/web101.mp4',
            'duration' => 40.00,
            'sessions' => 12,
            'selling_price' => 500.00,
            'discount_price' => 400.00,
            'prerequisites' => 'Conocimientos básicos de computación',
            'certificate_name' => true,
            'certificate_issuer' => 'Academia Tech',
            'bestseller' => true,
            'featured' => true,
            'highest_rated' => false,
            'status' => true,
        ]);

        $course2 = Course::create([
            'course_id' => '11',
            'title' => 'Laravel Avanzado',
            'name' => 'Desarrollo de aplicaciones con Laravel',
            'description' => 'Curso avanzado de desarrollo backend con Laravel',
            'level' => 'intermediate',
            'course_image' => 'https://example.com/courses/laravel.jpg',
            'video_url' => 'https://example.com/videos/laravel.mp4',
            'duration' => 60.00,
            'sessions' => 18,
            'selling_price' => 800.00,
            'discount_price' => 650.00,
            'prerequisites' => 'Conocimientos de PHP básico',
            'certificate_name' => true,
            'certificate_issuer' => 'Academia Tech',
            'bestseller' => false,
            'featured' => true,
            'highest_rated' => true,
            'status' => true,
        ]);

        $course3 = Course::create([
            'course_id' => '12',
            'title' => 'React y Vue.js',
            'name' => 'Frameworks JavaScript Modernos',
            'description' => 'Domina los frameworks más populares de JavaScript',
            'level' => 'advanced',
            'course_image' => 'https://example.com/courses/react-vue.jpg',
            'video_url' => 'https://example.com/videos/react-vue.mp4',
            'duration' => 50.00,
            'sessions' => 15,
            'selling_price' => 700.00,
            'discount_price' => 600.00,
            'prerequisites' => 'JavaScript intermedio',
            'certificate_name' => true,
            'certificate_issuer' => 'Academia Tech',
            'bestseller' => true,
            'featured' => false,
            'highest_rated' => true,
            'status' => true,
        ]);

        // -----------------------------------------------------------------
        // 5. PROGRAM_COURSES
        // -----------------------------------------------------------------

        $programCourse1 = ProgramCourse::create([
            'program_id' => $program1->id,
            'course_id' => $course1->id,
            'mandatory' => true,
        ]);

        $programCourse2 = ProgramCourse::create([
            'program_id' => $program1->id,
            'course_id' => $course2->id,
            'mandatory' => true,
        ]);

        $programCourse3 = ProgramCourse::create([
            'program_id' => $program1->id,
            'course_id' => $course3->id,
            'mandatory' => false,
        ]);

        // -----------------------------------------------------------------
        // 6. COURSE_PREVIOUS_REQUIREMENTS
        // -----------------------------------------------------------------

        $requirement1 = CoursePreviousRequirement::create([
            'course_id' => $course2->id,
            'previous_course_id' => $course1->id,
        ]);

        $requirement2 = CoursePreviousRequirement::create([
            'course_id' => $course3->id,
            'previous_course_id' => $course1->id,
        ]);

        $requirement3 = CoursePreviousRequirement::create([
            'course_id' => $course3->id,
            'previous_course_id' => $course2->id,
        ]);

        // -----------------------------------------------------------------
        // 7. GRUPOS
        // -----------------------------------------------------------------

        $group1 = Group::create([
            'course_id' => $course1->id,
            'code' => 'WEB101-2025-A',
            'name' => 'Grupo A - Mañana',
            'start_date' => '2025-01-15',
            'end_date' => '2025-03-15',
            'status' => 'approved',
        ]);

        $group2 = Group::create([
            'course_id' => $course2->id,
            'code' => 'PHP201-2025-A',
            'name' => 'Grupo A - Tarde',
            'start_date' => '2025-02-01',
            'end_date' => '2025-04-30',
            'status' => 'open',
        ]);

        $group3 = Group::create([
            'course_id' => $course3->id,
            'code' => 'JS301-2025-A',
            'name' => 'Grupo A - Noche',
            'start_date' => '2025-03-01',
            'end_date' => '2025-05-15',
            'status' => 'cancelled',
        ]);

        // -----------------------------------------------------------------
        // 8. GROUP_PARTICIPANTS
        // -----------------------------------------------------------------

        // Profesores asignados
        $participant1 = GroupParticipant::create([
            'group_id' => $group1->id,
            'user_id' => $teacher1->id,
            'role' => 'teacher',
            'enrollment_status' => 'active',
            'assignment_date' => now(),
        ]);

        $participant2 = GroupParticipant::create([
            'group_id' => $group2->id,
            'user_id' => $teacher2->id,
            'role' => 'teacher',
            'enrollment_status' => 'active',
            'assignment_date' => now(),
        ]);

        // Estudiantes inscritos
        $participant3 = GroupParticipant::create([
            'group_id' => $group1->id,
            'user_id' => $student1->id,
            'role' => 'student',
            'enrollment_status' => 'active',
            'assignment_date' => now(),
        ]);

        $participant4 = GroupParticipant::create([
            'group_id' => $group1->id,
            'user_id' => $student2->id,
            'role' => 'student',
            'enrollment_status' => 'active',
            'assignment_date' => now(),
        ]);

        $participant5 = GroupParticipant::create([
            'group_id' => $group2->id,
            'user_id' => $student3->id,
            'role' => 'student',
            'enrollment_status' => 'active',
            'assignment_date' => now(),
        ]);

        // -----------------------------------------------------------------
        // 9. CLASSES
        // -----------------------------------------------------------------

        $class1 = Classes::create([
            'group_id' => $group1->id,
            'class_name' => 'Introducción a HTML',
            'description' => 'Primera sesión sobre estructuras HTML básicas',
            'class_date' => '2025-01-15',
            'start_time' => '2025-01-15 09:00:00',
            'end_time' => '2025-01-15 12:00:00',
            'class_status' => 'SCHEDULED',
        ]);

        $class2 = Classes::create([
            'group_id' => $group1->id,
            'class_name' => 'CSS y Diseño Responsivo',
            'description' => 'Aprende a estilizar páginas web con CSS',
            'class_date' => '2025-01-17',
            'start_time' => '2025-01-17 09:00:00',
            'end_time' => '2025-01-17 12:00:00',
            'class_status' => 'FINISHED',
        ]);

        $class3 = Classes::create([
            'group_id' => $group1->id,
            'class_name' => 'JavaScript Básico',
            'description' => 'Introducción a la programación con JavaScript',
            'class_date' => '2025-01-20',
            'start_time' => '2025-01-20 09:00:00',
            'end_time' => '2025-01-20 12:00:00',
            'class_status' => 'CANCELLED',
        ]);

        // -----------------------------------------------------------------
        // 10. ATTENDANCES
        // -----------------------------------------------------------------

        $attendance1 = Attendance::create([
            'group_participant_id' => $participant3->id,
            'class_id' => $class1->id,
            'attended' => true,
            'observations' => 'Participación activa en clase',
        ]);

        $attendance2 = Attendance::create([
            'group_participant_id' => $participant3->id,
            'class_id' => $class2->id,
            'attended' => true,
            'observations' => 'Completó todos los ejercicios',
        ]);

        $attendance3 = Attendance::create([
            'group_participant_id' => $participant4->id,
            'class_id' => $class1->id,
            'attended' => false,
            'observations' => 'Ausencia justificada por motivos médicos',
        ]);

        // -----------------------------------------------------------------
        // 11. EVALUATIONS
        // -----------------------------------------------------------------

        $evaluation1 = Evaluation::create([
            'group_id' => $group1->id,
            'title' => 'Examen Parcial - HTML/CSS',
            'description' => 'Evaluación de conocimientos básicos de HTML y CSS',
            'external_url' => 'https://example.com/exam/parcial-html',
            'evaluation_type' => 'Exam',
            'due_date' => '2025-02-15 23:59:59',
            'weight' => 1.00,
            'teacher_creator_id' => $teacher1->id,
        ]);

        $evaluation2 = Evaluation::create([
            'group_id' => $group1->id,
            'title' => 'Proyecto Final - Página Web',
            'description' => 'Crear una página web completa usando HTML, CSS y JS',
            'external_url' => 'https://example.com/project/final',
            'evaluation_type' => 'Project',
            'due_date' => '2025-03-10 23:59:59',
            'weight' => 2.00,
            'teacher_creator_id' => $teacher1->id,
        ]);

        $evaluation3 = Evaluation::create([
            'group_id' => $group2->id,
            'title' => 'Quiz - Laravel Routing',
            'description' => 'Evaluación rápida sobre rutas en Laravel',
            'external_url' => 'https://example.com/quiz/laravel-routes',
            'evaluation_type' => 'Quiz',
            'due_date' => '2025-02-20 23:59:59',
            'weight' => 1.00,
            'teacher_creator_id' => $teacher2->id,
        ]);

        // -----------------------------------------------------------------
        // 12. GRADE_RECORDS
        // -----------------------------------------------------------------

        $gradeRecord1 = GradeRecord::create([
            'evaluation_id' => $evaluation1->id,
            'user_id' => $student1->id,
            'obtained_grade' => 15.50,
            'feedback' => 'Excelente comprensión de HTML y CSS',
            'record_date' => now(),
        ]);

        $gradeRecord2 = GradeRecord::create([
            'evaluation_id' => $evaluation1->id,
            'user_id' => $student2->id,
            'obtained_grade' => 11.00,
            'feedback' => 'Buen trabajo, pero necesita mejorar en flexbox',
            'record_date' => now(),
        ]);

        $gradeRecord4 = GradeRecord::create([
            'evaluation_id' => $evaluation2->id,
            'user_id' => $student1->id,
            'obtained_grade' => 18.00,
            'feedback' => 'Buen trabajo',
            'record_date' => now(),
        ]);

        $gradeRecord5 = GradeRecord::create([
            'evaluation_id' => $evaluation2->id,
            'user_id' => $student2->id,
            'obtained_grade' => 7.00,
            'feedback' => 'Decadencia',
            'record_date' => now(),
        ]);

        $gradeRecord3 = GradeRecord::create([
            'evaluation_id' => $evaluation3->id,
            'user_id' => $student3->id,
            'obtained_grade' => 15.00,
            'feedback' => 'Dominio completo de rutas en Laravel',
            'record_date' => now(),
        ]);

        // -----------------------------------------------------------------
        // 13. FINAL_GRADES
        // -----------------------------------------------------------------

        // $finalGrade1 = FinalGrade::create([
        //     'user_id' => $student1->id,
        //     'group_id' => $group1->id,
        //     'final_grade' => 85.50,
        //     'program_status' => 'Passed',
        //     'calculation_date' => now(),
        // ]);

        // $finalGrade2 = FinalGrade::create([
        //     'user_id' => $student2->id,
        //     'group_id' => $group1->id,
        //     'final_grade' => 72.00,
        //     'program_status' => 'Passed',
        //     'calculation_date' => now(),
        // ]);

        // $finalGrade3 = FinalGrade::create([
        //     'user_id' => $student3->id,
        //     'group_id' => $group2->id,
        //     'final_grade' => 90.00,
        //     'program_status' => 'In_progress',
        //     'calculation_date' => now(),
        // ]);

        // -----------------------------------------------------------------
        // 14. CREDENTIALS
        // -----------------------------------------------------------------

        // $credential1 = Credential::create([
        //     'user_id' => $student1->id,
        //     'group_id' => $group1->id,
        //     'issue_date' => '2025-03-20',
        // ]);

        // $credential2 = Credential::create([
        //     'user_id' => $student2->id,
        //     'group_id' => $group1->id,
        //     'issue_date' => '2025-03-20',
        // ]);

        // $credential3 = Credential::create([
        //     'user_id' => $student1->id,
        //     'group_id' => $group2->id,
        //     'issue_date' => '2025-07-01',
        // ]);

        $this->command->info('✅ Seeder ejecutado exitosamente!');
        $this->command->info('📊 Registros creados:');
        $this->command->info('   - Usuarios: ' . User::count());
        $this->command->info('   - Perfiles de Profesor: ' . TeacherProfile::count());
        $this->command->info('   - Programas: ' . Program::count());
        $this->command->info('   - Cursos: ' . Course::count());
        $this->command->info('   - Grupos: ' . Group::count());
        $this->command->info('   - Clases: ' . Classes::count());
        $this->command->info('   - Participantes: ' . GroupParticipant::count());
        $this->command->info('   - Asistencias: ' . Attendance::count());
        $this->command->info('   - Evaluaciones: ' . Evaluation::count());
        $this->command->info('   - Registros de Calificaciones: ' . GradeRecord::count());
        // $this->command->info('   - Calificaciones Finales: ' . FinalGrade::count());
        // $this->command->info('   - Credenciales: ' . Credential::count());
        $this->command->info('   - Program-Courses: ' . ProgramCourse::count());
        $this->command->info('   - Requisitos Previos: ' . CoursePreviousRequirement::count());
    }
}