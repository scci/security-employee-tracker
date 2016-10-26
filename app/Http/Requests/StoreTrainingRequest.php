<?php

namespace SET\Http\Requests;

use Illuminate\Support\Facades\Gate;

class StoreTrainingRequest extends Request
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
            'name' => 'required',
            'renews_in' => 'integer',
            'location' => 'required_if:assign,meeting',
            'start' => 'required_if:assign,meeting|date',
            'end' => 'required_if:assign,meeting|date',
            'due_date' => 'required_if:assign,due_date|date'
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
            'location' => 'A location is required for scheduling a meeting.',
            'start' => 'A valid start time is required for scheduling a meeting.',
            'end' => 'A valid end time is required for scheduling a meeting.',
            'due_date' => 'A valid due date is required for scheduling a due date assignment.'
        ];
    }
}
