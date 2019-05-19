<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'task' => 'required|string|max:250'
        ];
    }


    /**
     * Customizing error messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'task.required' => 'Task name is required',
            'task.string' => 'Task name must be string',
            'task.max' => 'Task name should consist of up to 250 letters'
        ];
    }
}
