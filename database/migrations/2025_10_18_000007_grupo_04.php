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
        // Roles and locations
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('description', 255)->nullable();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('country', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('city', 100)->nullable();
        });

        // User profiles
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('gender', 10)->nullable();
            $table->date('birth_date')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->text('bio')->nullable();
            $table->text('experience')->nullable();
            $table->string('photo', 255)->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
        });

        DB::statement('ALTER TABLE user_profiles ADD CONSTRAINT user_profiles_gender_check CHECK (gender IN (\'male\',\'female\'))');

        // Leads management
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 255);
            $table->string('phone', 20)->nullable();
            $table->string('origin', 50)->nullable();
            $table->string('main_interest', 100)->nullable();
            $table->string('status', 20)->default('new');
            $table->timestamp('creation_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('last_contact_date')->nullable();
        });

        DB::statement('ALTER TABLE leads ADD CONSTRAINT leads_status_check CHECK (status IN (\'new\', \'qualified\', \'contacted\', \'discarded\'))');

        // Content management
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('content_type', 20);
            $table->string('platform', 20)->nullable();
            $table->text('prompt');
            $table->text('generated_content');
            $table->jsonb('metadata')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('creation_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE contents ADD CONSTRAINT contents_content_type_check CHECK (content_type IN (\'social_post\',\'blog\',\'email\',\'video_script\',\'podcast\'))');
        DB::statement('ALTER TABLE contents ADD CONSTRAINT contents_platform_check CHECK (platform IN (\'facebook\',\'instagram\',\'twitter\',\'web\',\'newsletter\',\'youtube\'))');
        DB::statement('ALTER TABLE contents ADD CONSTRAINT contents_status_check CHECK (status IN (\'draft\',\'published\',\'scheduled\'))');

        Schema::create('generation_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('content_id')->nullable();
            $table->string('status', 20);
            $table->text('error_message')->nullable();
            $table->timestamp('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE generation_logs ADD CONSTRAINT generation_logs_status_check CHECK (status IN (\'successful\',\'failed\'))');

        // Campaigns and messaging
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('channel', 20);
            $table->string('target_audience', 100)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamp('creation_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        DB::statement('ALTER TABLE campaigns ADD CONSTRAINT campaigns_channel_check CHECK (channel IN (\'whatsapp\',\'facebook\',\'instagram\',\'webchat\',\'email\'))');
        DB::statement('ALTER TABLE campaigns ADD CONSTRAINT campaigns_status_check CHECK (status IN (\'active\',\'paused\',\'finished\'))');

        Schema::create('bot_flows', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('campaign_id')->nullable();
            $table->string('flow_name', 100);
            $table->string('trigger_keyword', 50)->nullable();
            $table->jsonb('flow_config');
            $table->bigInteger('priority')->default(1);

            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lead_id');
            $table->bigInteger('campaign_id')->nullable();
            $table->string('channel', 20);
            $table->string('message_type', 10);
            $table->text('message_content');
            $table->bigInteger('bot_flow_id')->nullable();
            $table->bigInteger('agent_id')->nullable();
            $table->timestamp('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('status', 20)->default('pending');

            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->foreign('bot_flow_id')->references('id')->on('bot_flows');
            $table->foreign('agent_id')->references('id')->on('users');
        });

        DB::statement('ALTER TABLE conversations ADD CONSTRAINT conversations_message_type_check CHECK (message_type IN (\'in\',\'out\'))');
        DB::statement('ALTER TABLE conversations ADD CONSTRAINT conversations_status_check CHECK (status IN (\'pending\',\'responded\',\'closed\'))');

        // Service configurations
        Schema::create('service_configs', function (Blueprint $table) {
            $table->id();
            $table->string('service_type', 30);
            $table->string('provider', 50);
            $table->jsonb('config_data');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_update')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        DB::statement('ALTER TABLE service_configs ADD CONSTRAINT service_configs_service_type_check CHECK (service_type IN (\'ai_content\',\'messaging\'))');

        // Metrics and performance
        Schema::create('campaign_metrics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('campaign_id');
            $table->date('date')->default(DB::raw('CURRENT_DATE'));
            $table->bigInteger('impressions')->default(0);
            $table->bigInteger('clicks')->default(0);
            $table->bigInteger('conversions')->default(0);
            $table->decimal('engagement_rate', 5, 4)->nullable();

            $table->foreign('campaign_id')->references('id')->on('campaigns');
        });

        Schema::create('content_performance', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('content_id');
            $table->bigInteger('views')->default(0);
            $table->bigInteger('likes')->default(0);
            $table->bigInteger('shares')->default(0);
            $table->decimal('click_rate', 5, 4)->nullable();

            $table->foreign('content_id')->references('id')->on('contents');
        });

        // Lead interests
        Schema::create('lead_course_interests', function (Blueprint $table) {
            $table->bigInteger('lead_id');
            $table->bigInteger('course_id');
            $table->timestamp('interest_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->primary(['lead_id', 'course_id']);
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_course_interests');
        Schema::dropIfExists('content_performance');
        Schema::dropIfExists('campaign_metrics');
        Schema::dropIfExists('service_configs');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('bot_flows');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('generation_logs');
        Schema::dropIfExists('contents');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('roles');
    }
};