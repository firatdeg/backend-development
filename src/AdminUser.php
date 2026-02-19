<?php declare(strict_types=1);

namespace App;

use App\Traits\CanLogin;

class AdminUser extends UserBase
{
    use CanLogin;

    public const ROLE_ADMIN = 'ADMIN';

    // A numeric array is used here since permissions don't need key-value pairs.
    private array $permissions = [];

    public function __construct(string $name, string $email, array $permissions = [])
    {
        parent::__construct($name, $email);
        $this->permissions = $permissions;
    }

    public function getRole(): string
    {
        return self::ROLE_ADMIN;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }

    public function __toString(): string
    {
        return "Admin: {$this->name} ({$this->email}) - Permissions: " . count($this->permissions);
    }
}