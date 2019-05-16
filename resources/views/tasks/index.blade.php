@extends('layouts.app')

@section('content')
    <div class="container">

        <div style="display: none" class="alert alert-danger" id="axios-error" role="alert">

        </div>

        <div class="row">
            <a class="btn btn-primary" href="{{route('tasks.create')}}">
                Add Task
            </a>
            <table class="table" style="background: #fff">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tasks as $task)
                    <tr id="task_{{$task->id}}" >
                        <td>{{$task->id}}</td>
                        <td>{{$task->task}}</td>
                        <td>
                            <button data-url="{{route('tasks.finish', ['id' => $task->id])}}" class="btn tasks-finish-button {{$task->finished? 'btn-success':'btn-primary'}}">
                                {{$task->finished?'Finished':'Finish'}}
                            </button>
                        </td>
                        <td>
                            <a href="{{route('tasks.edit', ['id' => $task->id])}}" class="btn btn-primary">
                                Edit
                            </a>
                        </td>
                        <td>
                            <form method="POST" action="{{route('tasks.destroy', ['id' => $task->id])}}">
                                @method('DELETE')
                                @csrf
                                <button class="btn btn-danger" type="submit">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{$tasks->links()}}
    </div>
@endsection