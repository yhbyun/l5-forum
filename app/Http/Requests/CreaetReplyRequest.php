<?php

namespace App\Http\Requests;

class CreateReplyRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body'     => 'required|min:2',
            'user_id'  => 'required|numeric',
            'topic_id' => 'required|numeric',
        ];
    }
}
