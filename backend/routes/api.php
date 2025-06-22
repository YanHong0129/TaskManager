<?php

use Slim\App;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\TaskController;
use App\Controllers\AuthController;
use App\Models\User;

return function (App $app) {
    // Public routes (no JWT required)
    $app->post('/register', AuthController::class . ':register');
    $app->post('/login', AuthController::class . ':login');

    // Protected routes (JWT required)
    // User management endpoints
    $app->get('/api/profile', AuthController::class . ':profile');
    $app->put('/api/profile', AuthController::class . ':updateProfile');
    $app->put('/api/change-password', AuthController::class . ':changePassword');

    // Task management endpoints
    $app->get('/api/tasks/stats', TaskController::class . ':stats');
    $app->get('/api/tasks', TaskController::class . ':index');
    $app->get('/api/tasks/{id}', TaskController::class . ':show');
    $app->post('/api/tasks', TaskController::class . ':create');
    $app->put('/api/tasks/{id}', TaskController::class . ':update');
    $app->delete('/api/tasks/{id}', TaskController::class . ':destroy');
    $app->patch('/api/tasks/{id}/toggle', TaskController::class . ':toggle');

    // Health check endpoint
    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'TaskMate API is running',
            'version' => '1.0.0',
            'endpoints' => [
                'public' => ['/register', '/login'],
                'protected' => [
                    '/api/profile',
                    '/api/tasks',
                    '/api/tasks/stats'
                ]
            ]
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });
};
