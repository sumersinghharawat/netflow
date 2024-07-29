<?php

namespace App\Http\Requests;

use App\Http\Controllers\CoreInfController;
use App\Models\OCProduct;
use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;

class updateRoiCommissionRequest extends FormRequest
{
    public $packages;
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $coreinf = new CoreInfController;
        $moduleStatus = $coreinf->moduleStatus();
        if ($moduleStatus->ecom_status) {
            $packages = OCProduct::select('product_id as id', 'model as name')->where('status', 1)->where('package_type', 'registration')->get();
        } else {
            $packages = Package::ActiveRegPackage()->select('id', 'name')->get();
        }
        $rules = collect($packages)->flatMap(function ($package) {
            return [
                'roi' . $package->id => 'required|numeric|gt:0',
                'days' . $package->id => 'required|numeric|gt:0'
            ];
        })->toArray();
        $this->packages = $packages;

        return $rules;
    }

    public function messages()
    {
        $messages = collect($this->packages)->flatMap(function ($package) {
            return [
                "roi$package->id" . '.required' => 'ROI is required for package ' . $package->name,
                "roi$package->id" . '.numeric' => 'ROI should be a numeric value for package ' . $package->name,
                "roi$package->id" . '.gte' => 'ROI should be greater than or equal to 0 for package ' . $package->name,
                "days$package->id" . '.required' => 'Days is required for package ' . $package->name,
                "days$package->id" . '.numeric' => 'Days should be a numeric value for package ' . $package->name,
                "days$package->id" . '.gte' => 'Days should be greater than or equal to 0 for package ' . $package->name
            ];
        })->toArray();
        return $messages;
    }
}
