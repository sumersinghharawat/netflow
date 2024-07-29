<?php

namespace App\Http\Controllers;

use App\Imports\EcomRegisterImport;
use App\Imports\UserRegisterImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class BulkRegisterController extends CoreInfController
{
    public function index()
    {
        $signupSettings = $this->signupSettings();
        if (!$signupSettings['registration_allowed'] && auth()->user()->user_type != 'admin' && auth()->user()->user_type != 'employee') {
            return redirect()->back()->with('error', 'registration not allowed');
        }

        $moduleStatus = $this->moduleStatus();

        return view('register.bulkRegister', compact('moduleStatus'));
    }

    public function store(Request $request)
    {
        if(session()->get('is_preset')){
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }
        
        try {
            $request->validate([
                'file' => 'required|mimes:xls,xlsx',
            ]);
            $moduleStatus = $this->moduleStatus();

            if ($moduleStatus->ecom_status) {
                $data = Excel::import(new EcomRegisterImport, $request->file('file'), null);
            } else {
                $data = Excel::import(new UserRegisterImport, $request->file('file'), null);
            }
            if ($data) {
                return redirect()->back()->with('success', 'registartion completed succesfully');
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            return redirect()->back()->withErrors($failures);
        } catch (Throwable $th) {
            $msg = $th->getMessage();

            return redirect()->back()->withErrors($msg);
        }
    }
}
