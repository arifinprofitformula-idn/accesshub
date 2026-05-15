<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            foreach (['categories', 'links', 'access_items'] as $table) {
                if (MigrationSchema::hasTable($table)) {
                    DB::statement("ALTER TABLE `{$table}` ENGINE = InnoDB");
                }
            }
        }

        if (
            MigrationSchema::hasTable('links')
            && MigrationSchema::hasTable('categories')
            && MigrationSchema::hasColumn('links', 'category_id')
            && ! MigrationSchema::hasForeignKey('links', 'links_category_id_foreign')
        ) {
            Schema::table('links', function (Blueprint $table) {
                $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->nullOnDelete();
            });
        }

        if (
            MigrationSchema::hasTable('access_items')
            && MigrationSchema::hasTable('categories')
            && MigrationSchema::hasColumn('access_items', 'category_id')
            && ! MigrationSchema::hasForeignKey('access_items', 'access_items_category_id_foreign')
        ) {
            Schema::table('access_items', function (Blueprint $table) {
                $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('access_items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });
    }
};
