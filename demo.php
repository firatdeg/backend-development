<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\AdminUser;
use App\CustomerUser;
use App\UserBase;

ini_set('zend.assertions', '1');

function displayUserStatus(UserBase $user): string {
    return "Status: {$user->getName()} is a " . $user->getRole();
}

echo "--- Initializing Users ---\n";
try {
    $admin = new AdminUser('Alice Admine', 'alice@example.com', ['manage_users', 'view_logs']);
    $customer = new CustomerUser('Bob Customerson', 'bob@example.com');
    
    echo displayUserStatus($admin) . "\n";
    echo displayUserStatus($customer) . "\n\n";

    $failUser = new CustomerUser('Eve', 'not-an-email'); // this will throw an exception later
} catch (\InvalidArgumentException $e) {
    echo "Caught Exception: " . $e->getMessage() . "\n";
}

echo "\n--- New Email and Purchase History ---\n";
$customer->setEmail('bob.new@example.com'); 
$customer->addPurchase(150.50);
$customer->addPurchase(49.99);

echo "Customer total spent: $" . $customer->getTotalSpent() . "\n";
echo "Admin has 'manage_users' permission? " . ($admin->hasPermission('manage_users') ? 'Yes' : 'No') . "\n";
// Mocking a POST request. Associative arrays are ideal here.
$_POST = [
    'email' => 'alice@example.com',
    'password' => 'securePass123'
];

// Must set the password hash first so our trait's password_verify doesn't fail.
$admin->resetPassword('securePass123'); 
$loginSuccess = $admin->login($_POST['email'], $_POST['password']);

echo "Admin logged in via POST payload? " . ($loginSuccess ? 'Yes' : 'No') . "\n";
// Filtering users to find admins.
$users = [$admin, $customer];
$adminsOnly = array_filter($users, fn(UserBase $u): bool => $u->getRole() === AdminUser::ROLE_ADMIN);
echo "Found " . count($adminsOnly) . " admin(s).\n";

echo "\n--- User Roles ---\n";
foreach ($users as $user) {
    switch ($user->getRole()) {
        case AdminUser::ROLE_ADMIN: echo "- {$user->getName()}: System Access.\n"; break;
        case CustomerUser::ROLE_CUSTOMER: echo "- {$user->getName()}: Store Access.\n"; break;
    }
}

echo "\n--- Magic Methods ---\n";
echo "String cast: " . $customer . "\n";
$customer->lastLoginIP = '192.168.1.50'; 
echo "Customer IP (via __get): " . $customer->lastLoginIP . "\n";
echo "Total User Instances: " . UserBase::getUserCount() . "\n";

echo "\n--- Tests ---\n";
function runIntegrationTests(AdminUser $admin, CustomerUser $customer): void {
    assert(UserBase::getUserCount() >= 2, 'Counter mismatch');
    assert($customer->getTotalSpent() === 200.49, 'Business logic failed');
    assert($admin->hasPermission('view_logs') === true, 'Array logic failed');
    assert($admin->getPasswordHash() !== null, 'Inherited interface logic failed');

    $exceptionThrown = false;
    try {
        $customer->invalidKey = 'hacked'; 
    } catch (\InvalidArgumentException $e) {
        $exceptionThrown = true;
    }
    assert($exceptionThrown === true, 'Magic method whitelist logic failed');

    echo "All assertions passed successfully.\n";
}

runIntegrationTests($admin, $customer);