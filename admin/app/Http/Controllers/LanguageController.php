<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class LanguageController extends CoreInfController
{
    public function index()
    {
        $languages = Language::get();

        return view('advancedSettings.language.index', compact('languages'));
    }

    public function updateLanguage(Request $request)
    {
        try {
            Language::Active()->first()->update(['status' => 0]);

            Language::where('code', $request->language)->update(['status' => 1]);
            App::setLocale($request->language);
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Language Updated Successfully',
                    'data' => $request->language,
                ], 200);
            }
        } catch (Throwable $th) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => $th,

                ], 400);
            }
        }
    }

    public function edit(Request $request, $language)
    {
        $language = Language::find($language);
        if($language->checkIsDefault()) {
            throw ValidationException::withMessages([
                'language' => __('common.cant_disable_default_lang'),
            ]);
        }
        $set = ($language->status == 1) ? 0 : 1;
        $language->update(['status' => $set]);
        User::where('default_lang', $language->id)->update(['default_lang' => null]);

        return redirect()->back()->with('success', 'Language status successfully changed.');
    }

    public function setDefault(Request $request, $language)
    {
        Language::where('default', 1)->update(['default' => 0]);
        $language = Language::where('id', $language)->update(['default' => 1]);

        return redirect()->back()->with('success', 'Language status successfully changed.');
    }

    public function setUserLang(Request $request)
    {

        if(session()->get('is_preset')){
            return response()->json([
                'status' => 'error',
                'errors' => "You don't have permission By using Preset Demo",
            ], 401);
        }
        $user = auth()->user();
        $user->default_lang = $request->language;
        if ($user->save()) {
            return response()->json([
                'status' => true,
                'data' => '',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data' => '',
            ], 404);
        }
    }
}
