<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CompanyLogos as RequestsCompanyLogos;
use App\Http\Requests\CompanyProfile as RequestsCompanyProfile;

class CompanyProfileController extends CoreInfController
{
    use UploadTraits;

    public function index()
    {
        $profile = CompanyProfile::first();

        return view('admin.settings.companyProfile.index', compact('profile'));
    }

    public function update(RequestsCompanyProfile $request, $id)
    {
        try {
            $profile = CompanyProfile::find($id);
            $profile->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return redirect(route('company.profile'))->with('success', 'company profile updated succesfully');
        } catch (Throwable $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect(route('company.profile'))->with('error', $e->getMessage());
        }
    }
    public function deleteImage(Request $request)
    {
        try {
            $type = $request->type;
            $profile = CompanyProfile::find(1);
            $image = $profile[$type];
            if (File::exists($image)) {
                File::delete($image);
            }
            $profile->update([
                $type => NULL,
            ]);

            return redirect(route('company.profile'))->with('success', 'company profile updated succesfully');
        } catch (Throwable $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect(route('company.profile'))->with('error', $e->getMessage());
        }
    }
    public function updateOld(RequestsCompanyProfile $request, $id)
    {
        try {
            $profile = CompanyProfile::find($id);
            if ($request->hasFile('login_logo')) {
                $file = $request->login_logo;
                $login_logo = $this->loginLogo($file, $profile, 'logo', 'uploads/logos');
            } else {
                $login_logo = $profile->login_logo;
            }
            if ($request->hasFile('logo')) {
                $file = $request->logo;
                $logo = $this->loginLogo($file, $profile, 'dark', 'uploads/logos');
            } else {
                $logo = $profile->logo;
            }

            if ($request->hasFile('shrink_logo')) {
                $file = $request->shrink_logo;
                $shrink_logo = $this->loginLogo($file, $profile, 'shrink_logo', 'uploads/logos');
            } else {
                $shrink_logo = $profile->logo_shrink;
            }

            if ($request->hasFile('favicon')) {
                $file = $request->favicon;
                $favicon = $this->loginLogo($file, $profile, 'fav', 'uploads/logos');
            } else {
                $favicon = $profile->favicon;
            }
            $profile->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'logo' => $logo,
                'login_logo' => $login_logo,
                'logo_shrink' => $shrink_logo,
                'favicon' => $favicon,

            ]);

            return redirect(route('company.profile'))->with('success', 'company profile updated succesfully');
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('company.profile'))->with('error', $e->getMessage());
        }
    }

    public function setTheme(Request $request)
    {
        $profile    = CompanyProfile::first();
        $profile->theme = $request->theme;
        $profile->save();
        $prefix = session()->get('prefix');
        Cache::forever("{$prefix}_companyProfile", $profile);
        return true;
    }

    public function updateLogo(RequestsCompanyLogos $request, $id)
    {
        $message = '';
        try {
            $profile = CompanyProfile::find($id);
            // if ($request->logoType == 'login_logo') {
            //     if($request->hasFile('file')){
            //         $file = $request->file('file');
            //     }
            //     $login_logo = $this->loginLogo($file, $profile, 'logo', 'uploads/logos');
            //     $message = 'Logo for light background';
            // } else {
            //     $login_logo = $profile->login_logo;
            // }
            if ($request->logoType == 'logo') {
                if($request->hasFile('file')){
                    $file = $request->file('file');
                }
                $logo['logo'] = $this->loginLogo($file, $profile, 'logo', 'uploads/logos');
                $message = 'Logo for light background';
            } elseif($request->logoType == 'logo_dark'){
                if($request->hasFile('file')){
                    $file = $request->file('file');
                }
                $logo['logo_dark'] = $this->loginLogo($file, $profile, 'logo_dark', 'uploads/logos');
                $message = 'Logo for dark background';
            } elseif ($request->logoType == 'shrink_logo') {
                if($request->hasFile('file')){
                    $file = $request->file('file');
                }
                $logo['logo_shrink'] = $this->loginLogo($file, $profile, 'logo_shrink', 'uploads/logos');
                $message = 'Logo for Collapsed Sidebar';
            } elseif ($request->logoType == 'favicon') {
                if($request->hasFile('file')){
                    $file = $request->file('file');
                }
                $logo['favicon'] = $this->loginLogo($file, $profile, 'fav', 'uploads/logos');
                $message = 'Favicon';
            }
            $profile->update($logo);
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => $message.' updated succesfully',
                ], 200);
            }
            return redirect(route('company.profile'))->with('success', $message.' updated succesfully');
        } catch (Throwable $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect(route('company.profile'))->with('error', $e->getMessage());
        }
    }
}
