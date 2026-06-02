<?php

use App\Enums\Company\CompanyRoles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->enum('company_role', CompanyRoles::values())->default(CompanyRoles::EMPLOYEE->value);
            $table->string('position');
            $table->text('information')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current_position')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_memberships');
    }
};