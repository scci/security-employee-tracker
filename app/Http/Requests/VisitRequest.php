<?php

namespace SET\Http\Requests;

use Illuminate\Support\Facades\Gate;

/**
 * Class VisitRequest
 */
class VisitRequest extends Request {
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
            'smo_code' => 'required',
            'expiration_date' => 'required',
        ];
    }
}

?>
