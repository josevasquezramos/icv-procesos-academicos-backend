<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // --- Perfiles de Usuario ---
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unique();
            $table->string('professional_title', 200);
            $table->string('specialty', 100);
            $table->bigInteger('experience_years')->default(0);
            $table->text('biography')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE teacher_profiles ADD CONSTRAINT teacher_profiles_experience_years_check CHECK (experience_years >= 0)');

        // --- Estructura de Cursos y Programas ---
        Schema::create('program_courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('program_id');
            $table->bigInteger('course_id');
            $table->boolean('mandatory')->default(true);
            $table->timestamps();

            $table->unique(['program_id', 'course_id']);
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        Schema::create('course_previous_requirements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_id');
            $table->bigInteger('previous_course_id');
            $table->timestamps();

            $table->unique(['course_id', 'previous_course_id']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('previous_course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE course_previous_requirements ADD CONSTRAINT ck_course_previous_no_self CHECK (course_id <> previous_course_id)');

        // --- Grupos y Clases ---
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_id');
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE groups ADD CONSTRAINT groups_status_check CHECK (status IN (\'draft\',\'approved\',\'open\',\'in_progress\',\'completed\',\'cancelled\',\'suspended\'))');

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id');
            $table->string('class_name', 100);
            $table->string('meeting_url', 500)->nullable();
            $table->text('description')->nullable();
            $table->date('class_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('class_status', 12)->default('SCHEDULED');
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE classes ADD CONSTRAINT classes_class_status_check CHECK (class_status IN (\'SCHEDULED\',\'IN_PROGRESS\',\'FINISHED\',\'CANCELLED\'))');

        Schema::create('class_materials', function (Blueprint $table) {
            $table->id(); // id del material
            $table->unsignedBigInteger('class_id'); // id de la clase (llave foránea)
            $table->text('material_url'); // material_url (uso text para URLs largas)
            $table->string('type', 50); // type (ej: 'PDF', 'Video', 'Enlace')
            $table->timestamps(); // Opcional, pero recomendado (created_at, updated_at)

            // Definir la relación con la tabla 'classes'
            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->onDelete('cascade'); // Si se borra la clase, se borran sus materiales
        });

        // --- Participantes y Asistencia ---
        Schema::create('group_participants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id');
            $table->bigInteger('user_id');
            $table->string('role', 10); // 'student' o 'teacher'
            $table->string('enrollment_status', 12)->default('active');
            $table->timestamp('assignment_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->unique(['group_id', 'user_id']);
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE group_participants ADD CONSTRAINT group_participants_role_check CHECK (role IN (\'student\',\'teacher\'))');
        DB::statement('ALTER TABLE group_participants ADD CONSTRAINT group_participants_enrollment_status_check CHECK (enrollment_status IN (\'pending\',\'approved\',\'rejected\',\'active\',\'withdrawn\',\'finished\'))');

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_participant_id');
            $table->bigInteger('class_id');
            $table->boolean('attended')->default(false);
            $table->string('observations', 200)->nullable();
            $table->timestamps();

            $table->unique(['group_participant_id', 'class_id']);
            $table->foreign('group_participant_id')->references('id')->on('group_participants')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });

        // --- Sistema de Evaluaciones y Calificaciones (Súper Simplificado) ---

        /**
         * Define la "columna" en el libro de notas (ej. "Examen Parcial", "Proyecto Final").
         * El profesor puede poner un link a un Google Form o dar instrucciones en la descripción.
         */
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id');
            $table->string('title', 200);
            $table->text('description')->nullable(); // Instrucciones
            $table->string('external_url', 500)->nullable(); // Link al Google Form
            $table->string('evaluation_type', 20); // 'Exam', 'Project', 'Assignment'
            $table->timestamp('due_date')->nullable(); // Fecha límite sugerida
            $table->decimal('weight', 5, 2)->default(1.00); // Peso para el promedio
            $table->bigInteger('teacher_creator_id')->nullable();
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('teacher_creator_id')->references('id')->on('users')->onDelete('set null');
        });

        DB::statement('ALTER TABLE evaluations ADD CONSTRAINT evaluations_evaluation_type_check CHECK (evaluation_type IN (\'Exam\',\'Quiz\',\'Project\',\'Assignment\',\'Final\'))');
        DB::statement('ALTER TABLE evaluations ADD CONSTRAINT evaluations_weight_check CHECK (weight > 0)');

        /**
         * El "Libro de Calificaciones".
         * Aquí el profesor registra la nota del estudiante para esa evaluación.
         */
        Schema::create('grade_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('evaluation_id');
            $table->bigInteger('user_id'); // ID del estudiante
            $table->decimal('obtained_grade', 5, 2); // La nota que pone el profesor
            $table->text('feedback')->nullable(); // Feedback del profesor
            $table->timestamp('record_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->unique(['evaluation_id', 'user_id']); // Un estudiante solo puede tener una nota por evaluación
            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('final_grades', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('group_id');
            $table->decimal('final_grade', 5, 2);
            $table->string('program_status', 20); // 'Passed', 'Failed', 'In_progress'
            $table->timestamp('calculation_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->unique(['user_id', 'group_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE final_grades ADD CONSTRAINT final_grades_program_status_check CHECK (program_status IN (\'Passed\',\'Failed\',\'Withdrawn\',\'In_progress\'))');

        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('user_id');
            $table->bigInteger('group_id');
            $table->date('issue_date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });

        Schema::create('academic_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('base_grade', 5, 2)->default(20.00);
            $table->decimal('min_passing_grade', 5, 2)->default(11.00);
            $table->timestamps();
        });

        Schema::create('teacher_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('status', 20)->default('open');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE teacher_offers ADD CONSTRAINT teacher_offers_status_check CHECK (status IN ('open', 'closed'))");

        Schema::create('teacher_applications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id'); // El usuario (postulante)
            $table->bigInteger('teacher_offer_id'); // La oferta a la que postula

            // --- CAMPOS REQUERIDOS (Réplica de teacher_profiles) ---
            // Se capturan aquí para tener una "foto" del postulante en ese momento
            $table->string('professional_title', 200);
            $table->string('specialty', 100);
            $table->bigInteger('experience_years')->default(0);
            $table->text('biography')->nullable();
            // --- Fin de campos de perfil ---

            // --- CAMPOS ADICIONALES (propios de la postulación) ---
            $table->string('cv_path', 500); // Ruta al archivo del CV (ej. en S3 o storage)
            $table->text('cover_letter')->nullable(); // Carta de presentación (opcional)
            $table->string('status', 20)->default('pending'); // 'pending', 'accepted', 'rejected'
            $table->text('admin_feedback')->nullable(); // Notas internas del revisor
            $table->timestamps(); // application_date es created_at

            // --- Llaves foráneas y constraints ---
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('teacher_offer_id')->references('id')->on('teacher_offers')->onDelete('cascade');

            // Un usuario solo puede postular una vez a la misma oferta
            $table->unique(['user_id', 'teacher_offer_id'], 'user_offer_unique_application');
        });

        DB::statement("ALTER TABLE teacher_applications ADD CONSTRAINT teacher_applications_status_check CHECK (status IN ('pending', 'accepted', 'rejected'))");
        DB::statement("ALTER TABLE teacher_applications ADD CONSTRAINT teacher_applications_experience_years_check CHECK (experience_years >= 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_applications');
        Schema::dropIfExists('teacher_offers');
        Schema::dropIfExists('academic_settings');
        Schema::dropIfExists('credentials');
        Schema::dropIfExists('final_grades');
        Schema::dropIfExists('grade_records');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('group_participants');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('course_previous_requirements');
        Schema::dropIfExists('program_courses');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('teacher_profiles');
    }
};