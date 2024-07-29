<?php

namespace App\Http\Requests;

use App\Models\RankConfiguration;
use App\Rules\RegisterPack;
use Illuminate\Foundation\Http\FormRequest;

class RankRequest extends FormRequest
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
        // dd($this->rank);
        $activeConfig = RankConfiguration::Active()->get();
        $rule = collect([
            'color' => 'required|string',
            'name' => 'required|string|unique:ranks,name,'.($this->rank) ?: $this->rank->id,
            'image' => 'sometimes|required|file|size:2080',
            'image' => 'mimes:jpeg,png,bmp',
            'priority' => 'sometimes|integer|unique:ranks,rank_order,' .($this->rank) ?: $this->rank->id,
        ]);
        if ($activeConfig->contains('slug', 'joiner-package')) {
            $rule->put('package_id', ['required', new RegisterPack]);
            $rule->put('commission', ['required', 'Numeric']);
        } else {
            $request = collect($this->request->all())->except(['_token', '_method', 'color', 'name', 'downlineRank', 'packageId']);
            $rule = $request->mapWithKeys(fn ($req, $key) => $rule->put($key, 'required|Numeric'));

            ($activeConfig->contains('slug', 'downline-rank-count')) ? $rule->put('downlineRank.*', 'required') : '';
            ($activeConfig->contains('slug', 'downline-package-count')) ? $rule->put('packageId.*', 'required') : '';
        }

        return $rule->toArray();
    }

    public function messages()
    {
        return [
            'downlineRank.*.required' => 'Rank count field is required.',
            'packageId.*.required' => 'Package count field is required.',
        ];
    }
}
