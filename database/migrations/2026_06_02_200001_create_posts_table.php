<?php

use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('title');
            $table->longText('content');
            $table->enum('category', PostCategory::values())->default(PostCategory::GENERAL->value);
            $table->enum('status', PostStatus::values())->default(PostStatus::DRAFT->value);
            $table->date('post_date')->nullable();
            $table->time('post_time')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['category', 'post_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};