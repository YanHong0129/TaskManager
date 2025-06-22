<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load(); // NOT safeLoad, this throws errors if .env is missing

foreach ($_ENV as $key => $value) {
    putenv("$key=$value");
}


// Set up the container and Slim app
$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

// Add BodyParsingMiddleware
$app->addBodyParsingMiddleware();

// Add RoutingMiddleware
// This should be added before the CORS middleware
$app->addRoutingMiddleware();

// Setup Eloquent ORM (Laravel's database layer)
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER'),
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_DATABASE'),
    'username'  => getenv('DB_USERNAME'),
    'password'  => getenv('DB_PASSWORD'), // will be empty if no password
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Add CORS middleware
$app->add(new \Tuupola\Middleware\CorsMiddleware([
    "origin" => ["http://localhost:5174", "http://localhost:5176"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"],
    "headers.allow" => ["Authorization", "Content-Type", "Accept", "Origin"],
    "credentials" => true,
    "cache" => 86400 // Cache for 1 day
]));

// Add JWT middleware (protects /api/* routes, skips /login and /register)
$app->add(new Tuupola\Middleware\JwtAuthentication([
    "secret" => getenv("JWT_SECRET"),
    "path" => "/api",
    "ignore" => ["/login", "/register"],
    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader("Content-Type", "application/json")->withStatus(401);
    }
]));

// Load your routes (tasks, login, etc.)
(require __DIR__ . '/../routes/api.php')($app);

// Add ErrorMiddleware
// This should be the last middleware added
$app->addErrorMiddleware(true, true, true);

$app->run();
