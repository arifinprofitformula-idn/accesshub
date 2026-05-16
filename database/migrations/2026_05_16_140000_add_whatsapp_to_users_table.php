<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'whatsapp')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('whatsapp', 30)->nullable()->after('email');
                $table->unique('whatsapp');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'whatsapp')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropUnique(['whatsapp']);
                $table->dropColumn('whatsapp');
            });
        }
    }
};
