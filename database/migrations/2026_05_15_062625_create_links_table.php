<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (MigrationSchema::hasTable('links')) {
            return;
        }

        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url', 2048);
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->string('platform')->nullable()->index();
            $table->string('priority', 32)->default('normal')->index();
            $table->string('status', 32)->default('active')->index();
            $table->string('visibility', 32)->default('internal')->index();
            $table->string('owner_name')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_checked_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
