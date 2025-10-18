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
        // Community forums
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_forum');
            $table->string('title', 100)->nullable();
            $table->string('description', 250)->nullable();
            $table->string('associated_program', 250)->nullable();
            $table->string('state', 14)->nullable();
            $table->timestamp('creation_date')->nullable();
        });

        DB::statement('ALTER TABLE forums ADD CONSTRAINT forums_state_check CHECK (state IN (\'Agendada\',\'Realizada\',\'Cancelada\'))');

        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_publicacion');
            $table->bigInteger('forum_id');
            $table->bigInteger('student_id');
            $table->string('content', 250)->nullable();
            $table->timestamp('creation_date')->nullable();
            $table->boolean('moderado')->nullable();

            $table->foreign('forum_id')->references('id')->on('forums');
            $table->foreign('student_id')->references('id')->on('students');
        });

        Schema::create('moderations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_moderation');
            $table->bigInteger('publicacion_id');
            $table->bigInteger('admin_id');
            $table->string('action', 50)->nullable();
            $table->string('comment', 200)->nullable();
            $table->timestamp('revision_date')->nullable();

            $table->foreign('publicacion_id')->references('id')->on('publications');
            $table->foreign('admin_id')->references('id')->on('users');
        });

        Schema::create('community_events', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_event');
            $table->string('titulo', 100)->nullable();
            $table->string('description', 200)->nullable();
            $table->timestamp('event_date')->nullable();
            $table->bigInteger('student_id');

            $table->foreign('student_id')->references('id')->on('students');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_message');
            $table->bigInteger('sender_id');
            $table->bigInteger('receiver_id');
            $table->string('content', 250)->nullable();
            $table->timestamp('sent_date')->nullable();
            $table->boolean('seen')->nullable();

            $table->foreign('sender_id')->references('id')->on('students');
            $table->foreign('receiver_id')->references('id')->on('students');
        });

        // Documentary processing
        Schema::create('type_documentary_processings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_type');
            $table->string('name_type', 80)->nullable();
            $table->string('description', 200)->nullable();
        });

        Schema::create('documentary_processings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_document');
            $table->bigInteger('student_id');
            $table->bigInteger('type_id');
            $table->string('description', 250)->nullable();
            $table->timestamp('creation_date')->nullable();
            $table->timestamp('update_date')->nullable();
            $table->string('current_state', 16)->nullable();
            $table->string('final_answer', 500)->nullable();

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('type_id')->references('id')->on('type_documentary_processings');
        });

        DB::statement('ALTER TABLE documentary_processings ADD CONSTRAINT documentary_processings_current_state_check CHECK (current_state IN (\'completed\',\'failed\',\'in_progress\'))');

        // Vocational guidance
        Schema::create('vocational_questionnaires', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_questionnaire');
            $table->string('title', 80)->nullable();
            $table->string('description', 200)->nullable();
            $table->timestamp('creation_date')->nullable();
            $table->boolean('activated')->nullable();
        });

        Schema::create('vocational_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_question');
            $table->bigInteger('id_questionnaire');
            $table->string('text_question', 250)->nullable();
            $table->string('type_response', 250)->nullable();

            $table->foreign('id_questionnaire')->references('id')->on('vocational_questionnaires');
        });

        Schema::create('vocational_responses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_response');
            $table->bigInteger('id_question');
            $table->string('text_response', 250)->nullable();
            $table->string('type_response', 250)->nullable();

            $table->foreign('id_question')->references('id')->on('vocational_questions');
        });

        Schema::create('vocational_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_resultado');
            $table->bigInteger('student_id');
            $table->bigInteger('questionnaire_id');
            $table->string('recommended_profile', 100)->nullable();
            $table->timestamp('completed_date')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('recommendation', 250)->nullable();

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('questionnaire_id')->references('id')->on('vocational_questionnaires');
        });

        // Tutoring and wellness
        Schema::create('tutorings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_tutorial');
            $table->bigInteger('estudent_id');
            $table->bigInteger('instructor_id');
            $table->timestamp('scheduled_date')->nullable();
            $table->string('type_tutorial', 16)->nullable();
            $table->string('state', 14)->nullable();

            $table->foreign('estudent_id')->references('id')->on('students');
            $table->foreign('instructor_id')->references('id')->on('instructors');
        });

        DB::statement('ALTER TABLE tutorings ADD CONSTRAINT tutorings_type_tutorial_check CHECK (type_tutorial IN (\'Académica\',\'Psicológica\'))');
        DB::statement('ALTER TABLE tutorings ADD CONSTRAINT tutorings_state_check CHECK (state IN (\'Agendada\',\'Realizada\',\'Cancelada\'))');

        Schema::create('tutoring_assistances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_assistance');
            $table->bigInteger('tutoring_id');
            $table->boolean('attended')->nullable();
            $table->string('observations', 500)->nullable();
            $table->timestamp('registration_date')->nullable();

            $table->foreign('tutoring_id')->references('id')->on('tutorings');
        });

        Schema::create('extracurricular_activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_activity');
            $table->string('activity_name', 100)->nullable();
            $table->string('activity_type', 16)->nullable();
            $table->string('description', 500)->nullable();
            $table->timestamp('event_date')->nullable();
        });

        DB::statement('ALTER TABLE extracurricular_activities ADD CONSTRAINT extracurricular_activities_activity_type_check CHECK (activity_type IN (\'Deportiva\',\'Cultural\',\'Integración\'))');

        Schema::create('extracurricular_enrollments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_enrollment');
            $table->bigInteger('activity_id');
            $table->bigInteger('student_id');
            $table->string('status', 15)->default('registered');
            $table->string('role', 16)->default('participant');
            $table->timestamp('attendance_at')->nullable();

            $table->foreign('activity_id')->references('id')->on('extracurricular_activities');
            $table->foreign('student_id')->references('id')->on('students');
        });

        DB::statement('ALTER TABLE extracurricular_enrollments ADD CONSTRAINT extracurricular_enrollments_status_check CHECK (status IN (\'registered\',\'confirmed\',\'attended\',\'cancelled\'))');
        DB::statement('ALTER TABLE extracurricular_enrollments ADD CONSTRAINT extracurricular_enrollments_role_check CHECK (role IN (\'participant\',\'organizer\',\'coach\'))');

        // Claims and suggestions
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_claim');
            $table->bigInteger('estudent_id');
            $table->string('type', 15)->nullable();
            $table->string('category', 50)->nullable();
            $table->string('priority', 10)->nullable();
            $table->string('description', 250)->nullable();
            $table->string('state', 14)->nullable();
            $table->timestamp('creation_date')->nullable();

            $table->foreign('estudent_id')->references('id')->on('students');
        });

        DB::statement('ALTER TABLE claims ADD CONSTRAINT claims_type_check CHECK (type IN (\'Reclamo\',\'Sugerencia\'))');
        DB::statement('ALTER TABLE claims ADD CONSTRAINT claims_priority_check CHECK (priority IN (\'Alta\',\'Media\',\'Baja\'))');
        DB::statement('ALTER TABLE claims ADD CONSTRAINT claims_state_check CHECK (state IN (\'Agendada\',\'Realizada\',\'Cancelada\'))');

        Schema::create('claim_assignments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_assignment');
            $table->bigInteger('claim_id');
            $table->bigInteger('responsible_id');
            $table->string('comments', 500)->nullable();
            $table->timestamp('event_date')->nullable();

            $table->foreign('claim_id')->references('id')->on('claims');
            $table->foreign('responsible_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_assignments');
        Schema::dropIfExists('claims');
        Schema::dropIfExists('extracurricular_enrollments');
        Schema::dropIfExists('extracurricular_activities');
        Schema::dropIfExists('tutoring_assistances');
        Schema::dropIfExists('tutorings');
        Schema::dropIfExists('vocational_results');
        Schema::dropIfExists('vocational_responses');
        Schema::dropIfExists('vocational_questions');
        Schema::dropIfExists('vocational_questionnaires');
        Schema::dropIfExists('documentary_processings');
        Schema::dropIfExists('type_documentary_processings');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('community_events');
        Schema::dropIfExists('moderations');
        Schema::dropIfExists('publications');
        Schema::dropIfExists('forums');
    }
};