<?php

namespace App\Http\Requests;

use App\Models\Rank;
use Illuminate\Foundation\Http\FormRequest;

class SalesRankRequest extends FormRequest
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
        $ranks = Rank::Active()->get();
        $data = [];
        foreach ($ranks as $key => $rank) {
            $data += [
                $rank->name.'.*' => 'required|numeric|lte:100|gte:0',
            ];
        }

        return $data;
        // dd($data);
        // return [
        //
        // ];
    }
}
