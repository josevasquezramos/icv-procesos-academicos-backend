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
        // Teams management
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->jsonb('members')->nullable();
            $table->timestamps();
        });

        // Strategic planning
        Schema::create('strategic_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 50)->default('draft');
            $table->bigInteger('created_by_user_id')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('created_by_user_id')->references('id')->on('users');
        });

        Schema::create('strategic_objectives', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->decimal('goal_value', 12, 2)->nullable();
            $table->bigInteger('responsible_user_id')->nullable();
            $table->bigInteger('weight')->default(0);
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('strategic_plans');
            $table->foreign('responsible_user_id')->references('id')->on('users');
        });

        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('objective_id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('target_value', 12, 2)->nullable();
            $table->string('unit', 50)->nullable();
            $table->string('frequency', 50)->nullable();
            $table->timestamps();

            $table->foreign('objective_id')->references('id')->on('strategic_objectives');
        });

        Schema::create('kpi_measurements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('kpi_id');
            $table->date('measured_at');
            $table->decimal('value', 12, 4)->nullable();
            $table->string('source', 255)->nullable();
            $table->bigInteger('recorded_by_user_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('kpi_id')->references('id')->on('kpis');
            $table->foreign('recorded_by_user_id')->references('id')->on('users');
        });

        Schema::create('dashboards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->bigInteger('owner_user_id')->nullable();
            $table->jsonb('widgets')->nullable();
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('strategic_plans');
            $table->foreign('owner_user_id')->references('id')->on('users');
        });

        Schema::create('initiatives', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->text('summary')->nullable();
            $table->bigInteger('responsible_team_id')->nullable();
            $table->bigInteger('responsible_user_id')->nullable();
            $table->string('status', 50)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('estimated_impact', 255)->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('plan_id')->references('id')->on('strategic_plans');
            $table->foreign('responsible_team_id')->references('id')->on('teams');
            $table->foreign('responsible_user_id')->references('id')->on('users');
        });

        Schema::create('initiative_evaluations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('initiative_id');
            $table->bigInteger('evaluator_user_id')->nullable();
            $table->date('evaluation_date')->nullable();
            $table->text('summary')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->bigInteger('report_document_version_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('initiative_id')->references('id')->on('initiatives');
            $table->foreign('evaluator_user_id')->references('id')->on('users');
        });

        // Partners and agreements
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('type', 100)->nullable();
            $table->jsonb('contact')->nullable();
            $table->string('legal_representative', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('partner_id');
            $table->string('title', 255)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 50)->nullable();
            $table->date('renewal_date')->nullable();
            $table->boolean('electronic_signature')->default(false);
            $table->bigInteger('created_by_user_id')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('created_by_user_id')->references('id')->on('users');
        });

        // Communication channels
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('channel_type', 100)->nullable();
            $table->bigInteger('related_plan_id')->nullable();
            $table->bigInteger('created_by_user_id')->nullable();
            $table->jsonb('members')->nullable();
            $table->timestamps();

            $table->foreign('related_plan_id')->references('id')->on('strategic_plans');
            $table->foreign('created_by_user_id')->references('id')->on('users');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('channel_id');
            $table->bigInteger('user_id');
            $table->text('content')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->boolean('pinned')->default(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('channel_id')->references('id')->on('channels');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('parent_id')->references('id')->on('messages');
        });

        // Task management
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('channel_id')->nullable();
            $table->bigInteger('initiative_id')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('priority', 50)->nullable();
            $table->date('due_date')->nullable();
            $table->bigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->foreign('channel_id')->references('id')->on('channels');
            $table->foreign('initiative_id')->references('id')->on('initiatives');
            $table->foreign('created_by_user_id')->references('id')->on('users');
        });

        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('task_id');
            $table->bigInteger('user_id');
            $table->bigInteger('assigned_by_user_id')->nullable();
            $table->timestamp('assigned_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('task_id')->references('id')->on('tasks');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('assigned_by_user_id')->references('id')->on('users');
        });

        // Document management (extends existing documents table)
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('document_id');
            $table->bigInteger('version_number')->default(1);
            $table->string('file_name', 255);
            $table->string('storage_path', 1024);
            $table->string('mime_type', 100)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->bigInteger('uploaded_by_user_id')->nullable();
            $table->timestamp('uploaded_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('checksum', 128)->nullable();
            $table->text('notes')->nullable();
            $table->string('linked_type', 100)->nullable();
            $table->bigInteger('linked_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('uploaded_by_user_id')->references('id')->on('users');
        });

        Schema::create('evidences', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('initiative_id')->nullable();
            $table->bigInteger('document_version_id');
            $table->text('description')->nullable();
            $table->bigInteger('kpi_measurement_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('document_version_id')->references('id')->on('document_versions');
            $table->foreign('kpi_measurement_id')->references('id')->on('kpi_measurements');
            $table->foreign('initiative_id')->references('id')->on('initiatives');
        });

        // Surveys
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('target_type', 100)->nullable();
            $table->bigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by_user_id')->references('id')->on('users');
        });

        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('survey_id');
            $table->text('question_text')->nullable();
            $table->string('question_type', 50)->nullable();

            $table->foreign('survey_id')->references('id')->on('surveys');
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('survey_id');
            $table->bigInteger('respondent_user_id')->nullable();
            $table->jsonb('answers')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreign('survey_id')->references('id')->on('surveys');
            $table->foreign('respondent_user_id')->references('id')->on('users');
        });

        // Audits and accreditations
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('area', 255)->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('summary_results')->nullable();
            $table->bigInteger('report_document_version_id')->nullable();
            $table->string('type', 13)->nullable();
            $table->string('state', 16)->nullable();
            $table->string('objective', 255)->nullable();
            $table->string('range', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('report_document_version_id')->references('id')->on('document_versions');
        });

        DB::statement('ALTER TABLE audits ADD CONSTRAINT audits_type_check CHECK (type IN (\'internal\',\'external\'))');
        DB::statement('ALTER TABLE audits ADD CONSTRAINT audits_state_check CHECK (state IN (\'planned\',\'in_progress\',\'completed\',\'cancelled\'))');

        Schema::create('accreditations', function (Blueprint $table) {
            $table->id();
            $table->string('entity', 255)->nullable();
            $table->date('accreditation_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('result', 255)->nullable();
            $table->bigInteger('document_version_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('document_version_id')->references('id')->on('document_versions');
        });

        // Activity and notifications
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('action_type', 100)->nullable();
            $table->string('target_type', 100)->nullable();
            $table->bigInteger('target_id')->nullable();
            $table->jsonb('old_data')->nullable();
            $table->jsonb('new_data')->nullable();
            $table->string('ip', 50)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('title', 255)->nullable();
            $table->text('message')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('related_type', 100)->nullable();
            $table->bigInteger('related_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('accreditations');
        Schema::dropIfExists('audits');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
        Schema::dropIfExists('evidences');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('task_assignments');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('channels');
        Schema::dropIfExists('agreements');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('initiative_evaluations');
        Schema::dropIfExists('initiatives');
        Schema::dropIfExists('dashboards');
        Schema::dropIfExists('kpi_measurements');
        Schema::dropIfExists('kpis');
        Schema::dropIfExists('strategic_objectives');
        Schema::dropIfExists('strategic_plans');
        Schema::dropIfExists('teams');
    }
};