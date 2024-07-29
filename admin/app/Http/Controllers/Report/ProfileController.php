<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestEWalletSummary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;
use Yajra\DataTables\Facades\DataTables;


class ProfileController extends Controller
{
    public function profileView(Request $request)
    {
        return view('admin.report.profile-view');
    }
    public function userReport(Request $request)
    {
        try {
            if ($request->username) {
                $user = User::with('userDetails')->find($request->username);

                $view = view('admin.report.ajax.profile-report', compact('user'));

                return response()->json([
                    'status' => true,
                    'data' => $view->render(),
                ], 200);
            }
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    public function userDateReport(RequestEWalletSummary $request)
    {
        $users = [];
        if ($request->fromDate || $request->toDate) {
            $users = User::select('username', 'date_of_joining', 'id', 'sponsor_id','email')->with('userDetails:name,second_name,mobile,pin,user_id,id,country_id', 'userDetail.country:name,id', 'sponsor:username,id')->where('user_type', 'user')->whereBetween('date_of_joining', [$request->fromDate . ' 00:00:00', $request->toDate . ' 23:59:59']);
        }

        return Datatables::of($users)
            ->addColumn('member', fn ($data) => $data->userDetail->name . ' ' . $data->userDetail->second_name ?? 'NA')
            ->addColumn('sponsor_name', fn ($data) => $data->sponsor->username ?? 'NA')
            ->addColumn('email', fn ($data) => $data->email ?? 'NA')
            ->addColumn('phone', fn ($data) => $data->userDetail->mobile ?? 'NA')
            ->addColumn('country', fn ($data) => $data->userDetail->country->name ?? 'NA')
            ->addColumn('pin', fn ($data) => $data->userDetail->pin ?? 'NA')
            ->addColumn('date_of_joining', fn ($data) => Carbon::parse($data->date_of_joining)->toDateString())
            ->rawColumns(['member', 'sponsor_name', 'email', 'phone', 'country', 'pin', 'date_of_joining'])
            ->make(true);
    }

    public function validate_user(Request $request)
    {
        //    return json response, not array
        if (User::where('username', $request->username)->exists()) {
            $result = [
                'status' => true,
                'message' => 'success',
            ];

            return $result;
        } else {
            $result = [
                'status' => 'not_exist',
                'message' => 'please enter valid username',
            ];

            return $result;
        }

        return response()->json([
            'status' => true,
            'message' => 'success',
        ], 404);
    }
}
