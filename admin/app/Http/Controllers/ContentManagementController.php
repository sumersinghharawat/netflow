<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Letterconfig;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use App\Models\ReplicaBanner;
use App\Models\ReplicaContent;
use App\Models\TermsAndCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RequestsTermnCond;
use App\Http\Requests\RequestsUpdateBanner;
use App\Http\Requests\RequestsWelcomeLetter;
use App\Http\Requests\RequestsReplicaSiteUpdate;
use Illuminate\Support\Facades\Storage;


class ContentManagementController extends CoreInfController
{
    use  UploadTraits;

    public function index()
    {
        $moduleStatus = $this->moduleStatus();
        if ($moduleStatus->multilang_status) {
            $languages = Language::Active()->get();
        } else {
            $languages = Language::where('default', true)->get();
        }
        $welcomeletter = Letterconfig::WhereHas('language', fn ($query) => $query->Active())->get();
        if (!$moduleStatus->multilang_status) {
            $welcomeletter = Letterconfig::WhereHas('language', fn ($query) => $query->where('code', 'en'))->get();
        }
        $termsandcond = TermsAndCondition::WhereHas('language', fn ($query) =>  $query->Active())->get();
        $default_banner = '';
        $banner = null;
        if ($moduleStatus['replicated_site_status']) {
            $default_banner = ReplicaBanner::where('user_id', null)->where('is_default', 1)->get();
            $banner = ReplicaBanner::where('user_id', Auth::user()->id)->where('is_default', 0)->first();
        }

        return view('admin.settings.contentManagement.index', compact('welcomeletter', 'termsandcond', 'default_banner', 'banner', 'languages', 'moduleStatus'));
    }

    public function updateWelcomeLetter(RequestsWelcomeLetter $request)
    {
        try {
            $welcomeletter = Letterconfig::where('language_id', $request->language_id)->first();
            $welcomeletter->content = $request->content;
            $welcomeletter->save();
        } catch (\Exception $e) {
            return back()
                ->with('warning', $e->getMessage());
        }

        return back()
            ->with('success', 'record updated successfully.');
    }

    public function terms_and_cond()
    {
        $language = Language::where('name_in_english', 'english')->first();
        $termsandcond = TermsAndCondition::where('language_id', $language->id)->first();

        return view('admin.terms-and-condition', compact('termsandcond', 'language'));
    }

    public function updateTerms(RequestsTermnCond $request)
    {
        try {
            $terms = TermsAndCondition::where('language_id', $request->language_id)->first();

            $terms->terms_and_conditions = $request->content;
            $terms->save();
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }

        return back()
            ->with('success', 'record updated successfully.');
    }

    public function replicaSite()
    {
        $moduleStatus = $this->moduleStatus();
        $default_banner = '';

        if ($moduleStatus['replicated_site_status']) {
            $default_banner = ReplicaBanner::first()->banner;
        }
        $banner = '';
        if (ReplicaBanner::where('user_id', Auth::user()->id)->first() != null) {
            $banner = ReplicaBanner::where('user_id', Auth::user()->id)->first()->banner;
        }

        return view('admin.replica-site', compact('default_banner', 'banner'));
    }

    public function replicaSiteEdit($id)
    {
        $replic_default = ReplicaContent::whereNull('user_id')->where('lang_id', $id)->get();
        $replic = ReplicaContent::where('user_id', Auth::user()->id)->where('lang_id', $id)->get();
        return view('admin.settings.contentManagement.replica._edit', compact('replic_default', 'replic', 'id'));
    }

    public function updateBannerDefault(RequestsUpdateBanner $request)
    {
        try {

            if ($request->hasFile('banner')) {
                $existingImages = ReplicaBanner::whereNull('user_id')->where('is_default', 1)->get();
        
                // Iterate through the uploaded files
                foreach ($request->file('banner') as $index => $file) {
                    if ($index < 2) {
                        $model = new ReplicaBanner();
                        $prefix = 'banner' . ($index + 1); // Use 'banner1' for the first file, 'banner2' for the second
                        $folder = 'ReplicaBanner';
        
                        // Upload image
                        $filename = $prefix . '-' . $model->id . time() . '.' . $file->getClientOriginalExtension();
                        if (Storage::putFileAs($folder, $file, $filename)) {
                            $this->unlinkFile($model);

                            $host = request()->getSchemeAndHttpHost();
                            $imagePath = $host . "/storage/$folder/$filename";

                        }
        
                        if ($index < count($existingImages)) {
                            $existingImage = $existingImages[$index];
                            $existingImage->image = $imagePath;
                            $existingImage->save();
                        } else {
                            $newImage = new ReplicaBanner();
                            $newImage->image = $imagePath;
                            $newImage->is_default = 1;
                            $newImage->save();
                        }
                    }
                }
            
                    // Delete any extra entries beyond the first two
                    // ReplicaBanner::whereNull('user_id')->where('is_default', 1)->skip(2)->delete();
                return back()
                    ->with('success', 'Image updated successfully.');
            } else {
                return back()
                    ->with('error', 'Please Upload a Banner.');
            }
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function updateBanner(RequestsUpdateBanner $request, $id = null)
    {
        try {
            //  $id        =    Auth::user()->id;
            if ($request->hasFile('banner')) {
                $ReplicaBanner = ReplicaBanner::findOrFail($id);
                $ReplicaBanner->user_id = Auth::user()->id;
                $ReplicaBanner->image = $request->banner;

                $file = $request->file('banner');
                $model = $ReplicaBanner;
                $prefix = 'banner';
                $folder = 'ReplicaBanner';
                if ($this->singleFileUpload($file, $model, $prefix, $folder)) {
                    return back()
                        ->with('success', 'Image updated successfully.');
                }
            } else {
                return back()
                    ->with('error', 'Please Upload a Banner.');
            }
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function createBanner(Request $request)
    {
        try {
            $ReplicaBanner = new ReplicaBanner;
            $ReplicaBanner->user_id = Auth::user()->id;
            $ReplicaBanner->image = $request->banner;
            $ReplicaBanner->is_default = '0';
            if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                $model = $ReplicaBanner;
                $prefix = 'banner';
                $folder = 'ReplicaBanner';
                if ($this->singleFileUpload($file, $model, $prefix, $folder)) {
                    return back()
                        ->with('success', 'Image uploaded successfully.');
                }
            } else {
                return back()
                    ->with('error', 'Please Upload a Banner.');
            }
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function replicaSiteUpdateDefault(RequestsReplicaSiteUpdate $request)
    {
        try {
            $replic_default = ReplicaContent::whereNull('user_id')->where('lang_id', 1)->get();
            foreach ($replic_default as $key => $value) {
                $v = '';
                $v = $value['key'];
                $v1 = $v . '1';
                $data = $request->$v;
                $data_id = $request->$v1;
                ReplicaContent::find($data_id)->update(['value' => $data]);
            }

            return back()
                ->with('success', 'Replica Default Content updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function replicaSiteUpdate(RequestsReplicaSiteUpdate $request)
    {
        try {
            if (ReplicaContent::where('user_id', Auth::user()->id)->exists()) {
                ReplicaContent::where('user_id', Auth::user()->id)->delete();
            }
            foreach ($request->except(['language', '_token']) as $key => $value) {
                $insertArray['key'] = $key;
                $insertArray['value'] = $value;
                $insertArray['user_id'] = Auth::user()->id;
                $insertArray['lang_id'] = 1;
                ReplicaContent::create($insertArray);
            }

            return back()
                ->with('success', 'Replica Content updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function replicaSiteStore(RequestsReplicaSiteUpdate $request, $id)
    {
        $validatedData  = $request->validated();
        try {
            $replicaContent[0]['key']     = 'home_title1';
            $replicaContent[0]['value']   = $validatedData['home_title1'];
            $replicaContent[0]['lang_id'] = $id;

            $replicaContent[1]['key']     = 'home_title2';
            $replicaContent[1]['value']   = $validatedData['home_title2'];
            $replicaContent[1]['lang_id'] = $id;

            $replicaContent[2]['key']     = 'plan';
            $replicaContent[2]['value']   = $validatedData['plan'];
            $replicaContent[2]['lang_id'] = $id;

            $replicaContent[2]['key']     = 'contact_phone';
            $replicaContent[2]['value']   = $validatedData['contact_phone'];
            $replicaContent[2]['lang_id'] = $id;

            $replicaContent[2]['key']     = 'contact_address';
            $replicaContent[2]['value']   = $validatedData['contact_address'];
            $replicaContent[2]['lang_id'] = $id;

            $replicaContent[2]['key']     = 'policy';
            $replicaContent[2]['value']   = $validatedData['policy'];
            $replicaContent[2]['lang_id'] = $id;

            $replicaContent[2]['key']     = 'terms';
            $replicaContent[2]['value']   = $validatedData['terms'];
            $replicaContent[2]['lang_id'] = $id;

            $replicaContent[2]['key']     = 'about';
            $replicaContent[2]['value']   = $validatedData['about'];
            $replicaContent[2]['lang_id'] = $id;

            ReplicaContent::insert($replicaContent);
            return redirect()->route('welcome.letter')
                ->with('success', 'Replica Default Content updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
