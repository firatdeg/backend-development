<?php declare(strict_types=1);

namespace App\Interfaces;

interface Resettable
{
    public function resetPassword(string $newPassword): void;
}