<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateAllSeeder extends Seeder
{
    /**
     * Vacía todas las tablas de la aplicación en el orden correcto.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('--- INICIANDO LIMPIEZA TOTAL ---');
        $this->command->warn('Deshabilitando llaves foráneas...');
        Schema::disableForeignKeyConstraints();

        $this->command->info('Vaciando tablas (TRUNCATE)...');

        DB::table('documents')->truncate();
        DB::table('documents')->truncate();
        DB::table('document_versions')->truncate();
        // DB::table('document_history')->truncate();
        DB::table('payments')->truncate();
        DB::table('invoices')->truncate();
        DB::table('academic_periods')->truncate();
        DB::table('payment_methods')->truncate();
        DB::table('students')->truncate();
        DB::table('enrollments')->truncate();

        // --- Tablas Nietas (Máxima dependencia) ---
        DB::table('class_materials')->truncate();
        DB::table('attendances')->truncate();
        DB::table('grade_records')->truncate();
        DB::table('survey_questions')->truncate();
        DB::table('vocational_response_courses')->truncate();
        DB::table('student_wellbeing_tutoring_assistances')->truncate();

        // --- Tablas Hijas (Dependencia media) ---
        DB::table('credentials')->truncate();
        DB::table('final_grades')->truncate();
        DB::table('evaluations')->truncate();
        DB::table('classes')->truncate();
        DB::table('group_participants')->truncate();
        DB::table('teacher_profiles')->truncate();
        DB::table('employment_profiles')->truncate();
        DB::table('program_courses')->truncate();
        DB::table('course_previous_requirements')->truncate();
        DB::table('graduates')->truncate(); // <-- de ValentinoSeeder
        
        // --- Tablas Hijas (Módulo Randal) ---
        DB::table('vocational_responses')->truncate();
        DB::table('vocational_questions')->truncate();
        DB::table('attention_students_requests')->truncate();
        DB::table('student_wellbeing_tutorings')->truncate();
        DB::table('student_wellbeing_extracurricular_activities')->truncate();
        DB::table('students')->truncate();
        DB::table('employees')->truncate();
        DB::table('instructors')->truncate(); // <-- Usada por Randal y Valentino

        // --- Tablas Padre (Dependencia baja) ---
        DB::table('academic_settings')->truncate();
        DB::table('groups')->truncate();
        DB::table('courses')->truncate();
        DB::table('programs')->truncate();
        DB::table('surveys')->truncate();
        DB::table('satisfaction_survey_categories')->truncate(); // <-- de ValentinoSeeder
        DB::table('positions')->truncate(); // <-- de RandalSeeder
        DB::table('departments')->truncate(); // <-- de RandalSeeder
        DB::table('vocational_questionnaires')->truncate(); // <-- de RandalSeeder
        DB::table('attention_students_Request_Types')->truncate(); // <-- de RandalSeeder

        DB::table('users')->truncate();
        DB::table('groups')->truncate();
        DB::table('final_grades')->truncate();
        DB::table('accounts')->truncate();
        DB::table('invoices')->truncate();

        DB::table('payments')->truncate();
        DB::table('financial_transactions')->truncate();
        DB::table('evaluations')->truncate();
        DB::table('grade_records')->truncate();
        DB::table('instructors')->truncate();

        DB::table('group_participants')->truncate();
        DB::table('courses')->truncate();
        DB::table('security_alerts')->truncate();
        DB::table('employees')->truncate();
        DB::table('incidents')->truncate();

        DB::table('enrollments')->truncate();
        DB::table('revenue_sources')->truncate();
        DB::table('payment_methods')->truncate();
        DB::table('payments')->truncate();
        DB::table('departments')->truncate();

        DB::table('positions')->truncate();
        DB::table('blocked_ips')->truncate();
        DB::table('security_logs')->truncate();
        DB::table('companies')->truncate();
        DB::table('students')->truncate();

        DB::table('subjects')->truncate();
        DB::table('tickets')->truncate();
        DB::table('academic_periods')->truncate();

        // --- Tabla Raíz (Máxima autoridad) ---
        DB::table('users')->truncate(); 
        
        $this->command->warn('Reactivando llaves foráneas...');
        Schema::enableForeignKeyConstraints();
        $this->command->info('--- LIMPIEZA TOTAL FINALIZADA ---');
    }
}