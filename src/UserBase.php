<?php declare(strict_types=1);

namespace App;

// Abstract base for all users.
abstract class UserBase implements Interfaces\Resettable
{
    protected string $name;
    protected string $email;
    protected ?string $passwordHash = null;
    protected static int $userCount = 0;
    protected array $metadata = [];

    // Class constant for allowed magic properties
    private const ALLOWED_METADATA = ['lastLoginIP', 'preferences'];

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->setEmail($email); 
        self::$userCount++;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid Email Format: $email");
        }
        $this->email = $email;
    }

    public function resetPassword(string $newPassword): void
    {
        $this->passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function __clone() { self::$userCount++; }
    public static function getUserCount(): int { return self::$userCount; } 
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    
    public function __get(string $name): mixed 
    { 
        if (in_array($name, self::ALLOWED_METADATA, true)) {
            return $this->metadata[$name] ?? null;
        }
        throw new \InvalidArgumentException("Property '{$name}' is not accessible via __get().");
    }

    public function __set(string $name, mixed $value): void 
    { 
        if (in_array($name, self::ALLOWED_METADATA, true)) {
            $this->metadata[$name] = $value;
            return;
        }
        throw new \InvalidArgumentException("Property '{$name}' is not accessible via __set().");
    }

    public function __isset(string $name): bool { return isset($this->metadata[$name]); }

    abstract public function getRole(): string;
}