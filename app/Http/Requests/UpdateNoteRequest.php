<?php

namespace SET\Http\Requests;

use Illuminate\Support\Facades\Gate;
use SET\Note;

/**
 * Class UpdateNoteRequest
 * @package SET\Http\Requests
 */
class UpdateNoteRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $noteID = $this->route('note');
        return Gate::allows('update_record', Note::findOrFail($noteID));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
        ];
    }
}
