<?php

namespace SET\Http\Requests;

use Illuminate\Support\Facades\Gate;

class DutySwapRequest extends Request
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
            'id'      => 'regex:/\d*,\d*/',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'regex' => 'Please select two different users to swap.',
        ];
    }
}
