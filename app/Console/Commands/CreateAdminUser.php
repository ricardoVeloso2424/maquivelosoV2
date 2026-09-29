<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CreateAdminUser extends Command
{
    /**
     * Interactive command — it never ships default credentials.
     */
    protected $signature = 'admin:create';

    protected $description = 'Create or promote an administrator account (interactive, no default credentials).';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Nome'));
        $email = Str::lower(trim((string) $this->ask('Email')));
        $password = (string) $this->secret('Password');
        $passwordConfirmation = (string) $this->secret('Confirmar password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $existing = User::query()->where('email', $email)->first();

        if ($existing && $existing->is_admin) {
            $this->warn("O utilizador {$email} já é administrador. Nada a fazer.");

            return self::SUCCESS;
        }

        // Use the model's controlled escape hatch so the privilege-escalation
        // guard does not silently strip the is_admin flag.
        $user = User::allowAdminPromotion(function () use ($existing, $name, $email, $password): User {
            $user = $existing ?? new User();

            if ($existing === null) {
                $user->name = $name;
                $user->email = $email;
                $user->password = Hash::make($password);
            }

            $user->forceFill(['is_admin' => true])->save();

            return $user;
        });

        $this->info($existing
            ? "Utilizador {$user->email} promovido a administrador."
            : "Administrador {$user->email} criado com sucesso.");

        return self::SUCCESS;
    }
}
