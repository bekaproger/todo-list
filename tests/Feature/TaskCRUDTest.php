<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Task;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskCRUDTest extends TestCase
{

    use RefreshDatabase;

    protected $user;

    protected $url;

    protected $task;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->task = factory(Task::class)->create(['user_id' => $this->user->id]);
        $this->url = '/tasks/';
    }


    public function test_user_can_view_tasks()
    {
        $res = $this->actingAs($this->user)->get($this->url);

        $res->assertOk();
        //tasks-table is the class name of the tasks table in the view
        $res->assertSee('tasks-table');
    }

    public function test_unauthorized_user_cant_review_tasks()
    {
        $res = $this->get($this->url);

        $res->assertStatus(302);
        $res->assertRedirect('/login');
    }

    public function test_user_can_create_task()
    {
        $data = [
            'task' => 'Specific task'
        ];

        $res = $this->actingAs($this->user)->post( '/tasks' ,$data);

        $res->assertRedirect($this->url);

        //getting new created task
        $task = $this->user->userTasks()->where('task', $data['task'])->first();

        $this->assertNotNull($task);

        return $task;
    }

    public function test_user_cant_create_text_without_task_given()
    {
        $data = [];
        $res = $this->actingAs($this->user)->post( $this->url, $data);
        //if no task is supplied task error message returned
        $res->assertSessionHasErrors(['task']);
    }

    public function test_user_can_update_task()
    {
        $data = [
            'task' => 'Task edited'
        ];

        $url = $this->url . $this->task->id;
        $res = $this->actingAs($this->user)->put($url, $data);

        $res->assertRedirect($this->url);
        //Get the edited task
        $task = Task::find($this->task->id);

        $this->assertEquals($data['task'], $task->task);
        return $task;
    }

    public function test_user_cant_update_test_without_task_given()
    {
        $data = [];
        $url = $this->url . $this->task->id;
        $res = $this->actingAs($this->user)->put($url, $data);
        //if no task is supplied task error message returned
        $res->assertSessionHasErrors(['task']);
    }

    public function test_user_can_finish_task()
    {
        $url = $this->url . $this->task->id . '/finish';

        $res = $this->actingAs($this->user)->post($url);

        $res->assertStatus(201);

        //get the finished task
        $finished_task = Task::find($this->task->id);

        //$this->task->finished is false since we haven't updated it yet
        //we check after finishing the task the old task's finished property is opposite of finished task's
        $this->assertEquals($finished_task->finished, !$this->task->finished);
    }

    public function text_user_can_delete_task()
    {
        $res = $this->actingAs($this->user)->post($this->url . $this->task->id);

        $res->assertRedirect($this->url);

        //check the database that this particular task is missing
        $this->assertDatabaseMissing('tasks', [ 'id' =>  $this->task->id]);
    }

}
