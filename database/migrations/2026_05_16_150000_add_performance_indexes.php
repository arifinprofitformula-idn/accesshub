<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (MigrationSchema::hasTable('links')) {
            Schema::table('links', function (Blueprint $table): void {
                if (! MigrationSchema::hasIndex('links', 'links_created_by_index')) {
                    $table->index('created_by');
                }

                if (! MigrationSchema::hasIndex('links', 'links_category_id_index')) {
                    $table->index('category_id');
                }

                if (! MigrationSchema::hasIndex('links', 'links_created_by_status_created_at_index')) {
                    $table->index(['created_by', 'status', 'created_at']);
                }

                if (! MigrationSchema::hasIndex('links', 'links_created_by_category_id_status_created_at_index')) {
                    $table->index(['created_by', 'category_id', 'status', 'created_at']);
                }
            });
        }

        if (MigrationSchema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                if (! MigrationSchema::hasIndex('users', 'users_approved_at_index')) {
                    $table->index('approved_at');
                }

                if (! MigrationSchema::hasIndex('users', 'users_is_active_approved_at_index')) {
                    $table->index(['is_active', 'approved_at']);
                }
            });
        }
    }

    public function down(): void
    {
        if (MigrationSchema::hasTable('links')) {
            Schema::table('links', function (Blueprint $table): void {
                if (MigrationSchema::hasIndex('links', 'links_created_by_index')) {
                    $table->dropIndex('links_created_by_index');
                }

                if (MigrationSchema::hasIndex('links', 'links_category_id_index')) {
                    $table->dropIndex('links_category_id_index');
                }

                if (MigrationSchema::hasIndex('links', 'links_created_by_status_created_at_index')) {
                    $table->dropIndex('links_created_by_status_created_at_index');
                }

                if (MigrationSchema::hasIndex('links', 'links_created_by_category_id_status_created_at_index')) {
                    $table->dropIndex('links_created_by_category_id_status_created_at_index');
                }
            });
        }

        if (MigrationSchema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                if (MigrationSchema::hasIndex('users', 'users_approved_at_index')) {
                    $table->dropIndex('users_approved_at_index');
                }

                if (MigrationSchema::hasIndex('users', 'users_is_active_approved_at_index')) {
                    $table->dropIndex('users_is_active_approved_at_index');
                }
            });
        }
    }
};
