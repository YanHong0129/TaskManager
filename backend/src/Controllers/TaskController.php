<?php

namespace App\Controllers;

use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// 🧱 Eloquent Model for the "tasks" table
class Task extends Model {
    protected $table = 'tasks';
    public $timestamps = false; // we’re not using created_at/updated_at
    protected $fillable = ['title', 'completed'];
}

// 🎯 Controller class
class TaskController
{
    // GET /api/tasks
    public function index(Request $request, Response $response) {
        $tasks = Task::all();
        $response->getBody()->write($tasks->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /api/tasks/{id}
    public function show(Request $request, Response $response, $args) {
        $task = Task::find($args['id']);
        if (!$task) {
            return $response->withStatus(404);
        }
        $response->getBody()->write($task->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // POST /api/tasks
    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $task = Task::create([
            'title' => $data['title'] ?? 'Untitled Task',
            'completed' => 0
        ]);
        $response->getBody()->write($task->toJson());
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    // PUT /api/tasks/{id}
    public function update(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $task = Task::find($args['id']);
        if (!$task) {
            return $response->withStatus(404);
        }
        $task->update($data);
        return $response->withHeader('Content-Type', 'application/json');
    }

    // DELETE /api/tasks/{id}
    public function destroy(Request $request, Response $response, $args) {
        $task = Task::find($args['id']);
        if (!$task) {
            return $response->withStatus(404);
        }
        $task->delete();
        return $response->withStatus(204);
    }
}
