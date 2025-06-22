<?php

namespace App\Controllers;

use App\Models\Task;
use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TaskController
{
    // GET /api/tasks - Get all tasks for authenticated user
    public function index(Request $request, Response $response) {
        try {
            $user = $this->getUserFromToken($request);
            $tasks = Task::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $tasks
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to fetch tasks'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // GET /api/tasks/{id} - Get specific task
    public function show(Request $request, Response $response, $args) {
        try {
            $user = $this->getUserFromToken($request);
            $task = Task::where('id', $args['id'])
                       ->where('user_id', $user->id)
                       ->first();
            
            if (!$task) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Task not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $task
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to fetch task'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // POST /api/tasks - Create new task
    public function create(Request $request, Response $response) {
        try {
            $user = $this->getUserFromToken($request);
            $data = $request->getParsedBody();
            
            // Validate required fields
            if (empty($data['title'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Title is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'completed' => false,
                'priority' => $data['priority'] ?? 'medium',
                'due_date' => $data['due_date'] ?? null,
                'user_id' => $user->id
            ]);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $task,
                'message' => 'Task created successfully'
            ]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to create task'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // PUT /api/tasks/{id} - Update task
    public function update(Request $request, Response $response, $args) {
        try {
            $user = $this->getUserFromToken($request);
            $data = $request->getParsedBody();
            
            $task = Task::where('id', $args['id'])
                       ->where('user_id', $user->id)
                       ->first();
            
            if (!$task) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Task not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            $task->update($data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $task,
                'message' => 'Task updated successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to update task'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // DELETE /api/tasks/{id} - Delete task
    public function destroy(Request $request, Response $response, $args) {
        try {
            $user = $this->getUserFromToken($request);
            
            $task = Task::where('id', $args['id'])
                       ->where('user_id', $user->id)
                       ->first();
            
            if (!$task) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Task not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            $task->delete();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to delete task'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // PATCH /api/tasks/{id}/toggle - Toggle task completion
    public function toggle(Request $request, Response $response, $args) {
        try {
            $user = $this->getUserFromToken($request);
            
            $task = Task::where('id', $args['id'])
                       ->where('user_id', $user->id)
                       ->first();
            
            if (!$task) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Task not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            $task->update(['completed' => !$task->completed]);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $task,
                'message' => 'Task status updated successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to toggle task'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // GET /api/tasks/stats - Get task statistics
    public function stats(Request $request, Response $response) {
        try {
            $user = $this->getUserFromToken($request);
            
            $totalTasks = Task::where('user_id', $user->id)->count();
            $completedTasks = Task::where('user_id', $user->id)->where('completed', true)->count();
            $pendingTasks = $totalTasks - $completedTasks;
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
            
            $stats = [
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'pending' => $pendingTasks,
                'completion_rate' => $completionRate
            ];
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $stats
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to fetch statistics'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    private function getUserFromToken(Request $request) {
        $token = $request->getAttribute('token');
        return User::find($token['sub']);
    }
}
