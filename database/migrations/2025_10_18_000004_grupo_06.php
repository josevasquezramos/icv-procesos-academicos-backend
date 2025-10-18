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
        // Employability surveys
        Schema::create('employability_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_question');
            $table->string('question_text', 255)->nullable();
            $table->string('question_type', 13);
        });

        DB::statement('ALTER TABLE employability_questions ADD CONSTRAINT employability_questions_question_type_check CHECK (question_type IN (\'text\', \'number\', \'option\', \'date\'))');

        Schema::create('option_job_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_option');
            $table->bigInteger('id_question');
            $table->string('option_text', 50)->nullable();

            $table->foreign('id_question')->references('id')->on('employability_questions');
        });

        Schema::create('employability_surveys', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_survey');
            $table->bigInteger('id_graduates');
            $table->timestamp('registration_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('id_graduates')->references('id')->on('graduates');
        });

        Schema::create('response_graduates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_answer');
            $table->bigInteger('id_survey');
            $table->bigInteger('id_question');
            $table->bigInteger('id_option')->nullable();
            $table->string('answer_text', 255)->nullable();
            $table->float('answer_number')->nullable();
            $table->timestamp('fanswer_date')->nullable();

            $table->foreign('id_survey')->references('id')->on('employability_surveys');
            $table->foreign('id_question')->references('id')->on('employability_questions');
            $table->foreign('id_option')->references('id')->on('option_job_questions');
        });

        Schema::create('professional_social_impacts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_impact');
            $table->bigInteger('id_graduates');
            $table->string('description', 255)->nullable();
            $table->timestamp('registration_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('id_graduates')->references('id')->on('graduates');
        });

        // Audit findings and reports  
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('audit_id');
            $table->string('description', 255)->nullable();
            $table->string('classification', 255)->nullable();
            $table->string('evidence', 255)->nullable();
            $table->string('severity', 20)->default('medium');
            $table->timestamp('discovery_date')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('audit_id')->references('id')->on('audits');
        });

        DB::statement('ALTER TABLE findings ADD CONSTRAINT findings_severity_check CHECK (severity IN (\'low\',\'medium\',\'high\'))');

        Schema::create('audit_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('audit_id');
            $table->bigInteger('version_document_id')->nullable();
            $table->string('resume', 255)->nullable();
            $table->string('recommendations', 255)->nullable();
            $table->string('indicators', 255)->nullable();
            $table->date('generation_date')->nullable();
            $table->timestamps();

            $table->foreign('audit_id')->references('id')->on('audits');
            $table->foreign('version_document_id')->references('id')->on('document_versions');
        });

        Schema::create('corrective_actions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('finding_id');
            $table->bigInteger('user_id');
            $table->string('description', 255)->nullable();
            $table->string('status', 20)->default('pending');
            $table->date('engagement_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->timestamps();

            $table->foreign('finding_id')->references('id')->on('findings');
            $table->foreign('user_id')->references('id')->on('users');
        });

        DB::statement('ALTER TABLE corrective_actions ADD CONSTRAINT corrective_actions_status_check CHECK (status IN (\'pending\',\'in_progress\',\'completed\',\'cancelled\'))');

        Schema::create('document_audits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('audit_id');
            $table->bigInteger('version_document_id');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('audit_id')->references('id')->on('audits');
            $table->foreign('version_document_id')->references('id')->on('document_versions');
        });

        // Student-course relationships
        Schema::create('student_courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_student_course');
            $table->bigInteger('id_student');
            $table->bigInteger('id_curse');
            $table->timestamp('assigned_date')->nullable();

            $table->foreign('id_student')->references('id')->on('students');
            $table->foreign('id_curse')->references('id')->on('courses');
        });

        // Evaluation criteria and instructor evaluations
        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_evaluation_criteria');
            $table->string('criterion_name', 255);
            $table->timestamp('category')->nullable();
            $table->string('response_type', 13);
            $table->float('percentage_weight')->nullable();
            $table->string('state', 255)->nullable();
        });

        DB::statement('ALTER TABLE evaluation_criteria ADD CONSTRAINT evaluation_criteria_response_type_check CHECK (response_type IN (\'numeric\', \'text\', \'option\'))');

        Schema::create('option_criteria', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_option_criteria');
            $table->bigInteger('id_evaluation_criteria');
            $table->string('option_text', 255)->nullable();

            $table->foreign('id_evaluation_criteria')->references('id')->on('evaluation_criteria');
        });

        Schema::create('instructor_evaluations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('instructor_evaluation_id');
            $table->bigInteger('student_id')->nullable();
            $table->bigInteger('instructor_id')->nullable();
            $table->bigInteger('course_offering_id')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->string('evaluation_period', 255)->nullable();
            $table->string('evaluation_status', 255)->nullable();
            $table->timestamp('evaluation_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('instructor_id')->references('id')->on('instructors');
            $table->foreign('course_offering_id')->references('id')->on('course_offerings');
        });

        Schema::create('detail_evaluation_criteria', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_detail_evaluation_criteria');
            $table->bigInteger('id_evaluation_criteria');
            $table->bigInteger('id_option_criteria')->nullable();
            $table->bigInteger('id_instructor_evaluation');
            $table->float('numeric_response')->nullable();
            $table->string('response_text', 255)->nullable();

            $table->foreign('id_evaluation_criteria')->references('id')->on('evaluation_criteria');
            $table->foreign('id_option_criteria')->references('id')->on('option_criteria');
            $table->foreign('id_instructor_evaluation')->references('id')->on('instructor_evaluations');
        });

        Schema::create('evaluation_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_evaluation_report');
            $table->bigInteger('id_instructor');
            $table->bigInteger('id_instructor_evaluation');
            $table->bigInteger('id_curse');
            $table->float('overall_average')->nullable();
            $table->float('evaluation_period')->nullable();
            $table->float('total_evaluations')->nullable();
            $table->timestamp('generation_date')->nullable();

            $table->foreign('id_instructor')->references('id')->on('instructors');
            $table->foreign('id_instructor_evaluation')->references('id')->on('instructor_evaluations');
            $table->foreign('id_curse')->references('id')->on('courses');
        });

        // Satisfaction surveys
        Schema::create('satisfaction_survey_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_category');
            $table->string('category_name', 255);
            $table->string('description', 255)->nullable();
        });

        Schema::create('satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_satisfaction_survey');
            $table->bigInteger('id_category');
            $table->string('qualification', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('state', 255)->nullable();
            $table->timestamp('creation_date')->nullable();

            $table->foreign('id_category')->references('id')->on('satisfaction_survey_categories');
        });

        Schema::create('satisfaction_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_satisfaction_question');
            $table->bigInteger('id_survey');
            $table->string('question_text', 255);
            $table->string('type', 255)->nullable();

            $table->foreign('id_survey')->references('id')->on('satisfaction_surveys');
        });

        Schema::create('satisfaction_options', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_satisfaction_option');
            $table->bigInteger('id_question');
            $table->string('option_text', 255)->nullable();

            $table->foreign('id_question')->references('id')->on('satisfaction_questions');
        });

        Schema::create('satisfaction_responses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_satisfaction_response');
            $table->bigInteger('id_student');
            $table->bigInteger('id_question');
            $table->bigInteger('id_opcion')->nullable();
            $table->string('response_text', 255)->nullable();
            $table->timestamp('response_date')->nullable();

            $table->foreign('id_question')->references('id')->on('satisfaction_questions');
            $table->foreign('id_opcion')->references('id')->on('satisfaction_options');
            $table->foreign('id_student')->references('id')->on('students');
        });

        Schema::create('surveys_assigned', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_survey_assigned');
            $table->bigInteger('id_survey');
            $table->bigInteger('id_student');
            $table->string('state', 255)->nullable();
            $table->timestamp('creation_date')->nullable();

            $table->foreign('id_survey')->references('id')->on('satisfaction_surveys');
            $table->foreign('id_student')->references('id')->on('students');
        });

        Schema::create('survey_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_report');
            $table->bigInteger('id_survey');
            $table->string('report_type', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->timestamp('creation_date')->nullable();

            $table->foreign('id_survey')->references('id')->on('satisfaction_surveys');
        });

        // Version document management (extends existing document management)
        Schema::create('version_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('document_id')->nullable();
            $table->bigInteger('file_id')->nullable();
            $table->string('num_version', 255)->nullable();
            $table->string('observations', 255)->nullable();
            $table->bigInteger('user_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('version_documents');
        Schema::dropIfExists('survey_reports');
        Schema::dropIfExists('surveys_assigned');
        Schema::dropIfExists('satisfaction_responses');
        Schema::dropIfExists('satisfaction_options');
        Schema::dropIfExists('satisfaction_questions');
        Schema::dropIfExists('satisfaction_surveys');
        Schema::dropIfExists('satisfaction_survey_categories');
        Schema::dropIfExists('evaluation_reports');
        Schema::dropIfExists('detail_evaluation_criteria');
        Schema::dropIfExists('instructor_evaluations');
        Schema::dropIfExists('option_criteria');
        Schema::dropIfExists('evaluation_criteria');
        Schema::dropIfExists('student_courses');
        Schema::dropIfExists('document_audits');
        Schema::dropIfExists('corrective_actions');
        Schema::dropIfExists('audit_reports');
        Schema::dropIfExists('findings');
        Schema::dropIfExists('professional_social_impacts');
        Schema::dropIfExists('response_graduates');
        Schema::dropIfExists('employability_surveys');
        Schema::dropIfExists('option_job_questions');
        Schema::dropIfExists('employability_questions');
    }
};