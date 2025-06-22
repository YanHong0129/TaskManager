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

// Function to create a user
function createUser($name, $email, $password) {
    try {
        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            echo "User with email '$email' already exists!\n";
            return false;
        }

        // Create user with hashed password
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        echo "User created successfully!\n";
        echo "ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Created: {$user->created_at}\n";
        
        return true;
    } catch (Exception $e) {
        echo "Error creating user: " . $e->getMessage() . "\n";
        return false;
    }
}

// Interactive mode
if (php_sapi_name() === 'cli') {
    echo "=== TaskMate User Creation Script ===\n\n";
    
    echo "Enter user details:\n";
    echo "Name: ";
    $name = trim(fgets(STDIN));
    
    echo "Email: ";
    $email = trim(fgets(STDIN));
    
    echo "Password: ";
    $password = trim(fgets(STDIN));
    
    if (empty($name) || empty($email) || empty($password)) {
        echo "All fields are required!\n";
        exit(1);
    }
    
    createUser($name, $email, $password);
} else {
    // Web mode - you can call this from browser
    echo "<h2>TaskMate User Creation</h2>";
    echo "<p>This script should be run from command line for security.</p>";
    echo "<p>Usage: php create_user.php</p>";
} 