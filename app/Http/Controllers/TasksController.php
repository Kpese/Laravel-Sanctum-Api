<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    use HttpResponses;

    public function index()
    {
        return TaskResource::collection(
            Task::where('user_id', Auth::user()->id)->get()
        );
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string',
        ]);

        $data['user_id'] = Auth::user()->id;
       $task = Task::create($data);

       return new TaskResource($task);
    }


    public function show(Task $task)
    {
       if ($task->user_id !== Auth::user()->id){
        return $this->error('', 'You are not authorized to make this request', 403);
       }
        return new TaskResource($task);
    }

    public function update(Task $task)
    {
        $data = request()->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'priority' => 'string',
        ]);

        $data['user_id'] = Auth::user()->id;

         if ($task->user_id !== Auth::user()->id){
        return $this->error('', 'You are not authorized to make this request', 403);
       }

       $task->update($data);

       return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::user()->id){
            return $this->error('', 'You are not authorized to make this request', 403);
           }

    $task->delete();
    return response(null, 204);
    }
}
