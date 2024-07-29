<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberListRequest;
use App\Models\Package;
use App\Models\PackageUpgradeHistory;
use App\Models\Packagevalidityextendhistory;
use App\Models\User;
use App\Services\commissionService;
use App\Services\PackageUpgradeService;
use App\Services\UserApproveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use App\Models\PaymentGatewayConfig;

class MemberListController extends CoreInfController
{
    protected $serviceClass;

    public function __construct(UserApproveService $serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }

    public function index(Request $request)
    {
        $memberList = User::query();
        $username = null;
        if ($request->has('username') && $request->username != null) {
            $memberList->where('user_type', '!=', 'employee')->where('id', $request->username);
            $username = User::select('username', 'id')->whereKey($request->username)->first();
        }
        $status = $request->status ?? 'active';

        if ($status == 'active') {
            $memberList->where('active', true);
        } elseif ($request->status == 'blocked') {
            $memberList->where('active', false);
        }

        $count          = $memberList->where('user_type', 'user')->count();
        $today_count    = User::where('user_type', 'user')->whereDay('date_of_joining', today())->count();
        $moduleStatus   = $this->moduleStatus();
        $tab            = $request->has('tab') ? $request->tab : null;
        if($moduleStatus->package_upgrade)
            $packagevalidityextendhistory =  Packagevalidityextendhistory::with('bankReciept')->where('renewal_status', false)->get();
        else $packagevalidityextendhistory = '';
        $memberList = $memberList->where('user_type', 'user')->with('sponsor', 'userDetails', 'package', 'rankDetail')->paginate(10)->withQueryString();
        return view('admin.reports.memberlist', compact('memberList', 'count', 'today_count', 'moduleStatus', 'username', 'tab', 'packagevalidityextendhistory','status'));
    }

    public function userUpdate(MemberListRequest $request)
    {
        if (session()->get('is_preset')) {
            return redirect()->back()->withErrors("You don't have permission By using Preset Demo");
        }

        $validatedData = $request->validated();

        $ids = $validatedData['user'];

        foreach ($ids as $id) {
            if (User::find($id)->active) {
                User::find($id)->update(['active' => 0]);
            } else {
                User::find($id)->update(['active' => 1]);
            }
        }

        return redirect(route('memberlist.view'))->with('success', 'User Updated successfully');
    }

    public function getPendingRenewal(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        $currency = $this->getDefaultCurrency();
        if ($moduleStatus->subscription_status) {
            $packagevalidityextendhistory = Packagevalidityextendhistory::with('bankReciept')->where('renewal_status', 0);

            return DataTables::of($packagevalidityextendhistory)

                ->addIndexColumn()
                ->addColumn('username', function ($packagevalidityextendhistory) {
                    return $packagevalidityextendhistory->user->username;
                })
                ->addColumn('package', function ($packagevalidityextendhistory) {
                    return $packagevalidityextendhistory->package->name;
                })
                ->addColumn('approve', function ($row) {
                    $reciept = $row->bankReciept->receipt ?? false;

                    if($reciept != false){
                        $btn = '<a class="btn" onclick="viewPendingBankReciept(\'' . $reciept. '\' )"><i class="far fa-eye"></i></a>  <a class="m2 btn btn-primary" onclick="approveRenewal(' . $row->id . ')">Approve</a>';
                    }
                    else {
                        $btn = '<a class="m2 btn btn-primary" onclick="approveRenewal(' . $row->id . ')">Approve</a>';
                    }

                    return $btn;
                })
                ->editColumn('total_amount', fn($item) => $currency->symbol_left .''.formatCurrency($item->total_amount))
                ->rawColumns(['username', 'approve', 'package'])
                ->make(true);
        }

        return DataTables::of(collect([]))->toJson();
    }

    public function getPendingUpgrade(Request $request)
    {
        $moduleStatus = $this->moduleStatus();
        if ($moduleStatus->package_upgrade || $moduleStatus->package_upgrade_demo) {
            $PackageUpgradeHistory = PackageUpgradeHistory::with('user.userDetail', 'user.sponsor', 'paymentMethod', 'upgradePackage', 'bankReciept')->where('status', false);
            if ($request->has('username') && $request->username != null) {
                $PackageUpgradeHistory->where("user_id", $request->username);
            }
            $currency = currencySymbol();
            return DataTables::of($PackageUpgradeHistory)
                ->addIndexColumn()
                ->addColumn('name', function ($PackageUpgradeHistory) {
                    if ($PackageUpgradeHistory->user->username != '') {
                        return $PackageUpgradeHistory->user->username;
                    } else {
                        return 'NA';
                    }
                })
                ->addColumn('sponsor', function ($PackageUpgradeHistory) {
                    return $PackageUpgradeHistory->user->sponsor->username ?? 'NA';
                })
                ->addColumn('payment_method', function ($PackageUpgradeHistory) {
                    return ($PackageUpgradeHistory->paymentMethod()->exists()) ? $PackageUpgradeHistory->paymentMethod->name : "";
                })
                ->addColumn('package', function ($PackageUpgradeHistory) {
                    return $PackageUpgradeHistory->upgradePackage->name;
                })
                ->addColumn('payment_amount', function ($PackageUpgradeHistory) use ($currency) {
                    return $currency . ' ' . formatCurrency($PackageUpgradeHistory->payment_amount);
                })
                ->addColumn('approve', function ($row) {
                    $reciept = $row->bankReciept->receipt ?? false;
                    if($reciept != false){
                        return '<a class="btn" onclick="viewPendingBankReciept(\'' . $reciept. '\' )"><i class="far fa-eye"></i></a>  <a class="m2 btn btn-primary" onclick="approvePackage(' . $row->id . ',this)">Approve</a>';
                    }
                    else{
                        return '<a class="m2 btn btn-primary" onclick="approvePackage(' . $row->id . ',this)">Approve</a>';
                    }

                })
                ->rawColumns(['name', 'sponsor', 'payment_amount', 'approve', 'payment_method'])
                ->make(true);
        }

        return true;
    }

    public function approve(Request $request, $id)
    {
        DB::beginTransaction();
        $prefix = str_replace("_", "", config('database.connections.mysql.prefix'));

        try {
            $PackageUpgradeHistory = PackageUpgradeHistory::with('user', 'currentPackage', 'upgradePackage')->find($id);
            $PackageUpgradeHistory->user->product_id = $PackageUpgradeHistory->new_package_id;
            $PackageUpgradeHistory->user->personal_pv += $PackageUpgradeHistory->pv_difference;
            $PackageUpgradeHistory->status = 1;
            $PackageUpgradeHistory->push();

            $user = $PackageUpgradeHistory->user;

            $user->load('sponsor');
            if ($this->moduleStatus()['product_status']) {
                $this->serviceClass->updateGroupPV($user->sponsor, $PackageUpgradeHistory->pv_difference, $user->id);
            }

            $total_amount = abs($PackageUpgradeHistory->currentPackage->price - $PackageUpgradeHistory->upgradePackage->price);

            if ($this->moduleStatus()->roi_status) {
                $paymentType = $PackageUpgradeHistory->load('paymentMethod');
                $this->serviceClass->insertRoi($user, $PackageUpgradeHistory->upgradePackage, $paymentType->paymentMethod);
            }
            DB::commit();
            $commissionService = new commissionService;
            $commissionService->levelCommission($user, $user->sponsor, 0, $PackageUpgradeHistory->new_package_id, $PackageUpgradeHistory->pv_difference, $total_amount, $prefix, 'upgrade');
            $commissionService->performanceBonus($user, $prefix);
            if ($this->moduleStatus()['rank_status']) {
                $commissionService->updateUplineRank($user->id, $prefix);
            }
            return response()->json(['message' => 'Package Upgraded Successfully']);
        } catch (Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ]);
            } else {
                return redirect(route('crm.index'))->with('error', $th->getMessage());
            }
        }
    }

    public function renewalApprove(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $packagevalidityextendhistory = Packagevalidityextendhistory::find($id);

            $packagevalidityextendhistory->update([
                'renewal_status' => 1,
            ]);
            if ($packagevalidityextendhistory) {
                $today = date('Y-m-d H:i:s');
                $packageUpgradeService = new PackageUpgradeService;
                $validity_date = $packageUpgradeService->getValidityDate($packagevalidityextendhistory->user_id);
                if ($validity_date < $today) {
                    $expiry_date = $packageUpgradeService->getPackageValidityDate($packagevalidityextendhistory->package_id, '', $this->moduleStatus());
                } else {
                    $expiry_date = $packageUpgradeService->getPackageValidityDate($packagevalidityextendhistory->package_id, $validity_date, $this->moduleStatus());
                }
                User::find($packagevalidityextendhistory->user_id)->update([
                    'product_validity' => $expiry_date,
                ]);
            }
            DB::commit();

            return response()->json(['message' => 'Package Renewed Successfully']);
        } catch (Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ]);
            } else {
                return redirect(route('memberlist.view'))->with('error', $th->getMessage());
            }
        }
    }

    public function packageupgradeapproval(Request $request)
    {
        $moduleStatus   = $this->moduleStatus();
        return view('admin.package.approval.upgradeapprove', compact( 'moduleStatus'));
    }
    public function packagerenewalapproval(Request $request)
    {
        $moduleStatus   = $this->moduleStatus();
        return view('admin.package.approval.renewalapprove', compact( 'moduleStatus'));
    }

}
