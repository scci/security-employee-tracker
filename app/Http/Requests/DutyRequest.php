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
            'name' => 'required|max:255',
            'users' => 'required_unless:has_groups,1',
            'groups' => 'required_if:has_groups,1'
        ];
    }

    public function messages()
    {
        return [
            'users.required_unless' => 'You must have at least one user.',
            'groups.required_if' => 'You must have at least one group.'
        ];
    }
}
