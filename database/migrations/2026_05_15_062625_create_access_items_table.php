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
        if (MigrationSchema::hasTable('access_items')) {
            return;
        }

        Schema::create('access_items', function (Blueprint $table) {
            $table->id();
            $table->string('platform_name');
            $table->string('login_url', 2048)->nullable();
            $table->string('username')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->string('pic_name')->nullable()->index();
            $table->string('sensitivity_level', 32)->default('medium')->index();
            $table->string('password_location')->nullable();
            $table->text('note')->nullable();
            $table->string('status', 32)->default('active')->index();
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
        Schema::dropIfExists('access_items');
    }
};
