<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddInviteRequest;
use App\Models\InvitesConfiguration;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadTraits;
use App\Jobs\UserActivityJob;


class PromotionalToolsController extends CoreInfController
{
    use UploadTraits;

    public function index()
    {
        $invites_data = InvitesConfiguration::get();
        return view('promotionalTools.index', compact('invites_data', 'invites_data'));
    }

    public function store(AddInviteRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $moduleStatus  = $this->moduleStatus();

            if ($moduleStatus->promotion_status) {
                $invites = new InvitesConfiguration;
                if ($validatedData['type'] == 'banner') {
                    if ($request->hasFile('content')) {
                        $file   = $request->file('content');
                        $folder = 'promotionalTools';
                        $prefix = 'PTB';
                        $upload = $this->singleImageUpload($file, $invites, $prefix, $folder);
                        if (!$upload) {
                            if ($request->ajax()) {
                                return response()->json(['status' => false, 'message' => 'file upload failed.'], 400);
                            }
                            return response()->json(['status' => false, 'message' => 'file upload failed.'], 400);
                        } else {
                            $validatedData['content'] = $upload;
                        }
                    }
                }
                $invites->fill($validatedData);
                $invites->save();

                if ($invites->id) {
                    $table_prefix = config('database.connections.mysql.prefix');
                    $user = auth()->user();
                    UserActivityJob::dispatch(
                        $user->id,
                        [],
                        'Invites added',
                        $user->username . 'New Invites added',
                        $table_prefix,
                        $user->user_type,
                    );
                }
            } else {
                return response()->json(['status' => false, 'message' => trans('common.module_not_enabled')], 400);
            }
            return response()->json(['status' => true, 'message' => trans('common.invite_added_successfully')]);
        } catch (\Throwable $th) {
            return response()->json(['status' => true, 'message' => $th->getMessage(),], 404);
        }
    }
    public function update(AddInviteRequest $request){
        try{
            $moduleStatus  = $this->moduleStatus();
            if ($moduleStatus->promotion_status) {
                $validatedData = $request->validated();
                InvitesConfiguration::where('id',$request->inviteId)->update($validatedData);
                return response()->json([
                    'status' => true,
                    'message' => 'Invite Added successfully'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'permission denied'],
                400);
            }

        }catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => $th->getMessage()
            ], 404);
            throw $th;
        }
    }

    public function deleteInvites(Request $request){
        try{
            $moduleStatus  = $this->moduleStatus();
            if ($moduleStatus->promotion_status) {
                if ($request->id) {
                    InvitesConfiguration::where('id',$request->id)->delete();
                    return response()->json([
                        'status' => true,
                        "message" => 'Invite has been deleted successfully']
                    );
                } else {
                    return response()->json([
                        'status' => false,
                        "message" => 'Invalid Invite ID'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'permission denied'],
                400);
            }

        }catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => $th->getMessage()
            ], 404);
            throw $th;
        }
    }
    public function checkEmptyStatus(Request $request){
        if($request->type == 'social')
            $type = ['social_instagram','social_facebook','social_twitter','social_email'];
        else
            $type = [$request->type];

        $count = InvitesConfiguration::whereIn('type',$type)->count();
        if($count){
            return response()->json([
                'status' => 'true',
            ]);
        }else{
            return response()->json([
                'status' => 'false',
            ]);
        }
    }
}
