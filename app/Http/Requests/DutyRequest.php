<?php

namespace SET\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class DutyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'   => 'required|max:255',
            'users'  => 'required_without:groups',
            'groups' => 'required_without:users',
        ];
    }

    public function messages()
    {
        return [
            'users.required_without'  => 'You must have at least one user.',
            'groups.required_without' => 'You must have at least one group.',
        ];
    }
}
