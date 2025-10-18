<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Program;
use App\Models\Course;
use App\Models\Group;
use App\Models\ClassModel;
use App\Models\GroupParticipant;
use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Attempt;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\CoursePreviousRequirement;
use App\Models\Diploma;
use App\Models\FinalGrade;
use App\Models\GradeConfiguration;
use App\Models\GradeRecord;
use App\Models\Grading;
use App\Models\Graduate;
use App\Models\GraduateSurvey;
use App\Models\ProgramCourse;
use App\Models\StudentProfile;
use App\Models\TeacherApplication;
use App\Models\TeacherEvaluation;
use App\Models\TeacherProfile;
use App\Models\TeacherRecruitment;
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
        // -----------------------------------------------------------------
        // 1. CREACIÓN DE USUARIOS
        // -----------------------------------------------------------------

        // 1.1. Admin User
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
            'role' => ['admin'], // Rol como array
            'gender' => 'male',
            'country' => 'Peru',
            'country_location' => 'Lima',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        // 1.2. Teacher User (también es Student)
        $teacher = User::create([
            'first_name' => 'Profesor',
            'last_name' => 'Garcia',
            'full_name' => 'Profesor Garcia',
            'dni' => '22222222',
            'document' => 'DOC222222',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999222222',
            'address' => 'Av. Docente 456',
            'birth_date' => '1985-05-10',
            'role' => ['teacher', 'student'], // Roles como array
            'gender' => 'male',
            'country' => 'Peru',
            'country_location' => 'Arequipa',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        // 1.3. Student User 1
        $student1 = User::create([
            'first_name' => 'Ana',
            'last_name' => 'Lopez',
            'full_name' => 'Ana Lopez',
            'dni' => '33333333',
            'document' => 'DOC333333',
            'email' => 'student1@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999333333',
            'address' => 'Calle Estudiante 789',
            'birth_date' => '2000-03-15',
            'role' => ['student'], // Rol como array
            'gender' => 'female',
            'country' => 'Peru',
            'country_location' => 'Trujillo',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        // 1.4. Student User 2
        $student2 = User::create([
            'first_name' => 'Carlos',
            'last_name' => 'Ruiz',
            'full_name' => 'Carlos Ruiz',
            'dni' => '44444444',
            'document' => 'DOC444444',
            'email' => 'student2@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+51999444444',
            'address' => 'Jiron Alumno 101',
            'birth_date' => '2001-11-20',
            'role' => ['student'], // Rol como array
            'gender' => 'male',
            'country' => 'Peru',
            'country_location' => 'Cusco',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);

        // -----------------------------------------------------------------
        // 2. CREACIÓN DE PERFILES (Teacher/Student Profiles)
        // -----------------------------------------------------------------
        TeacherProfile::create([
            'user_id' => $teacher->id,
            'professional_title' => 'Ingeniero de Software',
            'specialty' => 'Desarrollo Web Full Stack',
            'experience_years' => 8,
            'biography' => 'Apasionado por la enseñanza y la tecnología.',
            'linkedin_link' => 'https://linkedin.com/in/profesor-garcia',
        ]);

        StudentProfile::create([
            'user_id' => $teacher->id, // El docente también tiene perfil de alumno
            'career_interest' => 'Data Science',
            'work_situation' => 'employed',
        ]);
        StudentProfile::create([
            'user_id' => $student1->id,
            'career_interest' => 'Frontend Development',
            'work_situation' => 'student',
        ]);
        StudentProfile::create([
            'user_id' => $student2->id,
            'career_interest' => 'Backend Development',
            'work_situation' => 'unemployed',
        ]);

        // -----------------------------------------------------------------
        // 3. ESTRUCTURA ACADÉMICA (Programs, Courses)
        // -----------------------------------------------------------------
        $program1 = Program::create([
            'name' => 'Diplomado en Desarrollo Web Full Stack',
            'description' => 'Aprende a construir aplicaciones web completas.',
            'duration_weeks' => 24,
            'max_capacity' => 50,
            'start_date' => '2025-11-01',
            'end_date' => '2026-05-01',
            'price' => 1200.00,
            'status' => 'active',
        ]);
        $program2 = Program::create([
            'name' => 'Especialización en Data Science',
            'description' => 'Domina el análisis de datos y machine learning.',
            'duration_weeks' => 16,
            'max_capacity' => 40,
            'start_date' => '2025-11-15',
            'end_date' => '2026-03-15',
            'price' => 900.00,
            'status' => 'active',
        ]);
        $program3 = Program::create([
            'name' => 'Curso Corto: Introducción a DevOps',
            'description' => 'CI/CD y automatización.',
            'duration_weeks' => 8,
            'price' => 300.00,
            'status' => 'inactive',
        ]);

        $course1 = Course::create([
            'course_id' => 101,
            'title' => 'Desarrollo Backend con Laravel',
            'name' => 'Laravel Avanzado',
            'description' => 'Construye APIs RESTful robustas.',
            'level' => 'advanced',
            'duration' => 40,
            'sessions' => 10,
            'selling_price' => 300.00,
            'status' => true,
        ]);
        $course2 = Course::create([
            'course_id' => 102,
            'title' => 'Desarrollo Frontend con React',
            'name' => 'React.js Moderno',
            'description' => 'Hooks, Context y Next.js.',
            'level' => 'intermediate',
            'duration' => 35,
            'sessions' => 8,
            'selling_price' => 250.00,
            'status' => true,
        ]);
        $course3 = Course::create([
            'course_id' => 103,
            'title' => 'Bases de Datos SQL',
            'name' => 'SQL desde Cero',
            'description' => 'Fundamentos de bases de datos relacionales.',
            'level' => 'basic',
            'duration' => 20,
            'sessions' => 5,
            'selling_price' => 150.00,
            'status' => true,
        ]);

        // Relaciones Programa-Curso
        ProgramCourse::create(['program_id' => $program1->id, 'course_id' => $course3->id, 'mandatory' => true]); // SQL
        ProgramCourse::create(['program_id' => $program1->id, 'course_id' => $course1->id, 'mandatory' => true]); // Laravel
        ProgramCourse::create(['program_id' => $program1->id, 'course_id' => $course2->id, 'mandatory' => true]); // React
        ProgramCourse::create(['program_id' => $program2->id, 'course_id' => $course3->id, 'mandatory' => true]); // SQL para Data Science

        // Requerimientos previos
        CoursePreviousRequirement::create(['course_id' => $course1->id, 'previous_course_id' => $course3->id]); // Laravel requiere SQL
        CoursePreviousRequirement::create(['course_id' => $course2->id, 'previous_course_id' => $course3->id]); // React requiere SQL

        // -----------------------------------------------------------------
        // 4. GRUPOS Y CLASES (Groups, ClassModel)
        // -----------------------------------------------------------------
        $group1 = Group::create([
            'course_id' => $course1->id, // Laravel
            'code' => 'LARAVEL-2025-11A',
            'name' => 'Grupo A - Laravel (Noche)',
            'start_date' => '2025-11-05',
            'end_date' => '2026-01-15',
            'status' => 'open',
        ]);
        $group2 = Group::create([
            'course_id' => $course2->id, // React
            'code' => 'REACT-2025-11B',
            'name' => 'Grupo B - React (Tarde)',
            'start_date' => '2025-11-10',
            'end_date' => '2026-01-10',
            'status' => 'in_progress',
        ]);
        $group3 = Group::create([
            'course_id' => $course3->id, // SQL
            'code' => 'SQL-2025-10C',
            'name' => 'Grupo C - SQL (Mañana)',
            'start_date' => '2025-10-20',
            'end_date' => '2025-11-10',
            'status' => 'completed',
        ]);

        // Clases
        $class1_g1 = ClassModel::create([
            'group_id' => $group1->id,
            'class_name' => 'Clase 1: Introducción a Laravel',
            'class_date' => '2025-11-05',
            'start_time' => '2025-11-05 19:00:00',
            'end_time' => '2025-11-05 21:00:00',
            'platform' => 'Zoom',
            'class_status' => 'FINISHED',
        ]);
        $class2_g1 = ClassModel::create([
            'group_id' => $group1->id,
            'class_name' => 'Clase 2: Rutas y Controladores',
            'class_date' => '2025-11-07',
            'start_time' => '2025-11-07 19:00:00',
            'end_time' => '2025-11-07 21:00:00',
            'class_status' => 'SCHEDULED',
        ]);
        $class1_g2 = ClassModel::create([
            'group_id' => $group2->id,
            'class_name' => 'Clase 1: Introducción a React',
            'class_date' => '2025-11-10',
            'start_time' => '2025-11-10 15:00:00',
            'end_time' => '2025-11-10 17:00:00',
            'class_status' => 'FINISHED',
        ]);

        // -----------------------------------------------------------------
        // 5. PARTICIPANTES Y ASISTENCIA (GroupParticipant, Attendance)
        // -----------------------------------------------------------------
        // El Profesor Garcia enseña en el Grupo 1 (Laravel)
        $teacher_g1 = GroupParticipant::create([
            'group_id' => $group1->id,
            'user_id' => $teacher->id,
            'role' => 'teacher',
            'teacher_function' => 'titular',
            'enrollment_status' => 'active',
        ]);
        // Alumno 1 se une al Grupo 1 (Laravel)
        $student1_g1 = GroupParticipant::create([
            'group_id' => $group1->id,
            'user_id' => $student1->id,
            'role' => 'student',
            'enrollment_status' => 'active',
        ]);
        // Alumno 2 se une al Grupo 1 (Laravel)
        $student2_g1 = GroupParticipant::create([
            'group_id' => $group1->id,
            'user_id' => $student2->id,
            'role' => 'student',
            'enrollment_status' => 'active',
        ]);
        // Alumno 1 también se une al Grupo 2 (React)
        $student1_g2 = GroupParticipant::create([
            'group_id' => $group2->id,
            'user_id' => $student1->id,
            'role' => 'student',
            'enrollment_status' => 'active',
        ]);
        // El Profesor (que es alumno) se une al Grupo 3 (SQL)
        $teacher_g3_student = GroupParticipant::create([
            'group_id' => $group3->id,
            'user_id' => $teacher->id,
            'role' => 'student',
            'enrollment_status' => 'finished',
        ]);

        // Asistencias para la Clase 1 del Grupo 1 (Finalizada)
        Attendance::create([
            'group_participant_id' => $teacher_g1->id,
            'class_id' => $class1_g1->id,
            'attended' => 'YES',
            'entry_time' => '2025-11-05 18:55:00',
            'exit_time' => '2025-11-05 21:05:00',
            'connected_minutes' => 130,
        ]);
        Attendance::create([
            'group_participant_id' => $student1_g1->id,
            'class_id' => $class1_g1->id,
            'attended' => 'YES',
            'entry_time' => '2025-11-05 18:59:00',
            'exit_time' => '2025-11-05 21:01:00',
            'connected_minutes' => 122,
            'connection_ip' => '192.168.1.10',
        ]);
        Attendance::create([
            'group_participant_id' => $student2_g1->id,
            'class_id' => $class1_g1->id,
            'attended' => 'NO',
        ]);

        // -----------------------------------------------------------------
        // 6. EVALUACIONES Y CALIFICACIONES (Evaluation, Question, Attempt, Grading)
        // -----------------------------------------------------------------
        $eval1_g1 = Evaluation::create([
            'group_id' => $group1->id,
            'title' => 'Examen Parcial 1 - Laravel',
            'evaluation_type' => 'Exam',
            'start_date' => '2025-11-20 09:00:00',
            'end_date' => '2025-11-20 18:00:00',
            'duration_minutes' => 60,
            'total_score' => 20.00,
            'status' => 'Active',
            'teacher_creator_id' => $teacher->id,
        ]);
        $eval2_g1 = Evaluation::create([
            'group_id' => $group1->id,
            'title' => 'Proyecto Final - API',
            'evaluation_type' => 'Project',
            'start_date' => '2025-12-15 09:00:00',
            'end_date' => '2026-01-10 23:59:00',
            'duration_minutes' => 10080,
            'total_score' => 20.00,
            'status' => 'Active',
            'teacher_creator_id' => $teacher->id,
        ]);
        $eval3_g1 = Evaluation::create([ // Un Quiz
            'group_id' => $group1->id,
            'title' => 'Quiz: Modelos y Eloquent',
            'evaluation_type' => 'Quiz',
            'start_date' => '2025-11-15 10:00:00',
            'end_date' => '2025-11-15 11:00:00',
            'duration_minutes' => 20,
            'total_score' => 20.00,
            'status' => 'Finished',
            'teacher_creator_id' => $teacher->id,
        ]);

        // Preguntas para el Quiz (eval3_g1)
        Question::create([
            'evaluation_id' => $eval3_g1->id,
            'statement' => '¿Qué comando crea un modelo y una migración?',
            'question_type' => 'Multiple',
            'answer_options' => ['A' => 'php artisan make:model -m', 'B' => 'php artisan model:create -m', 'C' => 'php artisan new:model --migration'],
            'correct_answer' => ['A'],
            'score' => 10.00,
        ]);
        Question::create([
            'evaluation_id' => $eval3_g1->id,
            'statement' => '¿Define qué es Eloquent?',
            'question_type' => 'Essay',
            'score' => 10.00,
        ]);

        // Intentos (Attempts) para el Quiz
        $attempt_s1 = Attempt::create([
            'evaluation_id' => $eval3_g1->id,
            'user_id' => $student1->id,
            'start_date' => '2025-11-15 10:05:00',
            'end_date' => '2025-11-15 10:15:00',
            'answers' => ['q1' => 'A', 'q2' => 'Es el ORM de Laravel.'], // q1 Correcta
            'obtained_score' => 10.00, // Autocalificado
            'status' => 'Completed',
        ]);
        $attempt_s2 = Attempt::create([
            'evaluation_id' => $eval3_g1->id,
            'user_id' => $student2->id,
            'start_date' => '2025-11-15 10:02:00',
            'end_date' => '2025-11-15 10:12:00',
            'answers' => ['q1' => 'B', 'q2' => 'No sé.'], // q1 Incorrecta
            'obtained_score' => 0.00,
            'status' => 'Completed',
        ]);

        // Calificación manual (Grading) de las preguntas de ensayo por el docente
        Grading::create([
            'attempt_id' => $attempt_s1->id,
            'teacher_grader_id' => $teacher->id,
            'grading_detail' => ['q2_score' => 8.00],
            'feedback' => 'Buena definición, faltó mencionar Active Record.',
            'grading_date' => now(),
        ]);
        $attempt_s1->update(['obtained_score' => 18.00]); // 10 (q1) + 8 (q2)

        Grading::create([
            'attempt_id' => $attempt_s2->id,
            'teacher_grader_id' => $teacher->id,
            'grading_detail' => ['q2_score' => 0.00],
            'feedback' => 'Respuesta incompleta.',
            'grading_date' => now(),
        ]);
        // $attempt_s2->update(['obtained_score' => 0.00]); // Sigue en 0

        // -----------------------------------------------------------------
        // 7. REGISTRO DE NOTAS (GradeConfiguration, GradeRecord, FinalGrade)
        // -----------------------------------------------------------------
        $config_g1 = GradeConfiguration::create([
            'group_id' => $group1->id,
            'grading_system' => 'Vigesimal (0-20)',
            'max_grade' => 20.00,
            'passing_grade' => 13.00,
            'evaluation_weight' => 100.00,
        ]);
        $config_g3 = GradeConfiguration::create([ // Para el grupo completado
            'group_id' => $group3->id,
            'grading_system' => 'Vigesimal (0-20)',
            'max_grade' => 20.00,
            'passing_grade' => 13.00,
        ]);

        // Registros de notas (basados en los attempts calificados)
        GradeRecord::create([
            'user_id' => $student1->id,
            'evaluation_id' => $eval3_g1->id, // El Quiz
            'group_id' => $group1->id,
            'configuration_id' => $config_g1->id,
            'obtained_grade' => 18.00,
            'grade_weight' => 20.00, // Quiz vale 20%
            'grade_type' => 'Partial',
            'status' => 'Published',
        ]);
        GradeRecord::create([
            'user_id' => $student2->id,
            'evaluation_id' => $eval3_g1->id, // El Quiz
            'group_id' => $group1->id,
            'configuration_id' => $config_g1->id,
            'obtained_grade' => 0.00,
            'grade_weight' => 20.00, // Quiz vale 20%
            'grade_type' => 'Partial',
            'status' => 'Published',
        ]);
        // Nota simulada del proyecto (eval 2) para estudiante 1
        GradeRecord::create([
            'user_id' => $student1->id,
            'evaluation_id' => $eval2_g1->id, // El Proyecto
            'group_id' => $group1->id,
            'configuration_id' => $config_g1->id,
            'obtained_grade' => 15.00,
            'grade_weight' => 50.00, // Proyecto vale 50%
            'grade_type' => 'Partial',
            'status' => 'Recorded',
        ]);

        // Notas Finales (para el grupo completado G3)
        FinalGrade::create([
            'user_id' => $teacher->id, // El profesor como alumno
            'group_id' => $group3->id,  // Curso SQL
            'configuration_id' => $config_g3->id,
            'final_grade' => 16.50,
            'program_status' => 'Passed',
            'certification_obtained' => true,
        ]);
        // Notas "en progreso" para G1
        FinalGrade::create([
            'user_id' => $student1->id,
            'group_id' => $group1->id,
            'configuration_id' => $config_g1->id,
            'final_grade' => 0.00, // Aún no calculada
            'partial_average' => 15.85, // Promedio ponderado de 18 (20%) y 15 (50%)
            'program_status' => 'In_progress',
        ]);
        FinalGrade::create([
            'user_id' => $student2->id,
            'group_id' => $group1->id,
            'configuration_id' => $config_g1->id,
            'final_grade' => 0.00,
            'partial_average' => 0.00, // Promedio ponderado de 0 (20%)
            'program_status' => 'In_progress',
        ]);

        // -----------------------------------------------------------------
        // 8. GRADUADOS Y CERTIFICACIONES (Graduate, Certificate, Diploma, Survey)
        // -----------------------------------------------------------------
        $grad1 = Graduate::create([
            'user_id' => $teacher->id,
            'program_id' => $program2->id, // Asumimos que completó el programa de Data Science
            'graduation_date' => '2025-03-20',
            'final_note' => 17.5,
            'state' => 'graduated',
            'employability' => 'Promovido en trabajo actual',
        ]);

        // Encuestas al graduado
        GraduateSurvey::create([
            'graduate_id' => $grad1->id,
            'date' => '2025-09-15',
            'employability' => 'Conseguí nuevo trabajo como Data Analyst',
            'satisfaction' => 'High',
            'curriculum_feedback' => 'El módulo de Machine Learning fue excelente.',
        ]);
        GraduateSurvey::create([
            'graduate_id' => $grad1->id,
            'date' => '2025-06-15',
            'employability' => 'Aún buscando',
            'satisfaction' => 'Medium',
            'curriculum_feedback' => 'Faltaron más ejemplos prácticos.',
        ]);
        GraduateSurvey::create([
            'graduate_id' => $grad1->id,
            'date' => '2025-10-15',
            'employability' => 'Promovido',
            'satisfaction' => 'High',
            'curriculum_feedback' => 'Muy buen programa.',
        ]);


        // Certificados y Diplomas
        Certificate::create([
            'user_id' => $teacher->id,
            'program_id' => $program2->id, // Certificado por Data Science
            'issue_date' => '2025-03-25',
            'status' => 'issued',
            'verification_code' => 'UNIQUE-CERT-CODE-123',
        ]);
        Diploma::create([
            'user_id' => $teacher->id,
            'program_id' => $program1->id, // Diploma por Full Stack (simulado)
            'issue_date' => '2026-05-10',
            'status' => 'pending_payment',
        ]);
        Certificate::create([
            'user_id' => $student1->id,
            'program_id' => $program1->id, // Certificado en progreso
            'issue_date' => '2026-05-10',
            'status' => 'in_progress',
        ]);

        // -----------------------------------------------------------------
        // 9. RECLUTAMIENTO Y EVALUACIÓN DOCENTE
        // -----------------------------------------------------------------
        $recruitment1 = TeacherRecruitment::create([
            'request_date' => '2025-09-01',
            'title' => 'Convocatoria Docente: Data Science',
            'description' => 'Buscamos expertos en Python, Pandas y Scikit-learn.',
            'required_profile' => 'Magister en CS, +5 años experiencia.',
            'status' => 'open',
        ]);
        $recruitment2 = TeacherRecruitment::create([
            'request_date' => '2025-08-01',
            'title' => 'Convocatoria Docente: Ciberseguridad',
            'description' => 'Experto en Ethical Hacking.',
            'status' => 'closed',
        ]);
        $recruitment3 = TeacherRecruitment::create([
            'request_date' => '2025-10-01',
            'title' => 'Convocatoria Docente: Cloud (AWS)',
            'description' => 'Experto en AWS.',
            'status' => 'open',
        ]);

        // Aplicaciones a convocatorias
        TeacherApplication::create([
            'recruitment_id' => $recruitment1->id,
            'user_id' => $student1->id, // Ana postula
            'cv' => 'path/to/cv/ana_lopez.pdf',
            'status' => 'under_review',
        ]);
        TeacherApplication::create([
            'recruitment_id' => $recruitment1->id,
            'user_id' => $teacher->id, // El profesor Garcia también postula
            'cv' => 'path/to/cv/profesor_garcia.pdf',
            'status' => 'interview',
        ]);
        TeacherApplication::create([
            'recruitment_id' => $recruitment2->id,
            'user_id' => $student2->id, // Carlos postula
            'cv' => 'path/to/cv/carlos_ruiz.pdf',
            'status' => 'rejected',
        ]);

        // Evaluaciones de estudiantes al docente (TeacherEvaluation)
        TeacherEvaluation::create([
            'evaluator_id' => $student1->id,
            'group_id' => $group1->id,
            'teacher_id' => $teacher->id,
            'answers' => ['q1_clarity' => 5, 'q2_materials' => 4, 'q3_punctuality' => 5],
            'score' => 4.67,
        ]);
        TeacherEvaluation::create([
            'evaluator_id' => $student2->id,
            'group_id' => $group1->id,
            'teacher_id' => $teacher->id,
            'answers' => ['q1_clarity' => 4, 'q2_materials' => 4, 'q3_punctuality' => 5],
            'score' => 4.33,
        ]);
        TeacherEvaluation::create([
            'evaluator_id' => $admin->id, // El admin también puede evaluar
            'group_id' => $group1->id,
            'teacher_id' => $teacher->id,
            'answers' => ['q1_clarity' => 5, 'q2_materials' => 5, 'q3_punctuality' => 5],
            'score' => 5.00,
        ]);
    }
}