<?php

use App\Enums\Vacancy\EmploymentType;
use App\Enums\Vacancy\ExperienceLevel;
use App\Enums\Vacancy\VacancyStatus;
use App\Enums\Vacancy\WorkMode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title');
            $table->longText('description');
            $table->longText('requirements');
            $table->longText('responsibilities');
            $table->enum('employment_type', EmploymentType::values());
            $table->enum('experience_level', ExperienceLevel::values());
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('location');
            $table->enum('work_mode', WorkMode::values());
            $table->enum('status', VacancyStatus::values())->default(VacancyStatus::DRAFT->value);
            $table->date('application_deadline');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['employment_type', 'experience_level', 'work_mode']);
            $table->index(['published_at', 'application_deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};