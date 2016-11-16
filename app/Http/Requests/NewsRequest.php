<?php

namespace SET\Http\Requests;

use Illuminate\Support\Facades\Gate;

class NewsRequest extends Request
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
            'title'        => 'required',
            'description'  => 'required',
            'publish_date' => 'required',
            'expire_date'  => 'after:publish_date',
        ];
    }
}
