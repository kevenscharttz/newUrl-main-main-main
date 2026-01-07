<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Apenas para PostgreSQL: converter colunas JSON para JSONB
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'pgsql') {
            return;
        }

        // organizations.logo_settings -> jsonb
        if (Schema::hasTable('organizations') && Schema::hasColumn('organizations', 'logo_settings')) {
            DB::statement('ALTER TABLE organizations ALTER COLUMN logo_settings TYPE jsonb USING logo_settings::jsonb');
        }

        // dashboards.tags, dashboards.settings -> jsonb
        if (Schema::hasTable('dashboards')) {
            if (Schema::hasColumn('dashboards', 'tags')) {
                DB::statement('ALTER TABLE dashboards ALTER COLUMN tags TYPE jsonb USING tags::jsonb');
            }
            if (Schema::hasColumn('dashboards', 'settings')) {
                DB::statement('ALTER TABLE dashboards ALTER COLUMN settings TYPE jsonb USING settings::jsonb');
            }
        }

        // reports.tags, reports.settings -> jsonb
        if (Schema::hasTable('reports')) {
            if (Schema::hasColumn('reports', 'tags')) {
                DB::statement('ALTER TABLE reports ALTER COLUMN tags TYPE jsonb USING tags::jsonb');
            }
            if (Schema::hasColumn('reports', 'settings')) {
                DB::statement('ALTER TABLE reports ALTER COLUMN settings TYPE jsonb USING settings::jsonb');
            }
        }
    }

    public function down(): void
    {
        // Reverte para JSON se necessário (não recomendado, mas deixa reversível)
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'pgsql') {
            return;
        }

        if (Schema::hasTable('organizations') && Schema::hasColumn('organizations', 'logo_settings')) {
            DB::statement('ALTER TABLE organizations ALTER COLUMN logo_settings TYPE json USING logo_settings::json');
        }
        if (Schema::hasTable('dashboards')) {
            if (Schema::hasColumn('dashboards', 'tags')) {
                DB::statement('ALTER TABLE dashboards ALTER COLUMN tags TYPE json USING tags::json');
            }
            if (Schema::hasColumn('dashboards', 'settings')) {
                DB::statement('ALTER TABLE dashboards ALTER COLUMN settings TYPE json USING settings::json');
            }
        }
        if (Schema::hasTable('reports')) {
            if (Schema::hasColumn('reports', 'tags')) {
                DB::statement('ALTER TABLE reports ALTER COLUMN tags TYPE json USING tags::json');
            }
            if (Schema::hasColumn('reports', 'settings')) {
                DB::statement('ALTER TABLE reports ALTER COLUMN settings TYPE json USING settings::json');
            }
        }
    }
};
