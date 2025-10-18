<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Budget management
        Schema::create('budgets', function (Blueprint $table) {
            $table->id('id_budget');
            $table->string('category', 50);
            $table->bigInteger('academic_period_id');
            $table->decimal('assigned_amount', 15, 2);
            $table->decimal('executed_amount', 15, 2)->default(0.00);
            $table->timestamps();
            $table->bigInteger('approver_user_id');

            $table->foreign('approver_user_id')->references('id')->on('users');
        });

        Schema::create('budget_impacts', function (Blueprint $table) {
            $table->id('id_budget_impact');
            $table->bigInteger('final_transaction_id');
            $table->bigInteger('budget_id');
            $table->decimal('impact_amount', 15, 2);
            $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('user_id');

            $table->foreign('budget_id')->references('id_budget')->on('budgets');
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Accounting management
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('description', 100);
            $table->string('account_type', 20);
            $table->decimal('current_balance', 15, 2)->default(0.00);
        });

        DB::statement('ALTER TABLE accounts ADD CONSTRAINT accounts_account_type_check CHECK (account_type IN (\'Asset\',\'Liability\',\'Income\',\'Expense\'))');

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type', 20);
            $table->decimal('amount', 15, 2);
            $table->timestamp('transaction_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('description');
            $table->string('category', 100);
            $table->bigInteger('account_id');
            $table->bigInteger('budget_id')->nullable();
            $table->bigInteger('registered_by');
            $table->string('attachment', 255)->nullable();

            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('budget_id')->references('id_budget')->on('budgets');
            $table->foreign('registered_by')->references('id')->on('users');
        });

        DB::statement('ALTER TABLE transactions ADD CONSTRAINT transactions_transaction_type_check CHECK (transaction_type IN (\'Income\',\'Expense\'))');

        // Audit management
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity', 50);
            $table->bigInteger('entity_id');
            $table->string('action', 20);
            $table->bigInteger('user_id');
            $table->timestamp('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->jsonb('changes');

            $table->foreign('user_id')->references('id')->on('users');
        });

        DB::statement('ALTER TABLE audit_logs ADD CONSTRAINT audit_logs_action_check CHECK (action IN (\'INSERT\',\'UPDATE\',\'DELETE\'))');

        // Companies management
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('industry', 100);
            $table->string('contact_name', 100);
            $table->string('contact_email', 150);
            $table->timestamps();
        });

        // Courses management
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_id');
            $table->string('title', 255);
            $table->string('name', 200)->nullable();
            $table->text('description')->nullable();
            $table->string('level', 20)->default('basic');
            $table->string('course_image', 255)->nullable();
            $table->string('video_url', 255)->nullable();
            $table->decimal('duration', 8, 2)->nullable();
            $table->bigInteger('sessions')->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->text('prerequisites')->nullable();
            $table->boolean('certificate_name')->default(false);
            $table->string('certificate_issuer', 255)->nullable();
            $table->boolean('bestseller')->default(false);
            $table->boolean('featured')->default(false);
            $table->boolean('highest_rated')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE courses ADD CONSTRAINT courses_level_check CHECK (level IN (\'basic\',\'intermediate\',\'advanced\'))');

        // Academic periods
        Schema::create('academic_periods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('academic_period_id');
            $table->string('name', 255);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 50)->default('open');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        // Instructors management
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('instructor_id');
            $table->bigInteger('user_id');
            $table->text('bio')->nullable();
            $table->string('expertise_area', 255)->nullable();
            $table->string('status', 50)->default('active');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->bigInteger('duration_weeks')->nullable();
            $table->bigInteger('max_capacity')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('PEN');
            $table->string('image_url', 500)->nullable();
            $table->string('modality', 10)->default('virtual');
            $table->text('required_devices')->nullable();
            $table->string('status', 10)->default('active');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE programs ADD CONSTRAINT programs_modality_check CHECK (modality IN (\'virtual\',\'hybrid\'))');
        DB::statement('ALTER TABLE programs ADD CONSTRAINT programs_status_check CHECK (status IN (\'active\',\'inactive\'))');
        DB::statement('ALTER TABLE programs ADD CONSTRAINT programs_duration_weeks_check CHECK (duration_weeks IS NULL OR duration_weeks > 0)');
        DB::statement('ALTER TABLE programs ADD CONSTRAINT programs_max_capacity_check CHECK (max_capacity IS NULL OR max_capacity > 0)');

        // Students management
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id');
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('company_id')->nullable();
            $table->string('document_number', 20)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });

        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_offering_id');
            $table->bigInteger('course_id');
            $table->bigInteger('academic_period_id');
            $table->bigInteger('instructor_id')->nullable();
            $table->text('schedule')->nullable();
            $table->string('delivery_method', 50)->default('regular');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('academic_period_id')->references('id')->on('academic_periods');
            $table->foreign('instructor_id')->references('id')->on('instructors');
        });

        // Enrollments management
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('enrollment_id');
            $table->bigInteger('student_id');
            $table->bigInteger('academic_period_id');
            $table->string('enrollment_type', 100)->default('new');
            $table->date('enrollment_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('academic_period_id')->references('id')->on('academic_periods');
        });

        // Subjects management
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code', 20)->nullable();
            $table->string('subject_name', 100)->nullable();
            $table->bigInteger('credits')->nullable();
            $table->string('status', 20)->nullable();
        });

        Schema::create('enrollment_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('enrollment_id');
            $table->bigInteger('subject_id')->nullable();
            $table->bigInteger('course_offering_id')->nullable();
            $table->string('status', 50)->default('active');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('enrollment_id')->references('id')->on('enrollments');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('course_offering_id')->references('id')->on('course_offerings');
        });

        Schema::create('enrollment_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('enrollment_id');
            $table->bigInteger('student_id');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('receipt', 100)->nullable();
            $table->string('status', 20)->nullable();

            $table->foreign('enrollment_id')->references('id')->on('enrollments');
            $table->foreign('student_id')->references('id')->on('students');
        });

        // Documents management
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200)->nullable();
            $table->string('category', 20);
            $table->string('entity_type', 100)->nullable();
            $table->bigInteger('entity_id')->nullable();
            $table->bigInteger('version')->nullable();
            $table->string('status', 20);
            $table->string('file_path', 255)->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::statement('ALTER TABLE documents ADD CONSTRAINT documents_category_check CHECK (category IN (\'academic\',\'administrative\',\'legal\'))');
        DB::statement('ALTER TABLE documents ADD CONSTRAINT documents_status_check CHECK (status IN (\'draft\',\'active\',\'archived\'))');

        Schema::create('document_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('document_id');
            $table->bigInteger('version');
            $table->text('change_description')->nullable();
            $table->bigInteger('changed_by')->nullable();

            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('changed_by')->references('id')->on('users');
        });

        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable();
            $table->string('category', 20);
            $table->string('file_path', 255)->nullable();
            $table->boolean('is_active')->default(true);
        });

        DB::statement('ALTER TABLE document_templates ADD CONSTRAINT document_templates_category_check CHECK (category IN (\'academic\',\'administrative\',\'legal\'))');

        // Indicators management
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable();
            $table->text('description')->nullable();
            $table->string('category', 50);
            $table->string('formula', 255)->nullable();
            $table->decimal('target_value', 6, 2)->nullable();
            $table->string('unit', 20)->nullable();
            $table->bigInteger('responsible_user_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('responsible_user_id')->references('id')->on('users');
        });

        DB::statement('ALTER TABLE indicators ADD CONSTRAINT indicators_category_check CHECK (category IN (\'academic\',\'administrative\',\'financial\',\'satisfaction\'))');

        Schema::create('indicator_measurements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('indicator_id');
            $table->string('period', 20)->nullable();
            $table->decimal('value', 10, 2);
            $table->string('data_source', 255)->nullable();
            $table->timestamp('recorded_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('recorded_by')->nullable();

            $table->foreign('indicator_id')->references('id')->on('indicators');
            $table->foreign('recorded_by')->references('id')->on('users');
        });

        Schema::create('indicator_alerts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('indicator_id');
            $table->decimal('current_value', 10, 2);
            $table->decimal('threshold', 10, 2);
            $table->string('status', 20);
            $table->boolean('notified')->default(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('indicator_id')->references('id')->on('indicators');
        });

        DB::statement('ALTER TABLE indicator_alerts ADD CONSTRAINT indicator_alerts_status_check CHECK (status IN (\'normal\',\'alert\',\'critical\'))');

        // Human resources management
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('department_name', 100);
            $table->text('description')->nullable();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('position_name', 100);
            $table->bigInteger('department_id');

            $table->foreign('department_id')->references('id')->on('departments');
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id');
            $table->date('hire_date')->nullable();
            $table->bigInteger('position_id');
            $table->bigInteger('department_id');
            $table->bigInteger('user_id')->nullable();
            $table->string('employment_status', 20);
            $table->text('schedule')->nullable();
            $table->string('speciality', 255)->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('position_id')->references('id')->on('positions');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('user_id')->references('id')->on('users');
        });

        DB::statement('ALTER TABLE employees ADD CONSTRAINT employees_employment_status_check CHECK (employment_status IN (\'Active\',\'Inactive\',\'Terminated\'))');

        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('position_id');
            $table->bigInteger('department_id');
            $table->string('status', 20)->default('Open');
            $table->date('posted_date')->default(DB::raw('CURRENT_DATE'));
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('salary_range', 50)->nullable();

            $table->foreign('position_id')->references('id')->on('positions');
            $table->foreign('department_id')->references('id')->on('departments');
        });

        DB::statement('ALTER TABLE job_vacancies ADD CONSTRAINT job_vacancies_status_check CHECK (status IN (\'Open\', \'In Progress\', \'Closed\'))');

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('cv_path', 255)->nullable();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('candidate_id');
            $table->bigInteger('vacancy_id');
            $table->string('status', 20);
            $table->date('application_date')->nullable();

            $table->foreign('candidate_id')->references('id')->on('candidates');
            $table->foreign('vacancy_id')->references('id')->on('job_vacancies');
        });

        DB::statement('ALTER TABLE job_applications ADD CONSTRAINT job_applications_status_check CHECK (status IN (\'Review\',\'Interview\',\'Hired\',\'Rejected\'))');

        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('institution', 200)->nullable();
            $table->string('provider', 100)->nullable();
            $table->bigInteger('duration_hours')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE trainings ADD CONSTRAINT trainings_duration_hours_check CHECK (duration_hours IS NULL OR duration_hours >= 0)');

        Schema::create('employee_trainings', function (Blueprint $table) {
            $table->bigInteger('employee_id');
            $table->bigInteger('training_id');
            $table->boolean('attended')->default(false);
            $table->decimal('grade', 5, 2)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('certificate', 255)->nullable();
            $table->timestamps();

            $table->primary(['employee_id', 'training_id']);
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('training_id')->references('id')->on('trainings');
        });

        Schema::create('performance_evaluations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id');
            $table->string('period', 20)->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->text('comments')->nullable();

            $table->foreign('employee_id')->references('id')->on('employees');
        });

        // Revenue and billing management
        Schema::create('revenue_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('enrollment_id')->nullable();
            $table->bigInteger('revenue_source_id')->nullable();
            $table->string('invoice_number', 50)->nullable();
            $table->date('issue_date')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('status', 20)->default('Pending');
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('enrollments');
            $table->foreign('revenue_source_id')->references('id')->on('revenue_sources');
        });

        DB::statement('ALTER TABLE invoices ADD CONSTRAINT invoices_status_check CHECK (status IN (\'Pending\',\'Paid\',\'Cancelled\'))');

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('description', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoice_id')->nullable();
            $table->bigInteger('payment_method_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('payment_date')->nullable();
            $table->string('status', 20)->default('Pending');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });

        DB::statement('ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN (\'Pending\',\'Completed\',\'Failed\'))');

        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoice_id')->nullable();
            $table->bigInteger('installments')->nullable();
            $table->decimal('installment_amount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('status', 20)->default('Active');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices');
        });

        DB::statement('ALTER TABLE payment_plans ADD CONSTRAINT payment_plans_status_check CHECK (status IN (\'Active\',\'Completed\',\'Defaulted\'))');

        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id');
            $table->decimal('amount', 12, 2);
            $table->date('transaction_date');
            $table->string('description', 255)->nullable();
            $table->string('transaction_type', 10);
            $table->bigInteger('invoice_id')->nullable();
            $table->bigInteger('payment_id')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
        });

        DB::statement('ALTER TABLE financial_transactions ADD CONSTRAINT financial_transactions_transaction_type_check CHECK (transaction_type IN (\'income\',\'expense\'))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('payment_plans');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('revenue_sources');
        Schema::dropIfExists('performance_evaluations');
        Schema::dropIfExists('employee_trainings');
        Schema::dropIfExists('trainings');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('job_vacancies');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('indicator_alerts');
        Schema::dropIfExists('indicator_measurements');
        Schema::dropIfExists('indicators');
        Schema::dropIfExists('document_templates');
        Schema::dropIfExists('document_histories');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('enrollment_payments');
        Schema::dropIfExists('enrollment_details');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('course_offerings');
        Schema::dropIfExists('students');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('academic_periods');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('budget_impacts');
        Schema::dropIfExists('budgets');
    }
};