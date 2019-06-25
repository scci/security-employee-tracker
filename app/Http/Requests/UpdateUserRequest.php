<?php

namespace SET\Http\Requests;

use Illuminate\Support\Facades\Gate;

class UpdateUserRequest extends Request
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
            'cont_eval_date' => 'required_if:cont_eval,1',
        ];
    }

    public function messages()
    {
        return [
            'cont_eval_date.required_if'  => 'You must select a continuous evaluation date if continuous evaluation is set to Yes',
        ];
    }
}
