<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('full_name', 100)->nullable();
            $table->string('dni', 20)->unique()->nullable();
            $table->string('document', 20)->unique()->nullable();
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->jsonb('role')->default('"student"');
            $table->string('password', 255);
            $table->string('gender', 10)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('country_location', 100)->nullable();
            $table->string('timezone', 50)->default('America/Lima');
            $table->string('profile_photo', 500)->nullable();
            $table->string('status', 20)->default('active');
            $table->boolean('synchronized')->default(true);
            $table->string('last_access_ip', 45)->nullable();
            $table->timestamp('last_access')->nullable();
            $table->timestamp('last_connection')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));
            $table->timestampTz('updated_at')->default(DB::raw('now()'));
        });

        DB::statement("ALTER TABLE users ADD CONSTRAINT users_gender_check CHECK (gender IN ('male','female','other'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('active','inactive','banned'))");

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_gender_check');
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check');
        
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};