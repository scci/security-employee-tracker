<?php

namespace SET\Http\Requests;

use Illuminate\Support\Facades\Gate;

class AssignTrainingRequest extends Request
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
            'users'    => 'required_without:groups',
            'groups'   => 'required_without:users',
            'due_date' => 'required',
        ];
    }
}
