<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditLeadRequest;
use App\Models\Country;
use App\Models\CrmLead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ToolsController extends Controller
{
    public function viewLead(Request $request, $type = '')
    {
        $countries = Country::all();

        return view('admin.tools.view-lead', compact('countries', 'type'));
    }

    public function getViewLeads(Request $request)
    {
        $crm = CrmLead::query();
        $crm = $crm->with('user');
        if ($request->has('filter') && $request->filter != '') {
            $crm = $crm->where('first_name', 'like', '%'.$request->filter.'%')->orWhere('email_id', 'like', '%'.$request->filter.'%')->orWhere('mobile_no', 'like', '%'.$request->filter.'%');
        }
        $crm->orderBy('id', 'desc');
        return DataTables::of($crm)
            ->addIndexColumn()
            ->addColumn('sponsor', function ($crm) {
                if ($crm->user != null) {
                    return $crm->user->username;
                } else {
                    return 'NA';
                }
            })
            ->addColumn('status', function ($crm) {
                if ($crm->lead_status == 1) {
                    $data = 'Ongoing';
                } elseif ($crm->lead_status == 0) {
                    $data = 'Rejected';
                } else {
                    $data = 'Accepted';
                }

                return $data;
            })
            ->addColumn('date', function ($crm) {
                $date = Carbon::parse($crm->created_at)->format('d-M-Y g:i A');

                return $date;
            })
            // ->addColumn('edit_lead', function ($row) {
            //     $btn = '<a class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" onclick="editLead('.$row->id.')" aria-controls="offcanvasRight"><i class="fa fa-edit"></i></a>';

            //     return $btn;
            // })

            ->rawColumns(['sponsor', 'status', 'date'])
            ->make(true);
    }

    public function editLead(Request $request, $crmlead)
    {
        $crmlead = CrmLead::where('id', $crmlead)->first();

        return response()->json(['crmlead' => $crmlead, 'id' => $crmlead->id]);
    }

    public function leadUpdate(EditLeadRequest $request)
    {
        DB::beginTransaction();
        try {
            $crmLead = CrmLead::where('id', $request->id)->first();
            $crmLead->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email_id' => $request->email,
                'skype_id' => $request->skype_id,
                'mobile_no' => $request->mobile,
                'added_by' => Auth::user()->id,
                'country_id' => $request->country,
                'description' => $request->description,
                'interest_status' => $request->interest,
                'followup_date' => $request->followup_date,
                'confirmation_date' => ($request->status_change_date) ? $request->status_change_date : $crmLead->confirmation_date,
                'lead_status' => $request->lead_status,
            ]);
            DB::commit();
            $lead_completeness = $this->getLeadCompleteness($crmLead->id);
            if ($lead_completeness <= 50) {
                $color = 'bg-danger';
            } elseif ($lead_completeness >= 51 && $lead_completeness <= 99) {
                $color = 'bg-warning';
            } elseif ($lead_completeness = 100) {
                $color = 'bg-success';
            }
            $view = view('crm.ajax.progressbar', compact('lead_completeness', 'color'));

            return response()->json([
                'message' => 'Lead Updated successfully',
                'crmlead' => $crmLead,
                'data' => $view->render(),
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
    }
}
