<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        $adminName = env('ADMIN_NAME', 'Admin User');
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD');
        if (!$adminPassword) {
            $this->command->warn('ADMIN_PASSWORD não está definido no .env. Usuário admin não foi criado.');
        } else {
            $admin = User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => $adminName,
                    'password' => bcrypt($adminPassword),
                ]
            );
            $this->command->info("Usuário admin criado/atualizado: $adminEmail");
        }

        // Seed platform roles and permissions (spatie)
        $this->call(PlatformRolesAndPermissionsSeeder::class);
    }
}
