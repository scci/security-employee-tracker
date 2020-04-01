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
            'accessTokens.cac_issue_date' => 'required_if:accessTokens.cac_issued,1',
            'accessTokens.sipr_issue_date' => 'required_if:accessTokens.sipr_issued,1',
        ];
    }

    public function messages()
    {
        return [
            'cont_eval_date.required_if'  => 'You must select a continuous evaluation date if continuous evaluation is set to Yes',
            'accessTokens.cac_issue_date.required_if'  => 'You must select an issued date if CAC Issued is set to Yes',
            'accessTokens.sipr_issue_date.required_if'  => 'You must select an issued date if SIPR TOKEN Issued is set to Yes',
        ];
    }
}
