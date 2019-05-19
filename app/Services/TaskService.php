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

	public function finishTask(Task $task)
	{
		$task->finished = !$task->finished;
		$task->save();
	}
}