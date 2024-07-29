<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Language;
use App\Models\SignupField;
use Illuminate\Http\Request;
use App\Models\CustomfieldLang;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RequestsSignUpFieldStore;
use App\Http\Requests\RequestsCustomfieldUpdate;

class SignupFieldController extends CoreInfController
{
    public function index()
    {
        $signupField = SignupField::orderBy('sort_order', 'ASC')->NotCustom()->get();
        $customField = SignupField::orderBy('sort_order', 'ASC')->Custom()->get();
        $languages   = Language::Active()->get();
        return view('admin.settings.advancedSettings.customField.index', compact('signupField', 'customField', 'languages'));
    }

    public function store(RequestsSignUpFieldStore $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $sortorder = SignupField::max('sort_order');
            $defaultLang = Language::Default()->first();
            $field = SignupField::create([
                'name' => $request->name[$defaultLang->id],
                'type' => $request->type,
                'sort_order' => $sortorder + 1,
                'status' => $request->status,
                'required' => $request->required,
                'editable' => 1,
                'is_custom' => 1
            ]);
            foreach ($request->name as $key => $value) {
                $fieldLang = new CustomfieldLang;
                $fieldLang->customfield_id = $field->id;
                $fieldLang->language_id = $key;
                $fieldLang->value = $value;
                $fieldLang->save();
            }
            DB::commit();
            $customField = SignupField::orderBy('sort_order', 'ASC')->Custom()->get();
            $view = view('admin.settings.advancedSettings.customField.addSignupfield', compact('customField'));

            return response()->json([
                'message' => trans('common.signup_field_updated_succesfully'),
                'data' => $view->render(),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect(route('signupField'))
                ->with('error', $e->getMessage());
        }
    }
    public function signupFieldUpdate(RequestsCustomfieldUpdate $request)
    {
        $fields = SignupField::NotCustom()->get();
        foreach ($fields as $item) {
            if (in_array($item->name, ['first_name', 'email', 'mobile', 'date_of_birth'])) continue;
            $item->name         = $request->field[$item->id]['field_name'] ?? $item->name;
            $item->type         = $request->field[$item->id]['field_type'] ?? $item->type;
            $item->sort_order   = $request->field[$item->id]['sortorder'] ?? $item->sort_order;
            $item->status       = $request->field[$item->id]['is_enabled'] ?? 0;
            $item->required     = $request->field[$item->id]['is_required'] ?? 0;
            $item->push();
        }
        return redirect(route('signupField'))->with('success', trans('common.signup_field_updated_succesfully'));
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $signupField = SignupField::find($id);
            if ($signupField->editable == 1) {
                $signupField->delete();
                if ($request->ajax()) {
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message' => trans('common.custom_field_deleted_succesfully'),
                    ]);
                } else {
                    DB::commit();
                    return redirect(route('signupField'))->with('success', trans('common.custom_field_deleted_succesfully'));
                }
            } else {
                return abort(403);
            }
        } catch (Throwable $th) {
            if ($request->ajax()) {
                DB::rollBack();
                return response()->json([
                    'message' => $th,
                ], 400);
            } else {
                DB::rollBack();
                return redirect(route('signupField'))->with('error', $th->getMessage());
            }
        }
    }

    public function destroy1($id)
    {
        try {
            $signupField = SignupField::find($id);
            if ($signupField->editable == 1) {
                $signupField->delete();

                return response()->json([
                    'message' => 'Signup field deleted',

                ], 200);
                //  return redirect(route('signupField'))->with('success', 'signup field deleted ');
            } else {
                return abort(403);
            }
        } catch (\Exception $e) {
            return redirect(route('signupField'))
                ->with('error', $e->getMessage());
        }
    }
    public function newcustomFieldUpdate(RequestsCustomfieldUpdate $request, $id)
    {
        try {
            $field = SignupField::with('customFieldLang.language')->find($id);
            foreach ($field->customFieldLang as  $item) {
                $fieldName =  $request->name[$item->language->id];
                $item->value = $fieldName;
                $item->push();

                if ($item->language->default)
                    $field->name = $fieldName;
            }

            $field->status     = $request->status;
            $field->required   = $request->required;
            $field->sort_order = $request->sort_order;
            $field->push();

            return response()->json([
                'status'  => true,
                'message' => trans('common.custom_field_updated_succesfully')
            ]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    public function edit($id)
    {
        $signupField = SignupField::with('customFieldLang')->find($id);
        $languages   = Language::Active()->get();

        $view = view('admin.settings.advancedSettings.customField.editCustomField', compact('signupField', 'languages'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ]);
    }
}
