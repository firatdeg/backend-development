<?php declare(strict_types=1);

namespace App;

use App\Traits\CanLogin;

class CustomerUser extends UserBase
{
    use CanLogin;

    public const ROLE_CUSTOMER = 'CUSTOMER';

   // Numeric array is used for purchase history since we dont need key values here
    private array $purchaseHistory = [];

    public function getRole(): string
    {
        return self::ROLE_CUSTOMER;
    }

    public function addPurchase(float $amount): void
    {
        $this->purchaseHistory[] = $amount;
    }

    public function getTotalSpent(): float
    {
        return array_sum($this->purchaseHistory);
    }

    public function __toString(): string
    {
        return "Customer: {$this->name} ({$this->email}) - Total Spent: $" . $this->getTotalSpent();
    }
}