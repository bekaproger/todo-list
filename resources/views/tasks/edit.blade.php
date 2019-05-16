@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Edit task
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('tasks.update', ['id' => $task->id]) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="task" class="col-md-4 col-form-label text-md-right">Task</label>

                                <div class="col-md-6">
                                    <input id="task" type="text" class="form-control @error('task') is-invalid @enderror" name="task" value="{{$task->task}}" required autocomplete="task" autofocus>

                                    @error('task')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection