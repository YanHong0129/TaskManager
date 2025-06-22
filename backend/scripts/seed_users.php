<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\User;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Setup Eloquent ORM
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER'),
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_DATABASE'),
    'username'  => getenv('DB_USERNAME'),
    'password'  => getenv('DB_PASSWORD'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Sample users data
$users = [
    [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123'
    ],
    [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'password' => 'password123'
    ],
    [
        'name' => 'Admin User',
        'email' => 'admin@taskmate.com',
        'password' => 'admin123'
    ],
    [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'test123'
    ]
];

echo "=== TaskMate User Seeder ===\n\n";

$created = 0;
$skipped = 0;

foreach ($users as $userData) {
    try {
        // Check if user already exists
        if (User::where('email', $userData['email'])->exists()) {
            echo "User with email '{$userData['email']}' already exists - skipping\n";
            $skipped++;
            continue;
        }

        // Create user with hashed password
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => password_hash($userData['password'], PASSWORD_DEFAULT)
        ]);

        echo "✓ Created user: {$user->name} ({$user->email})\n";
        $created++;
        
    } catch (Exception $e) {
        echo "✗ Error creating user {$userData['email']}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Summary ===\n";
echo "Users created: $created\n";
echo "Users skipped: $skipped\n";
echo "Total processed: " . count($users) . "\n";

if ($created > 0) {
    echo "\nYou can now login with any of these accounts:\n";
    foreach ($users as $userData) {
        echo "- Email: {$userData['email']}, Password: {$userData['password']}\n";
    }
} 