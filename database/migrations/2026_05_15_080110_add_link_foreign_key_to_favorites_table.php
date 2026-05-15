<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            MigrationSchema::hasTable('favorites')
            && MigrationSchema::hasTable('links')
            && MigrationSchema::hasColumn('favorites', 'link_id')
            && ! MigrationSchema::hasForeignKey('favorites', 'favorites_link_id_foreign')
        ) {
            Schema::table('favorites', function (Blueprint $table) {
                $table->foreign('link_id')
                    ->references('id')
                    ->on('links')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign(['link_id']);
        });
    }
};
