<?php declare(strict_types=1);

namespace App\Traits;

trait CanLogin
{
    private bool $isLoggedIn = false;

    public function login(string $email, string $password): bool
    {
        // Hashing
        $hash = $this->getPasswordHash();
        
        if ($email !== $this->getEmail() || $hash === null || !password_verify($password, $hash)) {
            return false;
        }

        // In a real app, we'd establish a session here. For this context, a boolean state is enough.
        $this->isLoggedIn = true;
        return true;
    }

    public function logout(): void
    {
        $this->isLoggedIn = false;
    }

    public function getLoginStatus(): bool
    {
        return $this->isLoggedIn;
    }
}