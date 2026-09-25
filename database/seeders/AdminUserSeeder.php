<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = (string) config('admin.name');
        $email = (string) config('admin.email');
        $password = config('admin.password');

        if (! is_string($password) || strlen($password) < 12) {
            throw new InvalidArgumentException('ADMIN_PASSWORD debe estar configurada y tener al menos 12 caracteres.');
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->command?->info("El usuario administrador {$email} ya existe; no se modificó su contraseña.");

            return;
        }

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        $this->command?->info("Usuario administrador creado: {$email}");
    }
}
