<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonolineConfigUpdateRequest;
use App\Jobs\UserActivityJob;
use App\Models\MonolineConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MonolineConfigController extends Controller
{
    public function index()
    {
        $config = MonolineConfig::first();
        $currency = currencySymbol();

        return view('admin.settings.monoline.index', compact('config', 'currency'));
    }

    public function configUpdate(MonolineConfigUpdateRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $config = MonolineConfig::findOrFail($id);
            $config->fill($validated);
            $config->push();
            $user = auth()->user();
            $prefix = session()->get('prefix');

            UserActivityJob::dispatch(
                $user->id,
                json_encode($validated),
                'configuration changed',
                $user->username . ' changed configuration ',
                "{$prefix}_",
                $user->user_type
            );
            return redirect(route('monoline.index'))->with('success', trans('settings.config_success'));
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function treeIconUpdate(Request $request)
    {
        try {
            $config = MonolineConfig::first();
            $image = $request->file('reentry_tree_icon');
            $filename = time() . "." . $image->getClientOriginalExtension();
            $folder = "tree-icon";
            if (Storage::putFileAs($folder, $image, $filename)) {
                $host = request()->getSchemeAndHttpHost();
                $config->tree_icon =  $host . "/storage/$folder/$filename";
                $config->push();
            }
            return back()
                ->with('success', 'Image uploaded successfully.');
        } catch (\Throwable $th) {
            return back()
            ->with('error', $th->getMessage());
        }
    }
}
