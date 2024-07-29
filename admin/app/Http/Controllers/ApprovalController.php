<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestRegisterApprove;
use App\Jobs\UserApproveJob;
use App\Models\Package;
use App\Models\PendingRegistration;
use App\Models\SignupField;
use App\Models\User;
use App\Services\PackageUpgradeService;
use App\Services\UserApproveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends CoreInfController
{
    public function __construct(
        private UserApproveService $serviceClass,
        private PackageUpgradeService $servicePackageupgrade
    ) {
    }

    public function index()
    {
        if ($this->moduleStatus()['ecom_status']) {
            return abort(403);
        }
        $currency = currencySymbol();
        $modulestatus = $this->moduleStatus();
        $pendingUsers = $this->serviceClass->getPendingRegistarion();
        $dynamicFields = SignupField::where('status', true)->pluck('name')->toArray();
        return view('register.approval', compact('modulestatus', 'pendingUsers', 'currency', 'dynamicFields'));
    }

    public function approve(RequestRegisterApprove $request)
    {
        $validatedData = $request->validated();
        $ids = $validatedData['user'];
        $pendingUsers = PendingRegistration::PendingRegDet([...$ids])->get();
        $moduleStatus = $this->moduleStatus();
        $plan = $moduleStatus->mlm_plan;

        if ($request->has('rejected')) {
            if (!$this->serviceClass->rejectPendingRegistartion($pendingUsers)) {
                return redirect()->back()->withErrors(trans('approval.reject_failure'));
            }

            return redirect(route('approval.index'))->with('success', trans('approval.reject_success'));
        }
        $prefix = config('database.connections.mysql.prefix');
        if(config('queue.default') === 'redis') {
            PendingRegistration::whereIn('id', [...$ids])->update(['status' => 'processing']);
        }

        $job = UserApproveJob::dispatch($pendingUsers->pluck('id'), $prefix, $request->all());
        // return redirect(route('approval.index'))->with('info', __('approval.registration_approval_on_processing'));
        return redirect(route('approval.index'))->with('success', trans('approval.registration_approval'));
    }
}
