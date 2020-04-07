<?php

namespace SET\Http\Requests;

use Illuminate\Support\Facades\Gate;

class StoreUserRequest extends Request
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
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users,email',
            'accessTokens.cac_issue_date' => 'required_if:accessTokens.cac_issued,1',
            'accessTokens.cac_expiration_date' => 'required_if:accessTokens.cac_issued,1',
            'accessTokens.sipr_issue_date' => 'required_if:accessTokens.sipr_issued,1',
            'accessTokens.sipr_expiration_date' => 'required_if:accessTokens.sipr_issued,1',
            'cont_eval_date' => 'required_if:cont_eval,1',
        ];
    }

    public function messages()
    {
        return [
            'cont_eval_date.required_if'  => 'You must select a Continuous Evaluation date if Continuous Evaluation is set to Yes',
            'accessTokens.cac_issue_date.required_if'  => 'You must select a CAC Issued Date if CAC Issued is set to Yes',
            'accessTokens.cac_expiration_date.required_if'  => 'You must select a CAC Expiration Date if CAC Issued is set to Yes',
            'accessTokens.sipr_issue_date.required_if'  => 'You must select a SIPR TOKEN Issued Date if SIPR TOKEN Issued is set to Yes',
            'accessTokens.sipr_expiration_date.required_if'  => 'You must select a SIPR TOKEN Expiration Date if SIPR TOKEN Issued is set to Yes',
        ];
    }
}
