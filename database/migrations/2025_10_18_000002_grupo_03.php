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
        // Instructor applications
        Schema::create('instructor_applications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->text('bio')->nullable();
            $table->string('expertise_area', 150)->nullable();
            $table->string('status', 10)->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->bigInteger('reviewed_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });

        DB::statement('ALTER TABLE instructor_applications ADD CONSTRAINT instructor_applications_status_check CHECK (status IN (\'pending\',\'approved\',\'rejected\'))');

        // Course categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id');
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('image', 255)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_course_catg');
            $table->bigInteger('course_id');
            $table->bigInteger('category_id');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('category_id')->references('id')->on('categories');
        });

        Schema::create('course_instructors', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_course_inst');
            $table->bigInteger('instructor_id');
            $table->bigInteger('course_id');
            $table->timestamp('assigned_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('instructor_id')->references('id')->on('instructors');
            $table->foreign('course_id')->references('id')->on('courses');
        });

        Schema::create('course_contents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_id');
            $table->bigInteger('session')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('content')->nullable();
            $table->bigInteger('order_number')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('course_id')->references('id')->on('courses');
        });

        Schema::create('graduates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('program_id');
            $table->date('graduation_date');
            $table->float('final_note')->nullable();
            $table->string('state', 14);
            $table->string('employability', 255)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->unique(['user_id', 'program_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE graduates ADD CONSTRAINT graduates_state_check CHECK (state IN (\'graduated\',\'pending\',\'withdrawn\'))');

        // Tickets and support
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ticket_id');
            $table->bigInteger('assigned_technician')->nullable();
            $table->bigInteger('user_id');
            $table->string('title', 255);
            $table->text('description');
            $table->string('priority', 50)->default('media');
            $table->string('status', 50)->default('abierto');
            $table->timestamp('creation_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('assignment_date')->nullable();
            $table->timestamp('resolution_date')->nullable();
            $table->timestamp('close_date')->nullable();
            $table->string('category', 100)->nullable();
            $table->text('notes')->nullable();

            $table->foreign('assigned_technician')->references('id')->on('employees');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('ticket_trackings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ticket_tracking_id');
            $table->bigInteger('ticket_id');
            $table->text('comment')->nullable();
            $table->string('action_type', 50)->nullable();
            $table->timestamp('follow_up_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('ticket_id')->references('id')->on('tickets');
        });

        Schema::create('escalations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('escalation_id');
            $table->bigInteger('ticket_id');
            $table->bigInteger('technician_origin_id')->nullable();
            $table->bigInteger('technician_destiny_id')->nullable();
            $table->string('escalation_reason', 255)->nullable();
            $table->text('observations')->nullable();
            $table->timestamp('escalation_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('approved')->default(false);

            $table->foreign('ticket_id')->references('id')->on('tickets');
            $table->foreign('technician_origin_id')->references('id')->on('employees');
            $table->foreign('technician_destiny_id')->references('id')->on('employees');
        });

        // Security management
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_security_log');
            $table->bigInteger('user_id')->nullable();
            $table->string('event_type', 100);
            $table->text('description')->nullable();
            $table->string('source_ip', 45)->nullable();
            $table->timestamp('event_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_blocked_ip');
            $table->string('ip_address', 45);
            $table->string('reason', 255)->nullable();
            $table->timestamp('block_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('active')->default(true);
        });

        Schema::create('security_alerts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_security_alert');
            $table->string('threat_type', 100);
            $table->string('severity', 50)->default('medium');
            $table->string('status', 50)->default('new');
            $table->bigInteger('blocked_ip_id')->nullable();
            $table->timestamp('detection_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('blocked_ip_id')->references('id')->on('blocked_ips');
        });

        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_incident');
            $table->bigInteger('alert_id')->nullable();
            $table->bigInteger('responsible_id')->nullable();
            $table->string('title', 255);
            $table->string('status', 50)->default('open');
            $table->timestamp('report_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('alert_id')->references('id')->on('security_alerts');
            $table->foreign('responsible_id')->references('id')->on('employees');
        });

        Schema::create('active_sessions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('session_id');
            $table->bigInteger('user_id');
            $table->string('ip_address', 45)->nullable();
            $table->string('device', 255)->nullable();
            $table->timestamp('start_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('active')->default(true);
            $table->boolean('blocked')->default(false);

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('security_configurations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_security_configuration');
            $table->bigInteger('user_id')->nullable();
            $table->string('modulo', 100)->nullable();
            $table->string('parameter', 100);
            $table->text('value')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('users');
        });

        // Software and licenses
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_license');
            $table->string('software_name', 255);
            $table->text('license_key')->nullable();
            $table->string('license_type', 100)->nullable();
            $table->string('provider', 255)->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->bigInteger('seats_total')->nullable();
            $table->bigInteger('seats_used')->default(0);
            $table->decimal('cost_annual', 12, 2)->nullable();
            $table->string('status', 50)->default('active');
            $table->bigInteger('responsible_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('responsible_id')->references('id')->on('employees');
        });

        Schema::create('softwares', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_software');
            $table->string('software_name', 255);
            $table->string('version', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('vendor', 255)->nullable();
            $table->bigInteger('license_id')->nullable();
            $table->timestamp('installation_date')->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('license_id')->references('id')->on('licenses');
        });

        // Chatbot management
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_conversation');
            $table->timestamp('started_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('ended_date')->nullable();
            $table->bigInteger('satisfaction_rating')->nullable();
            $table->text('feedback')->nullable();
            $table->boolean('resolved')->default(false);
            $table->boolean('handed_to_human')->default(false);
        });

        DB::statement('ALTER TABLE chatbot_conversations ADD CONSTRAINT chatbot_conversations_satisfaction_rating_check CHECK (satisfaction_rating BETWEEN 1 AND 5)');

        Schema::create('chatbot_faqs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_faq');
            $table->text('question');
            $table->text('answer');
            $table->string('category', 100)->nullable();
            $table->jsonb('keywords')->nullable();
            $table->boolean('active')->default(true);
            $table->bigInteger('usage_count')->default(0);
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_message');
            $table->bigInteger('conversation_id');
            $table->string('sender', 50);
            $table->text('message');
            $table->timestamp('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('faq_matched')->nullable();

            $table->foreign('conversation_id')->references('id')->on('chatbot_conversations');
            $table->foreign('faq_matched')->references('id')->on('chatbot_faqs');
        });

        // News and announcements
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_news');
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('summary')->nullable();
            $table->text('content')->nullable();
            $table->string('featured_image', 500)->nullable();
            $table->bigInteger('author_id')->nullable();
            $table->string('category', 100)->nullable();
            $table->jsonb('tags')->nullable();
            $table->string('status', 50)->default('draft');
            $table->bigInteger('views')->default(0);
            $table->timestamp('published_date')->nullable();
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('seo_title', 255)->nullable();
            $table->text('seo_description')->nullable();

            $table->foreign('author_id')->references('id')->on('users');
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_announcement');
            $table->string('title', 255);
            $table->text('content');
            $table->string('image_url', 500)->nullable();
            $table->string('display_type', 50)->nullable();
            $table->string('target_page', 100)->nullable();
            $table->string('link_url', 500)->nullable();
            $table->string('button_text', 100)->nullable();
            $table->string('status', 50)->default('draft');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->bigInteger('views')->default(0);
            $table->bigInteger('created_by')->nullable();
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_alert');
            $table->text('message');
            $table->string('type', 50)->nullable();
            $table->string('status', 50)->default('active');
            $table->string('link_url', 500)->nullable();
            $table->string('link_text', 100)->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->bigInteger('priority')->default(1);
            $table->bigInteger('created_by')->nullable();
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('created_by')->references('id')->on('users');
        });

        // Contact forms
        Schema::create('contact_forms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_contact');
            $table->string('full_name', 255);
            $table->string('email', 255);
            $table->string('phone', 20)->nullable();
            $table->string('company', 255)->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('message');
            $table->string('form_type', 50)->nullable();
            $table->string('status', 50)->default('pending');
            $table->bigInteger('assigned_to')->nullable();
            $table->text('response')->nullable();
            $table->timestamp('response_date')->nullable();
            $table->timestamp('submission_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('assigned_to')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_forms');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('news');
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chatbot_faqs');
        Schema::dropIfExists('chatbot_conversations');
        Schema::dropIfExists('softwares');
        Schema::dropIfExists('licenses');
        Schema::dropIfExists('security_configurations');
        Schema::dropIfExists('active_sessions');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('security_alerts');
        Schema::dropIfExists('blocked_ips');
        Schema::dropIfExists('security_logs');
        Schema::dropIfExists('escalations');
        Schema::dropIfExists('ticket_trackings');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('graduates');
        Schema::dropIfExists('course_contents');
        Schema::dropIfExists('course_instructors');
        Schema::dropIfExists('course_categories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('instructor_applications');
    }
};