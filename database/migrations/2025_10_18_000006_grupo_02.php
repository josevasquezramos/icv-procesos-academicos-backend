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
        // User profiles
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unique();
            $table->string('professional_title', 200);
            $table->string('specialty', 100);
            $table->bigInteger('experience_years')->default(0);
            $table->text('biography')->nullable();
            $table->string('linkedin_link', 255)->nullable();
            $table->string('cover_photo', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE teacher_profiles ADD CONSTRAINT teacher_profiles_experience_years_check CHECK (experience_years >= 0)');

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unique();
            $table->string('career_interest', 100)->nullable();
            $table->string('work_situation', 20)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE student_profiles ADD CONSTRAINT student_profiles_work_situation_check CHECK (work_situation IN (\'employed\',\'unemployed\',\'student\',\'other\'))');

        // Program and course relationships
        Schema::create('program_courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('program_id');
            $table->bigInteger('course_id');
            $table->boolean('mandatory')->default(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->unique(['program_id', 'course_id']);
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        Schema::create('course_previous_requirements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_id');
            $table->bigInteger('previous_course_id');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->unique(['course_id', 'previous_course_id']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('previous_course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE course_previous_requirements ADD CONSTRAINT ck_course_previous_no_self CHECK (course_id <> previous_course_id)');

        // Groups and classes
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_id');
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->date('start_date');
            $table->date('end_date');
            $table->bigInteger('minimum_enrolled')->default(1);
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE groups ADD CONSTRAINT groups_minimum_enrolled_check CHECK (minimum_enrolled >= 1)');
        DB::statement('ALTER TABLE groups ADD CONSTRAINT groups_status_check CHECK (status IN (\'draft\',\'approved\',\'open\',\'in_progress\',\'completed\',\'cancelled\',\'suspended\'))');

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id');
            $table->string('class_name', 100);
            $table->date('class_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('platform', 50)->default('Zoom');
            $table->string('meeting_url', 500)->nullable();
            $table->string('external_meeting_id', 100)->nullable();
            $table->string('meeting_password', 100)->nullable();
            $table->boolean('allow_recording')->default(true);
            $table->string('recording_url', 500)->nullable();
            $table->bigInteger('max_participants')->default(100);
            $table->string('class_status', 12)->default('SCHEDULED');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE classes ADD CONSTRAINT classes_max_participants_check CHECK (max_participants > 0)');
        DB::statement('ALTER TABLE classes ADD CONSTRAINT classes_class_status_check CHECK (class_status IN (\'SCHEDULED\',\'IN_PROGRESS\',\'FINISHED\',\'CANCELLED\'))');

        // Group participants and attendance
        Schema::create('group_participants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id');
            $table->bigInteger('user_id');
            $table->string('role', 10);
            $table->string('teacher_function', 20)->nullable();
            $table->string('enrollment_status', 12)->default('active');
            $table->timestamp('assignment_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->jsonb('schedule')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->unique(['group_id', 'user_id']);
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE group_participants ADD CONSTRAINT group_participants_role_check CHECK (role IN (\'student\',\'teacher\'))');
        DB::statement('ALTER TABLE group_participants ADD CONSTRAINT group_participants_teacher_function_check CHECK (teacher_function IN (\'titular\',\'auxiliary\',\'coordinator\'))');
        DB::statement('ALTER TABLE group_participants ADD CONSTRAINT group_participants_enrollment_status_check CHECK (enrollment_status IN (\'pending\',\'approved\',\'rejected\',\'active\',\'withdrawn\',\'finished\'))');

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_participant_id');
            $table->bigInteger('class_id');
            $table->string('attended', 3)->default('NO');
            $table->timestamp('entry_time')->nullable();
            $table->timestamp('exit_time')->nullable();
            $table->bigInteger('connected_minutes')->default(0);
            $table->string('connection_ip', 45)->nullable();
            $table->string('device', 100)->nullable();
            $table->string('approximate_location', 100)->nullable();
            $table->string('connection_quality', 12)->nullable();
            $table->string('observations', 200)->nullable();
            $table->boolean('cloud_synchronized')->default(false);
            $table->timestamp('record_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->unique(['group_participant_id', 'class_id']);
            $table->foreign('group_participant_id')->references('id')->on('group_participants')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE attendances ADD CONSTRAINT attendances_attended_check CHECK (attended IN (\'YES\',\'NO\'))');
        DB::statement('ALTER TABLE attendances ADD CONSTRAINT attendances_connected_minutes_check CHECK (connected_minutes >= 0)');
        DB::statement('ALTER TABLE attendances ADD CONSTRAINT attendances_connection_quality_check CHECK (connection_quality IN (\'EXCELLENT\',\'GOOD\',\'FAIR\',\'POOR\'))');

        // Evaluations and grading system
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id');
            $table->string('title', 200);
            $table->string('evaluation_type', 20);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->bigInteger('duration_minutes');
            $table->decimal('total_score', 5, 2);
            $table->string('status', 20)->default('Active');
            $table->bigInteger('teacher_creator_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('teacher_creator_id')->references('id')->on('users')->onDelete('set null');
        });

        DB::statement('ALTER TABLE evaluations ADD CONSTRAINT evaluations_evaluation_type_check CHECK (evaluation_type IN (\'Exam\',\'Quiz\',\'Project\',\'Assignment\',\'Final\'))');
        DB::statement('ALTER TABLE evaluations ADD CONSTRAINT evaluations_duration_minutes_check CHECK (duration_minutes > 0)');
        DB::statement('ALTER TABLE evaluations ADD CONSTRAINT evaluations_total_score_check CHECK (total_score > 0)');
        DB::statement('ALTER TABLE evaluations ADD CONSTRAINT evaluations_status_check CHECK (status IN (\'Active\',\'Inactive\',\'Finished\'))');

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('evaluation_id');
            $table->text('statement');
            $table->string('question_type', 20);
            $table->jsonb('answer_options')->nullable();
            $table->jsonb('correct_answer')->nullable();
            $table->decimal('score', 5, 2);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE questions ADD CONSTRAINT questions_question_type_check CHECK (question_type IN (\'Multiple\',\'Essay\',\'True_False\'))');
        DB::statement('ALTER TABLE questions ADD CONSTRAINT questions_score_check CHECK (score > 0)');

        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('evaluation_id');
            $table->bigInteger('user_id');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->jsonb('answers');
            $table->decimal('obtained_score', 5, 2)->nullable();
            $table->string('status', 20)->default('In_progress');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE attempts ADD CONSTRAINT attempts_status_check CHECK (status IN (\'In_progress\',\'Completed\',\'Abandoned\'))');

        Schema::create('gradings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('attempt_id')->unique();
            $table->bigInteger('teacher_grader_id')->nullable();
            $table->jsonb('grading_detail');
            $table->text('feedback')->nullable();
            $table->timestamp('grading_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('attempt_id')->references('id')->on('attempts')->onDelete('cascade');
            $table->foreign('teacher_grader_id')->references('id')->on('users')->onDelete('set null');
        });

        // Grade management
        Schema::create('grade_configurations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id')->unique();
            $table->string('grading_system', 50);
            $table->decimal('max_grade', 5, 2);
            $table->decimal('passing_grade', 5, 2);
            $table->decimal('evaluation_weight', 5, 2)->default(100.00);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });

        Schema::create('grade_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('evaluation_id');
            $table->bigInteger('group_id');
            $table->bigInteger('configuration_id');
            $table->decimal('obtained_grade', 5, 2);
            $table->decimal('grade_weight', 5, 2);
            $table->string('grade_type', 20);
            $table->string('status', 20)->default('Recorded');
            $table->timestamp('record_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('configuration_id')->references('id')->on('grade_configurations')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE grade_records ADD CONSTRAINT grade_records_grade_type_check CHECK (grade_type IN (\'Partial\',\'Final\',\'Makeup\'))');
        DB::statement('ALTER TABLE grade_records ADD CONSTRAINT grade_records_status_check CHECK (status IN (\'Recorded\',\'Validated\',\'Published\',\'Observed\'))');

        Schema::create('final_grades', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('group_id');
            $table->bigInteger('configuration_id');
            $table->decimal('final_grade', 5, 2);
            $table->decimal('partial_average', 5, 2)->nullable();
            $table->string('program_status', 20);
            $table->boolean('certification_obtained')->default(false);
            $table->timestamp('calculation_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->unique(['user_id', 'group_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('configuration_id')->references('id')->on('grade_configurations')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE final_grades ADD CONSTRAINT final_grades_program_status_check CHECK (program_status IN (\'Passed\',\'Failed\',\'Withdrawn\',\'In_progress\'))');

        Schema::create('grade_changes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('record_id');
            $table->decimal('previous_grade', 5, 2);
            $table->decimal('new_grade', 5, 2);
            $table->text('reason');
            $table->bigInteger('user_id');
            $table->timestamp('change_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('record_id')->references('id')->on('grade_records')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });

        // Certificates and diplomas
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('program_id');
            $table->date('issue_date');
            $table->string('status', 20)->nullable();
            $table->string('verification_code', 255)->unique()->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        Schema::create('diplomas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('program_id');
            $table->date('issue_date');
            $table->string('status', 20)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // Graduate surveys
        Schema::create('graduate_surveys', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('graduate_id');
            $table->date('date');
            $table->string('employability', 255)->nullable();
            $table->string('satisfaction', 50)->nullable();
            $table->text('curriculum_feedback')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('graduate_id')->references('id')->on('graduates')->onDelete('cascade');
        });

        // Teacher recruitment and evaluations
        Schema::create('teacher_recruitments', function (Blueprint $table) {
            $table->id();
            $table->date('request_date');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->text('required_profile')->nullable();
            $table->string('status', 12)->default('open');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE teacher_recruitments ADD CONSTRAINT teacher_recruitments_status_check CHECK (status IN (\'open\',\'closed\',\'suspended\'))');

        Schema::create('teacher_applications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('recruitment_id');
            $table->bigInteger('user_id');
            $table->string('cv', 255);
            $table->string('status', 15)->default('received');
            $table->timestamps();

            $table->unique(['recruitment_id', 'user_id']);
            $table->foreign('recruitment_id')->references('id')->on('teacher_recruitments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE teacher_applications ADD CONSTRAINT teacher_applications_status_check CHECK (status IN (\'received\',\'under_review\',\'interview\',\'selected\',\'rejected\'))');

        Schema::create('teacher_evaluations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('evaluator_id');
            $table->bigInteger('group_id');
            $table->bigInteger('teacher_id');
            $table->jsonb('answers');
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('evaluator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_evaluations');
        Schema::dropIfExists('teacher_applications');
        Schema::dropIfExists('teacher_recruitments');
        Schema::dropIfExists('graduate_surveys');
        Schema::dropIfExists('diplomas');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('grade_changes');
        Schema::dropIfExists('final_grades');
        Schema::dropIfExists('grade_records');
        Schema::dropIfExists('grade_configurations');
        Schema::dropIfExists('gradings');
        Schema::dropIfExists('attempts');
        Schema::dropIfExists('questions');
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