<?php

use Slim\App;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\TaskController;
use App\Models\User;

return function (App $app) {

    $app->post('/register', function (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $email = $body['email'] ?? '';
    $password = $body['password'] ?? '';

    // Validate email + password
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $response->getBody()->write(json_encode(['error' => 'Invalid email or password too short']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    // Check if user already exists
    if (User::where('email', $email)->exists()) {
        $response->getBody()->write(json_encode(['error' => 'Email already registered']));
        return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
    }

    // Create user with hashed password
    $user = User::create([
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ]);

    $response->getBody()->write(json_encode(['message' => 'User registered successfully']));
    return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
});

    // 👤 Login endpoint (hardcoded user for demo)
    $app->post('/login', function (Request $request, Response $response) {
        $body = $request->getParsedBody();

        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        if ($email !== 'demo@demo.com' || $password !== 'demo') {
            $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $now = time();
        $payload = [
            "iat" => $now,
            "exp" => $now + 3600, // expires in 1 hour
            "sub" => 1,
            "email" => $email
        ];

        $token = JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');

        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // 📝 Task CRUD Endpoints (all require JWT)
    $app->post('/api/tasks', TaskController::class . ':create');
    $app->get('/api/tasks', TaskController::class . ':index');
    $app->get('/api/tasks/{id}', TaskController::class . ':show');
    $app->put('/api/tasks/{id}', TaskController::class . ':update');
    $app->delete('/api/tasks/{id}', TaskController::class . ':destroy');
        // ✅ Simple test route
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write("✅ Slim backend is running");
        return $response;
    });

};
