<?php

namespace App\Jobs;

use App\Services\UserApproveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Package;
use App\Models\User;
use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\DB;
use App\Services\PackageUpgradeService;
use stdClass;
use Throwable;



class InstantRegisterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $serviceClass;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $regData, public $moduleStatus, public $productValidity, public $prefix)
    {
        $this->serviceClass = new UserApproveService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DB::purge('mysql');
            config(['database.connections.mysql.prefix' => "{$this->prefix}"]);
            DB::connection('mysql');
            DB::beginTransaction();
            $sponsorId = $this->regData['sponsor_id'];
            $position = $this->regData['position'];
            $mlmplan = $this->moduleStatus['mlm_plan'];
            if ($this->moduleStatus['product_status']) {
                $packageId = $this->regData['product_id'];
                $productData = Package::find($packageId);
            }
            if ($mlmplan == 'Binary') {
                $placementData = $this->serviceClass->getPlacementData($this->regData['position'], $this->regData['sponsor_id'], $this->regData['regFromTree']);
            } elseif ($mlmplan == 'Unilevel' || $mlmplan == 'Stair_Step') {
                $placementData = $this->serviceClass->getUnilevelPlacement($this->regData['sponsor_id']);
            } elseif ($mlmplan == 'Donation') {
                $placementData = $this->serviceClass->getDonationPlacement($this->regData['sponsor_id']);
            } elseif ($mlmplan == 'Matrix') {
                $placementData = $this->serviceClass->getMatrixPlacement($this->regData['sponsor_id'], $this->regData['regFromTree']);
            } elseif ($mlmplan == 'Party') {
                $placementData = $this->serviceClass->getPartyPlacement($this->regData['sponsor_id']);
            } elseif ($mlmplan == 'Stair_Step') {
                $placementData = $this->serviceClass->getStairStepPlacement($this->regData['sponsor_id']);
            }
            if ($this->regData['regFromTree'] && ! $placementData) {
                $placementUserDetails = User::where('username', $this->regData['placement_username'])->first();
                if ($this->serviceClass->checkPositionAvailable($placementUserDetails->id, $this->regData['position'])) {
                    DB::rollBack();

                    return redirect()->back()->withErrors('register.placment_is_not_available');
                }
                $placementData = (object) [
                    'fatherId' => $placementUserDetails->id,
                    'father' => $placementUserDetails->username,
                    'positionString' => $this->regData['position'],
                ];
            }

            $fatherData = User::find($placementData->fatherId)->load('ancestors');
            $sponsorData = User::find($this->regData['sponsor_id'])->load('sponsorAncestors');

            if ($this->regData['product_id'] != '') {
                $servicePackageupgrade = new PackageUpgradeService;
                $product_validity = $servicePackageupgrade->getPackageValidityDate($this->regData['product_id'], '', $this->moduleStatus);
            } else {
                $product_validity = '';
            }
            $user = $this->serviceClass->addToUsers($this->regData, $fatherData, $placementData, $productData, $sponsorData, $this->moduleStatus->mlm_plan, $product_validity, $this->moduleStatus->product_status, $this->moduleStatus->subscription_status);
            if (! $user) {
                DB::rollback();
                return [
                    'status' => false,
                    'error' => 'user_activation_failed',
                ];
            }

            $pendingUser = new stdClass();
            $pendingUser->sponsor_id = $sponsorId;
            $data = [
                'pendingUser'   => $pendingUser,
                'regData'       => $this->regData,
                'request'       => $this->regData,
                'user'          => $user
            ];

            if ($mlmplan == 'Binary') {
                $this->serviceClass->updatePlacementTable(sponsor: $sponsorData, user: $user, position: $regData['position'], fromTree: $regData['regFromTree'], fatherData: $fatherData);
            }

            $this->serviceClass->addToUserDetails($data);
            $data2  = [
                'regData'   => $this->regData,
                'productData' => $productData,
                'user'  => $user
            ];
            $this->serviceClass->addToRegistrationDetails($data2);

            $this->serviceClass->insertTreepath($fatherData, $user);

            $this->serviceClass->createTransPassword($user);
            if(isset($regData['custom'])){
                $this->serviceClass->addToCustomDetails($regData, $user->id);
            }
            if ($mlmplan == 'Binary') {
                $this->serviceClass->addLegDetails($user);
            } elseif ($mlmplan == 'Stair_Step') {
                $this->serviceClass->addToStairStep($user);
                $this->serviceClass->addToUserPvDetails($user);
            }
            $this->serviceClass->sponsorTreePath($sponsorData, $user);

            $this->serviceClass->addToUserBalance($user);

            if ($this->moduleStatus['product_status']) {
                $this->serviceClass->updateGroupPV($user->sponsor, $user->personal_pv, $user->id);
            }

            $paymentType = PaymentGatewayConfig::find($this->regData['payment_method']);
            if ($this->moduleStatus->roi_status) {
                $this->serviceClass->insertRoi($user, $productData, $paymentType);
            }

            $paymentStatus = $this->serviceClass->checkPaymentMethod($this->moduleStatus, $paymentType, $this->regData, $user, $sponsorData, 'registration');
            if ($paymentStatus) {
                DB::commit();
                $commissionStatus = $this->serviceClass->runCommission($user, $sponsorData, $productData);

                return $commissionStatus;
            }
        } catch (Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return [
                'status' => false,
                'error' => 'user_activation_failed',
            ];
        }
    }
}
