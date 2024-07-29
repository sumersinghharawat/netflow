<?php

namespace App\Jobs;

use Throwable;
use App\Models\User;
use App\Models\Package;
use App\Models\Treepath;
use App\Models\SalesOrder;
use App\Models\ModuleStatus;
use Illuminate\Bus\Queueable;
use App\Events\UserRegistered;
use Illuminate\Support\Facades\DB;
use App\Models\PendingRegistration;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentGatewayConfig;
use App\Services\UserApproveService;
use Illuminate\Queue\SerializesModels;
use App\Services\PackageUpgradeService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Validation\ValidationException;

class UserApproveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected $pendingUsers,
        protected $prefix,
        protected $requestData
    ) {
        $this->onQueue('userApprove');
    }

    public $timeout = 1200;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $moduleStatus = ModuleStatus::first();
        $pendingUserData = PendingRegistration::whereIn('id', [...$this->pendingUsers])->get();
        $plan = $moduleStatus->mlm_plan;
        $request = $this->requestData;
        Log::channel('mylog')->debug('Approved values: ' . print_r($pendingUserData->toArray(), true));

        foreach ($pendingUserData as $k => $pendingUser) {
            Log::channel('mylog')->debug('Loop index: ' . print_r($k, true));

            DB::transaction(function () use ($pendingUser, $moduleStatus, $plan, $request) {
                try {
                    $regData = json_decode($pendingUser->data, true);
                    $serviceClass = new UserApproveService;
                    if ($moduleStatus->product_status) {
                        $productData = Package::find($regData['product_id']);
                    } else {
                        $productData = [];
                    }
                    if ($plan == 'Binary') {
                        $placementData = $serviceClass->getPlacementData($regData['position'], $regData['sponsor_id'], $regData['regFromTree']);
                    } elseif ($plan == 'Unilevel' || $plan == 'Stairstep' || $plan == 'Xup') {
                        $placementData = $serviceClass->getUnilevelPlacement($regData['sponsor_id']);
                    } elseif ($plan == 'Donation') {
                        $placementData = $serviceClass->getDonationPlacement($regData['sponsor_id']);
                    } elseif ($plan == 'Matrix') {
                        $placementData = $serviceClass->getMatrixPlacement($regData['sponsor_id'], $regData['regFromTree']);
                    } elseif ($plan == 'Party') {
                        $placementData = $serviceClass->getPartyPlacement($regData['sponsor_id']);
                    } elseif ($plan == 'Monoline') {
                        $placementData = $serviceClass->monolinePlacement();
                    }
                    if (isset($regData['regFromTree'])) {
                        if ($regData['regFromTree'] && !$placementData) {
                            $placementUserDetails = User::where('username', $regData['placement_username'])->first();
                            if ($serviceClass->checkPositionAvailable($placementUserDetails->id, $regData['position'], $regData['sponsor_id'])) {
                                $PendingRegistrationStatus = PendingRegistration::where('id', $pendingUser->id)->update(['status' => 'failed', 'failed_reason' => 'Placement is not available under this sponsor']);
                                $pendingNewData = PendingRegistration::find($pendingUser->id);
                                Log::channel('mylog')->debug('Pending Registration Update' . print_r($pendingNewData->toArray(), true));
                                throw ValidationException::withMessages([
                                    'user_activation' => 'Placement is not available under this sponsor'
                                ], 422);
                            }
                            $placementData = (object) [
                                'fatherId' => $placementUserDetails->id,
                                'father' => $placementUserDetails->username,
                                'positionString' => $regData['position'],
                            ];
                        }
                    }
                    $fatherData = User::find($placementData->fatherId)->load('ancestors');
                    $sponsorData = User::withCount('sponsorDescendant')->find($regData['sponsor_id'])->load('sponsorAncestors');
                    if (isset($regData['product_id'])) {
                        $servicePackageupgrade = new PackageUpgradeService;
                        $product_validity = $servicePackageupgrade->getPackageValidityDate($regData['product_id'], '', $moduleStatus);
                    } else {
                        $product_validity = '';
                    }
                    $user = $serviceClass->addToUsers($regData, $fatherData, $placementData, $productData, $sponsorData, $moduleStatus->mlm_plan, $product_validity, $moduleStatus->product_status, $moduleStatus->subscription_status);
                    if (!$user) {
                        PendingRegistration::where('id', $pendingUser->id)->update(['status' => 'failed', 'failed_reason' => 'User Activation Failed']);
                    }

                    if ($plan == 'Binary') {
                        $serviceClass->updatePlacementTable(sponsor: $sponsorData, user: $user, position: $regData['position'], fromTree: $regData['regFromTree'], fatherData: $fatherData);
                    }

                    $serviceClass->addToUserDetails(compact('pendingUser', 'regData', 'user', 'request'));
                    $serviceClass->addToRegistrationDetails(compact('regData', 'productData', 'user'));

                    $serviceClass->insertTreepath($fatherData, $user);

                    $serviceClass->createTransPassword($user);
                    if (isset($regData['custom']))
                        $serviceClass->addToCustomDetails($regData, $user->id);

                    if ($plan == 'Binary') {
                        $serviceClass->addLegDetails($user);
                    } elseif ($plan == 'Stair_Step') {
                        $serviceClass->addToStairStep($user);
                        $serviceClass->addToUserPvDetails($user);
                    }

                    $serviceClass->sponsorTreePath($sponsorData, $user);

                    $serviceClass->addToUserBalance($user);
                    if ($pendingUser->paymentGateway->slug == 'bank-transfer') {
                        $serviceClass->addToPaymentReceipt($pendingUser->id, $user);
                    }
                    Log::channel('mylog')->debug('pendinguser value' . print_r($pendingUser->toArray(), true));
                    $pending = PendingRegistration::where('id', $pendingUser->id)->first();

                    Log::channel('mylog')->debug('The value of $pending is: ' . print_r($pending->toArray(), true));
                    if ($pending) {
                        $pending->status = 'active';
                        $pending->updated_id = $user->id;
                        if (!$pending->save()) {
                            throw ValidationException::withMessages([
                                'user_activation' => 'user activation failed'
                            ]);
                        }
                        Log::channel('mylog')->debug('The value of $pending after updation: ' . print_r($pending->toArray(), true));
                    } else {
                        PendingRegistration::where('id', $pendingUser->id)->update(['status' => 'pending', 'failed_reason' => 'Processing not turn to complete']);
                    }
                    $user->load('sponsor');
                    if ($moduleStatus->product_status) {
                        $serviceClass->updateGroupPV($user->sponsor, $user->personal_pv, $user->id);
                        $ancestors = Treepath::where('descendant',$user->id)->pluck('ancestor');
                        $serviceClass->insertPVhistoryDetailsNew($ancestors, $user->personal_pv, 'personal_pv', $user->id, 'register');
                        $salesOrder = SalesOrder::where('pending_user_id', $pendingUser->id)->first();
                        $salesOrder->user_id = $user->id;
                        $salesOrder->push();
                    }
                    $paymentType = PaymentGatewayConfig::findOrfail($regData['payment_method']);
                    $userApproveService = new UserApproveService;
                    if ($moduleStatus->roi_status) {
                        $userApproveService->insertRoi($user, $productData, $paymentType);
                    }

                    if ($plan == "Monoline") {
                        $serviceClass->addToUserReentry($user);
                    }
                    Log::channel('mylog')->debug('######################################################################');

                    UserRegistered::dispatch($user, $regData);
                }
                catch(\Illuminate\Database\QueryException $th){
                    Log::channel('mylog')->debug('LOG Db error: ' . $th->getMessage());
                    $pending = PendingRegistration::where('id', $pendingUser->id)->first();
                    $pending->status = 'failed';
                    $pending->failed_reason = "Db insertion issue check values";
                    $pending->push();
                } catch (\Throwable $th) {
                    DB::rollback(); // rollback is used for removing table insertion occured before catch. dont remove it.
                    Log::channel('mylog')->debug('eRROR lOG: ' . $th->getMessage());
                    $email = json_decode($pendingUser->data)?->email ?? null;
                    if (User::where('username', $pendingUser->username)->exists()) {
                        $pending = PendingRegistration::where('id', $pendingUser->id)->first();
                        $pending->status = 'failed';
                        $pending->failed_reason = "username already exists new";
                        $pending->push();
                    } elseif ($email && User::where('email', $email)->exists()) {
                        $pending = PendingRegistration::where('id', $pendingUser->id)->first();
                        $pending->status = 'failed';
                        $pending->failed_reason = "Duplicate email id $email";
                        $pending->push();
                    } elseif (isset($th->status) && $th->status == 422) {
                        $pending = PendingRegistration::where('id', $pendingUser->id)->first();
                        $pending->status = 'failed';
                        $pending->failed_reason = $th->getMessage();
                        $pending->push();
                    } else {
                        $pending = PendingRegistration::where('id', $pendingUser->id)->first();
                        $pending->status = 'failed';
                        $pending->failed_reason = $th->getMessage();
                        $pending->push();
                    }
                }
            }, 5);
            $userApproveService = new UserApproveService;
            $user               = User::where('username', $pendingUser->username)->with('sponsor', 'package')->first();
            $prefix             = str_replace("_", "", $this->prefix);
            if ($user) {
                $commissionStatus = $userApproveService->runCommission($user, $user->sponsor, $user->package, $prefix);
                if (!$commissionStatus['status']) {
                    return false;
                }

                if ($moduleStatus->mlm_plan == 'Monoline') {
                    $userApproveService->runReentryCommission($user->id);
                }
            }
        }
    }
}
