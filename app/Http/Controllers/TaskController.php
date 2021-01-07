<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TaskResource::collection(Task::paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\CreateTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTaskRequest $request)
    {
        try {
            $task = Task::create($request->validated());

            if (!$task)
                return response()->json(['message' => 'Error Creating Task'], 500);

            return TaskResource::make($task)
                ->additional(['message' => 'Task Created'])
                ->response()
                ->setStatusCode(201);

        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'Error Creating Task'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return TaskResource::make($task)->additional(['message' => 'Task Found']);;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\UpdateTaskRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();

        $metadata = $request->except('title', 'body', 'is_completed');

        try {
            $task->update([
                'title' => $data['title'] ?? $task['title'],
                'body' => $data['body'] ?? $task['body'],
                'is_completed' => $data['is_completed'] ?? $task['is_completed'],
                'metadata' => array_merge($task['metadata'] ?? [], $metadata),
            ]);

            return TaskResource::make($task)->additional(['message' => 'Task Updated']);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'Error Updating Task'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();

            return response()->json(['message' => 'Task Deleted'], 200);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'Error Deleting Task'], 500);
        }
    }
}
