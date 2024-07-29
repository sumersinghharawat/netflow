<?php

namespace App\Http\Controllers;

use App\Models\KycCategory;
use App\Models\KycDocs;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KycController extends Controller
{
    public function index()
    {
        $kyc_list = kycDocs::with('user.userDetail', 'KycCategory')->get();
        $kyc_catg = KycCategory::where('status', '1')->get();
        return view('admin.profile.kyc', compact('kyc_catg', 'kyc_list'));
    }

    public function kycDetails(Request $request)
    {
        if ($request->has('status')) {
            switch ($request->status) {
                case 'any':
                    $status = 'any';
                    $kyc_details = kycDocs::select('user_id', 'type', \DB::raw('MAX(id) as id'), \DB::raw('MAX(file_name) as file_name'), \DB::raw('MAX(status) as status'))->with('user.userDetail', 'KycCategory');
                    break;
                case 'pending':
                    $status = 'pending';
                    $kyc_details = kycDocs::select('user_id', 'type', \DB::raw('MAX(id) as id'), \DB::raw('MAX(file_name) as file_name'), \DB::raw('MAX(status) as status'))->with('user.userDetail', 'KycCategory')->Pending();
                    break;
                case 'approved':
                    $status = 'approved';
                    $kyc_details = kycDocs::select('user_id', 'type', \DB::raw('MAX(id) as id'), \DB::raw('MAX(file_name) as file_name'), \DB::raw('MAX(status) as status'))->with('user.userDetail', 'KycCategory')->Approved();
                    break;
                case 'rejected':
                    $status = 'rejected';
                    $kyc_details = kycDocs::select('user_id', 'type', \DB::raw('MAX(id) as id'), \DB::raw('MAX(file_name) as file_name'), \DB::raw('MAX(status) as status'))->with('user.userDetail', 'KycCategory')->Rejected();
                default:
                    // code...
                    break;
            }
        }

        if ($request->has('users')) {
            $kyc_details->whereIn('user_id', [...$request->users]);
        }

        if ($request->has('category')) {
            if ($request->category == '') {
                $kyc_details = $kyc_details;
            } else {
                $kyc_details = $kyc_details->where('type', $request->category);
            }
        }

        $kyc_details = $kyc_details->orderBy('status','desc');
        $kyc_details = $kyc_details->groupBy('user_id','type');
        return DataTables::of($kyc_details)
            ->addColumn('checkbox', function ($data) {
                if ($data->status == 2) {
                    return '<input type="checkbox" name="approve" id="btn" class="form-check-input mt-3 checked-box" value="' . $data->id . '">';
                }else{
                    return '';
                }
            })
            ->addColumn('member_name', function ($data) {
                return '<div class="d-flex"><img class="rounded-circle avatar-md" src="'.asset('/assets/images/users/avatar-1.jpg').'"><div class="transaction-user"><h5>'.$data->user->userDetail->name.'</h5><span>'.$data->user->username.'</span></div></div';
            })
            ->addColumn('category', function ($data) {
                return $data->KycCategory->category;
            })
            ->addColumn('view', function ($data) {
                $img_array = kycDocs::select('file_name','id')
                            ->where('user_id',$data->user_id)
                            ->where('type',$data->type)
                            ->where('status',2)
                            ->get();
                $buttons = '';
                foreach ($img_array as $kycimgs) {
                    $extension = pathinfo(storage_path($kycimgs->file_name), PATHINFO_EXTENSION);
                    if ($extension == 'pdf') {
                        return "<a href='$kycimgs->file_name' class='btn btn-info' data-placement='top' data-original-title='' title=download target='_blank' download>
                        <i class='fa fa-download' data-toggle='tooltip' title='Download'></i>$extension</a>";
                    } else {
                        $btn = "<a href='javascript:void' class='btn btn-primary thumbs m-1' data-bs-toggle='modal' onclick='kycImg($kycimgs->id)'
                        data-bs-target='#terms'>$extension</a>";
                        $buttons .= $btn;
                    }
                }
                return $buttons;


            })
            ->addColumn('action', function ($data) use ($status) {
                if ($status == 'any') {
                    if ($data->status == 2) {
                        return '
                        <div class="popup-btn-area col-8 d-none" id="reg_approval_action_popup">
                    <div class="row">
                        <div class="text-white col">
                            <span id="active_items_selected_span"></span>
                            <!-- <div id="active_items_selected_div_new"></div> -->
                        </div>
                        <div class="col">
                        <div class="gap-2 d-flex">
                    <a href="#" onclick="approvekyc('.$data->id.')" class="waves-effect btn btn-success">
                    <i class="material-icons">Approve</i></a>
                    <a href="#" onclick="rejectKyc('.$data->id.')" class="waves-effect btn btn-danger">
                    <i class="material-icons">Reject</i></a>
                    </div>
                    </div>
                    </div>
                    </div>';
                    } else {
                        return '';
                    }
                } elseif ($status == 'pending') {
                    return '
                    <div class="popup-btn-area col-8 d-none" id="reg_approval_action_popup">
                    <div class="row">
                        <div class="text-white col">
                            <span id="active_items_selected_span"></span>
                            <!-- <div id="active_items_selected_div_new"></div> -->
                        </div>
                        <div class="col">
                        <div class="gap-2 d-flex">
                    <a href="#" onclick="approvekyc('.$data->id.')" class="waves-effect btn btn-success">
                    <i class="material-icons">Approve</i></a>
                    <a href="#" onclick="rejectKyc('.$data->id.')" class="waves-effect btn btn-danger">
                    <i class="material-icons">Reject</i></a>
                    </div>
                    </div>
                    </div>
                    </div>';
                } else {
                    return '';
                }
            })
            ->addColumn('status', function ($data) {
                if ($data->status == '2') {
                    $status = '<span class="label label-warning">Pending</span>';
                } elseif ($data->status == '1') {
                    $status = '<span class="label label-success">Approved</span>';
                } else {
                    $status = '<span class="label label-danger">Rejected</span>';
                }

                return $status;
            })

            ->rawColumns(['checkbox','member_name', 'category', 'status', 'view', 'action'])
            ->make(true);
    }

    public function approvekyc($data)
    {
        try {
            $ids = explode(',', $data);
                foreach ($ids as $id){
                DB::beginTransaction();

                $approvekyc = KycDocs::find($id);
                $user_id = $approvekyc->user_id;
                $type = $approvekyc->type;
                $kyc_images = kycDocs::select('*')
                            ->where('user_id',$user_id)
                            ->where('type',$type)
                            ->where('status',2)
                            ->get();
                foreach ($kyc_images as $image){
                    $image->update([
                        'status' => '1',
                    ]);
                }

                UserDetail::where('user_id', $user_id)->update([
                    'kyc_status' => 1,
                ]);

                DB::commit();
            }
            return response()->json([
                'status' => true,
                'message' => 'approved successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
        }
    }

    public function getUserKycImage($id)
    {
        $kyc = KycDocs::find($id);
        if (! empty($kyc)) {
            $img_array = explode(",", $kyc->file_name);
                $img_array = array_map(function($val){
                    return str_replace(["[","]",'"'],'',$val);
                },$img_array);
            return $img_array;
        }
    }

    public function rejectkyc($data)
    {
        $ids = explode(',', $data);
        foreach ($ids as $id){
            $rejectkyc = KycDocs::find($id);
            $user_id = $rejectkyc->user_id;
                $type = $rejectkyc->type;
                $kyc_images = kycDocs::select('*')
                            ->where('user_id',$user_id)
                            ->where('type',$type)
                            ->where('status',2)
                            ->get();
                foreach ($kyc_images as $image){
                    $image->update([
                        'status' => '0',
                    ]);
                }
        }
        return response()->json([
            'status' => true,
            'message' => 'rejected successfully',
        ]);
    }
}
