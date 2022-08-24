<?php

namespace SET\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

class StoreUpdateInventoryRequest extends Request
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
        // Get the ID number from the route if an update request is passed through the request validation
        if(request()->route('inventory') && request()->route()->getName() == 'inventory.update'){
            $id = request()->route('inventory');
            return [
                'material_control_number'    => [
                    'required',
                    Rule::unique('inventory')->ignore($id),
                ],
                'date_created'    => 'required',
                'date_into_inventory'   => 'required',
                'received_from' => 'required',
                'classification_id' => 'required',
                'type_id' => 'required',
                'description' => 'required',
                'room_id' => 'required',
            ];
        } else {
            return [
                'material_control_number'    => 'required|unique:inventory',
                'date_created'    => 'required',
                'date_into_inventory'   => 'required',
                'received_from' => 'required',
                'classification_id' => 'required',
                'type_id' => 'required',
                'description' => 'required',
                'room_id' => 'required',
            ];
        }
    }
}
