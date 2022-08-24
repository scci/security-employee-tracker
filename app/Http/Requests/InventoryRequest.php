<?php

namespace SET\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class InventoryRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return in_array(Auth::user()->username, Config::get('auth.FSO'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
