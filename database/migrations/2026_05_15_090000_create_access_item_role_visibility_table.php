<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (MigrationSchema::hasTable('access_item_role_visibility')) {
            return;
        }

        Schema::create('access_item_role_visibility', function (Blueprint $table) {
            $table->foreignId('access_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();

            $table->primary(['access_item_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_item_role_visibility');
    }
};
