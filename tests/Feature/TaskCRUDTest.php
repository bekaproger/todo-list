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

    protected $login_url;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->task = factory(Task::class)->create(['user_id' => $this->user->id]);
        $this->url = route('tasks.index') ;
        $this->login_url = route('login');
    }


    public function test_user_can_view_tasks()
    {
        $res = $this->actingAs($this->user)->get($this->url);

        $res->assertOk();
        //tasks-table is the class name of the tasks table in the view
        $res->assertSee('tasks-table');
    }

    public function test_unauthorized_user_cant_review_tasks_and_make_get_requests()
    {
        $create_url = route('tasks.create');
        $edit_url = route('tasks.edit', ['id' => $this->task->id]);

        $index = $this->get($this->url);
        $create = $this->get($create_url);
        $edit = $this->get($edit_url);

        //try to access all tasks
        $index->assertStatus(302);
        $index->assertRedirect($this->login_url);

        //try to access create form
        $create->assertStatus(302);
        $create->assertRedirect($this->login_url);

        //try to access edit form
        $edit->assertRedirect($this->login_url);
        $edit->assertStatus(302);
    }

    public function test_unauthorized_user_cant_make_post_request()
    {
        $task_id = $this->task->id;
        $create_url = route('tasks.store');
        $update_url = route('tasks.update', ['id' => $task_id]);
        $finish_url = route('tasks.finish', ['id' => $task_id]);
        $delete_url = route('tasks.destroy', ['id' => $task_id]) ;
        //we use this data when we try to create or update a task
        $data = [
            'task' => 'Task'
        ];
        //try to create task
        $create = $this->post($create_url, $data);
        //try to edit task
        $update = $this->put($update_url, $data);
        //try to finish
        $finish = $this->post($finish_url);
        //try to delete task
        $delete = $this->delete($delete_url);

        $create->assertRedirect($this->login_url);
        $update->assertRedirect($this->login_url);
        $finish->assertRedirect($this->login_url);
        $delete->assertRedirect($this->login_url);
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

        $url = route('tasks.update', ['id' => $this->task->id]);
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
        $url = route('tasks.update', ['id' => $this->task->id]);
        $res = $this->actingAs($this->user)->put($url, $data);
        //if no task is supplied task error message returned
        $res->assertSessionHasErrors(['task']);
    }

    public function test_user_can_finish_task()
    {
        $url = route('tasks.finish', ['id'=> $this->task->id]);

        $res = $this->actingAs($this->user)->post($url);

        $res->assertStatus(201);

        //get the finished task
        $finished_task = Task::find($this->task->id);

        //$this->task->finished is false since we haven't updated it yet
        //we check after finishing the task the old task's finished property is opposite of finished task's
        $this->assertEquals($finished_task->finished, !$this->task->finished);
    }

    public function test_user_cant_finish_not_exiting_task()
    {
        $task_id = 'non_existing_id';
        $url = route('tasks.finish', ['id'=> $task_id]);
        //try to finish non exiting task
        $res = $this->actingAs($this->user)->post($url);
        $res->assertNotFound();
    }

    public function test_user_can_delete_task()
    {
        $url = route('tasks.destroy', ['id' => $this->task->id]);
        $res = $this->actingAs($this->user)->delete($url);

        $res->assertRedirect($this->url);

        //check the database that this particular task is missing
        $this->assertDatabaseMissing('tasks', [ 'id' =>  $this->task->id]);
    }

}
