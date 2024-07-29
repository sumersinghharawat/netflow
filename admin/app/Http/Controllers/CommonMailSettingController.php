<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RequestsCommonMail;
use App\Models\CommonMailSetting;
use App\Models\Language;
use App\Models\Placeholders;
use App\Models\Letterconfig;

class CommonMailSettingController extends Controller
{
    public function index()
    {
        // TODO welcome and terms relation others single select all
        $languages = Language::where('status', 1)->get();
        $commonMail = CommonMailSetting::where('status',1)
        ->where('lang_id' ,1)
        ->get();
        return view('admin.settings.mailContent.index', compact('languages', 'commonMail'));
    }

    public function edit($id,$language_id)
    {
        $data = CommonMailSetting::where('mail_type',$id)
        ->where('lang_id',$language_id)
        ->first();
        // $placeholders = DB::table('placeholders')->get();

        if ($id == 'send_tranpass') {
            $idsToExclude = [ 5 , 10 ];
            $placeholders = Placeholders::whereNotIn('id', $idsToExclude)->get();
        }
        elseif ($id == 'payout_request') {
            $idsToExclude = [ 2 , 9 , 10 ];
            $placeholders = Placeholders::whereNotIn('id', $idsToExclude)->get();
        }
        elseif ($id == 'registration_email_verification') {
            $idsToExclude = [ 2 , 9 , 5 ];
            $placeholders = Placeholders::whereNotIn('id', $idsToExclude)->get();
        }
        elseif ($id == 'forgot_password') {
            $idsToExclude = [ 5 ];
            $placeholders = Placeholders::whereNotIn('id', $idsToExclude)->get();
        }
        elseif ($id == 'reset_googleAuth') {
            $idsToExclude = [ 5 , 2 , 9 ];
            $placeholders = Placeholders::whereNotIn('id', $idsToExclude)->get();
        }
        elseif ($id == 'forgot_transaction_password') {
            $idsToExclude = [ 5 ];
            $placeholders = Placeholders::whereNotIn('id', $idsToExclude)->get();
        }
        elseif ($id == 'external_mail') {
            $idsToExclude = [ 5 , 2 , 9 ];
            $placeholders = Placeholders::whereNotIn('id', $idsToExclude)->get();
        }
        elseif ($id == 'change_password') {
            $idsToExclude = [ 5 ];
            $placeholders = Placeholders::whereNotIn('id', $idsToExclude)->get();
        }
        elseif ($id == 'registration') {
            $idsToExclude = [ 5 ];
            $placeholders = Placeholders::whereNotIn('id', $idsToExclude)->get();
        }
        elseif ($id == 'payout_release') {
            $idsToExclude = [ 2 , 9 ];
            $placeholders = Placeholders::whereNotIn('id', $idsToExclude)->get();
        }
        else{
            $placeholders = Placeholders::get();
        }
        if($data){
            return view('admin.settings.mailContent._edit', compact('data' , 'placeholders'));
        }
        else
        {
            return view('admin.settings.mailContent._add', compact('placeholders' , 'id' , 'language_id'));
        }
    }

    public function update(RequestsCommonMail $request, $id)
    {
        try {
            CommonMailSetting::find($id)->update([
                'mail_content' => $request->content,
                'subject' => $request->subject,
            ]);

            return back()
            ->with('success', 'record updated successfully.');
        } catch (\Exception $e) {
            return back()
            ->with('warning', $e->getMessage());
        }
    }

    public function addnew(RequestsCommonMail $request){
        try{
            $data = [
                'mail_type'    => $request->mail_type,
                'subject'      => $request->subject,
                'mail_content' => $request->content,
                'lang_id'      => $request->lang_id,
                'status'       => 1
            ];
            CommonMailSetting::create($data);
            return back()
            ->with('success' , 'record added successfully');
        }catch (\Exception $e){
            return back()
            ->with('warning', $e->getMessage());
        }
    }
}
