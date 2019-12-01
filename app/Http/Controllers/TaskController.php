<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use App\Http\Requests\TaskPostRequest;
use App\Services\TaskService;

class TaskController extends Controller
{

    protected $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = auth()->user()->userTasks()->with('taskUser')->paginate(50);
        return view('tasks.index', ['tasks' => $tasks]);
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskPostRequest $request)
    {
        
        $this->service->createTask($request->task);

        return redirect()->route('tasks.index');
    }

    /**
     * Show the form for editing the specified task.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        return view('tasks.edit', ['task' => $task]);
    }

    /**
     * Update the specified task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(TaskPostRequest $request, Task $task)
    {
        $task->update($request->only(['task']));

        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified task from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index');
    }

    /**
    * Finish the task
    *
    * @param string|integer $id
    * @return \Illuminate\Http\Response
    */
    public function finish($id)
    {
        try{
            $this->service->finishTask($id);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), $e->getCode());
        }
        return response()->json('', 201);
    }
}
