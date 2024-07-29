<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class RequestsDocumentAddnew extends FormRequest
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
    public function rules(Request $request)
    {
        // dd($this->all());
        if ($request->category == 1) {
            $condition = 'required|mimes:pdf,ppt,docx,doc,xls,xlsx,ods,odt';
        } elseif ($request->category == 2) {
            $condition = 'required|mimes:png,jpeg,jpg,gif,ico';
        } elseif ($request->category == 3) {
            $condition = 'required|mimes:mp4,mov,avi,flv,mpg,wmv,3gp,rm';
        }
        $rules = [
            'category' => 'required',
            'title' => 'required',
            'file' => $condition,
            'description' => 'required',
        ];

        return $rules;
    }
}
