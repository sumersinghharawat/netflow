<?php

namespace App\Http\Requests;

use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;

class RequestsMembershippackUpdate extends FormRequest
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
        $membership_packages = Package::all();
        $data = [];
        foreach ($membership_packages as $membership_package) {
            $data += [
                'membership_pack_image'.$membership_package->id => 'nullable|image|mimes:png,jpeg,jpg',
            ];
        }

        return $data;
    }
}
