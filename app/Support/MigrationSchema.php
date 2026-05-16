<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MigrationSchema
{
    public static function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable) {
            return false;
        }
    }

    public static function hasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (Throwable) {
            return false;
        }
    }

    public static function hasForeignKey(string $table, string $constraint): bool
    {
        try {
            $database = DB::getDatabaseName();

            if (! $database) {
                return false;
            }

            return DB::table('information_schema.TABLE_CONSTRAINTS')
                ->where('CONSTRAINT_SCHEMA', $database)
                ->where('TABLE_NAME', $table)
                ->where('CONSTRAINT_NAME', $constraint)
                ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
                ->exists();
        } catch (Throwable) {
            return false;
        }
    }

    public static function hasIndex(string $table, string $index): bool
    {
        try {
            $database = DB::getDatabaseName();

            if (! $database) {
                return false;
            }

            return DB::table('information_schema.STATISTICS')
                ->where('TABLE_SCHEMA', $database)
                ->where('TABLE_NAME', $table)
                ->where('INDEX_NAME', $index)
                ->exists();
        } catch (Throwable) {
            return false;
        }
    }
}
