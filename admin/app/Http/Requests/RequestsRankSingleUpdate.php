<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestsRankSingleUpdate extends FormRequest
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
            'rankName' => 'required|max:32',
            'rank_id' => 'nullable',
            'joineePackId' => 'nullable|numeric',
            'package' => 'required|numeric',
            'rank_bonus' => 'required|numeric',
            'rankColor' => 'required',

        ];
    }
}
