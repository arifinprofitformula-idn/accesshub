<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (MigrationSchema::hasTable('link_role_visibility')) {
            return;
        }

        Schema::create('link_role_visibility', function (Blueprint $table) {
            $table->foreignId('link_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();

            $table->primary(['link_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_role_visibility');
    }
};
