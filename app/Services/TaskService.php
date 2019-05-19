<?php 

namespace App\Services;

use App\Task;

class TaskService
{

	public function createTask(string $task)
	{
		$task = Task::create(['task' => $task, 'user_id' => auth()->id()]);

		return $task;
	}

	public function finishTask($id)
	{
	    $task = Task::find($id);

        //if the task is not found return error response
        if(!$task){
            throw new \Exception('Task not found', 404);
        }
		$task->finished = !$task->finished;
		$task->save();
	}
}