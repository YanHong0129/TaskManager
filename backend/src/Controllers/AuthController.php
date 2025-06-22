<?php

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    // POST /register - User registration
    public function register(Request $request, Response $response) {
        try {
            $body = $request->getParsedBody();
            $name = $body['name'] ?? '';
            $email = $body['email'] ?? '';
            $password = $body['password'] ?? '';

            // Validate input
            if (empty($name) || empty($email) || empty($password)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Name, email, and password are required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid email format'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (strlen($password) < 6) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Password must be at least 6 characters long'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Check if user already exists
            if (User::where('email', $email)->exists()) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Email already registered'
                ]));
                return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
            }

            // Create user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);

            // Generate JWT token
            $token = $this->generateToken($user);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Registration failed'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // POST /login - User login
    public function login(Request $request, Response $response) {
        try {
            $body = $request->getParsedBody();
            $email = $body['email'] ?? '';
            $password = $body['password'] ?? '';

            if (empty($email) || empty($password)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Email and password are required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Find user by email
            $user = User::where('email', $email)->first();

            if (!$user || !password_verify($password, $user->password)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid credentials'
                ]));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            // Generate JWT token
            $token = $this->generateToken($user);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Login failed'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // GET /api/profile - Get user profile
    public function profile(Request $request, Response $response) {
        try {
            $user = $this->getUserFromToken($request);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to fetch profile'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // PUT /api/profile - Update user profile
    public function updateProfile(Request $request, Response $response) {
        try {
            $user = $this->getUserFromToken($request);
            $data = $request->getParsedBody();
            
            $updateData = [];
            
            if (isset($data['name']) && !empty($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            
            if (isset($data['email']) && !empty($data['email'])) {
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Invalid email format'
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
                
                // Check if email is already taken by another user
                $existingUser = User::where('email', $data['email'])->where('id', '!=', $user->id)->first();
                if ($existingUser) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Email already taken'
                    ]));
                    return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
                }
                
                $updateData['email'] = $data['email'];
            }
            
            if (empty($updateData)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'No valid data to update'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $user->update($updateData);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user,
                'message' => 'Profile updated successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to update profile'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // PUT /api/change-password - Change password
    public function changePassword(Request $request, Response $response) {
        try {
            $user = $this->getUserFromToken($request);
            $data = $request->getParsedBody();
            
            $currentPassword = $data['current_password'] ?? '';
            $newPassword = $data['new_password'] ?? '';
            
            if (empty($currentPassword) || empty($newPassword)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Current password and new password are required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            if (strlen($newPassword) < 6) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'New password must be at least 6 characters long'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            // Verify current password
            if (!password_verify($currentPassword, $user->password)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Current password is incorrect'
                ]));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }
            
            // Update password
            $user->update([
                'password' => password_hash($newPassword, PASSWORD_DEFAULT)
            ]);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Password changed successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to change password'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    private function generateToken(User $user) {
        $now = time();
        $payload = [
            "iat" => $now,
            "exp" => $now + (24 * 60 * 60), // expires in 24 hours
            "sub" => $user->id,
            "email" => $user->email,
            "name" => $user->name
        ];

        return JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');
    }

    private function getUserFromToken(Request $request) {
        $token = $request->getAttribute('token');
        return User::find($token['sub']);
    }
} 