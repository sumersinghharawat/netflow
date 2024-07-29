<?php

namespace App\Services;

use App\Http\Controllers\CoreInfController;
use App\Models\ {
    AggregateUserCommissionAndIncome,
    UserpvDetails,
    UserDetail,
    UserBalanceAmount,
    User,
    Treepath,
    Transaction,
    SubscriptionConfig,
    StairStep,
    SignupSetting,
    SignupField,
    SalesOrder,
    RoiOrder,
    PvHistoryDetail,
    PinUsed,
    PinNumber,
    PendingRegistration,
    PaypalOrder,
    PaymentReceipt,
    PaymentGatewayConfig,
    Package,
    MonolineConfig,
    ModuleStatus,
    CustomfieldValues,
    Country,
    Configuration,
    UserPlacement,
    SponsorTreepath
};
use App\Services\SendMailService;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use stdClass;
use Throwable;
use App\Events\UserRegistered;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

class UserApproveService
{
    public function getPlacementData($position, $sponsor_id, $fromTree = false)
    {
        if ($fromTree) {
            return null;
        }
        $targetPosition = ($position == 'R') ? 2 : 1;

        $sponsor        = User::where('id',$sponsor_id)
                                ->with('placementData.leftMost','placementData.rightMost', 'placementData.branchParent')
                                ->first();
        $branchParent   = $sponsor->placementData->branchParent;
        $leftMost       = $sponsor->placementData->leftMost;
        $rightMost      = $sponsor->placementData->rightMost;
        if($branchParent && ($targetPosition == $sponsor->leg_position)){
            $branchParent   = UserPlacement::where('user_id', $branchParent->id)->with('rightMost', 'leftMost')->first();
            $leftMost       = ($branchParent->leftMost) ? $branchParent->leftMost : $branchParent->user_id;
            $rightMost      = ($branchParent->rightMost) ? $branchParent->rightMost : $branchParent->user_id;
        }
        else
        {
            $leftMost       = ($sponsor->placementData->leftMost) ? $sponsor->placementData->leftMost: $sponsor;
            $rightMost      = ($sponsor->placementData->rightMost) ? $sponsor->placementData->rightMost : $sponsor;
        }
        return (object) [
            'fatherId' => ($targetPosition == 1) ? $leftMost->id : $rightMost->id,
            'father' => ($targetPosition == 1) ? $leftMost->username : $rightMost->username,
            'positionString' => $targetPosition,
        ];
    }

    public function getPendingRegistarion($users = [])
    {
        $resultArray = collect([]);
        if (empty($users)) {
            $pendingUsers = PendingRegistration::whereIn('status', ['pending', 'processing', 'failed'])
                ->with('RegistraionPackage', 'sponsorData.userDetail', 'paymentGateway', 'bankReciept')->latest()->get();
        }
        if (!empty($users)) {
            $pendingUsers = PendingRegistration::whereIn('status', ['pending', 'processing', 'failed'])
                ->whereIn('id', $users)->with('RegistraionPackage', 'sponsorData.userDetail', 'paymentGateway', 'bankReciept')->latest()->get();
        }
        $currency = currencySymbol();
        foreach ($pendingUsers as $key => $pendingUser) {
            $decodeData = collect(json_decode($pendingUser->data));
            $countryId = (isset($decodeData['country']) ? intval($decodeData['country']) : $decodeData['default_country'] ?? 'NA');
            // $country = Country::find($countryId)->name ?? 'NA';
            $country = (new CoreInfController())->countries()->find($countryId)->name ?? 'NA';
            $moduleStatus = (new CoreInfController())->moduleStatus();
            $listData = collect([
                'username' => $pendingUser->username,
                'payment' => $pendingUser->paymentGateway->slug,
                'paymentMethod' => $pendingUser->paymentGateway,
                'id' => $pendingUser['id'],
                'sponsor' => $pendingUser->sponsorData->userDetail->name . ' ' . $pendingUser->sponsorData->userDetail->second_name . ' (' . $pendingUser->sponsorData->username . ')',
                'package' => ($moduleStatus->product_status ? $pendingUser->RegistraionPackage->name . ' ' . '(Amount : ' . $currency . ' ' . formatCurrency($pendingUser->RegistraionPackage->price) . ')' : ''),
                'sponsorUsername' => $pendingUser->sponsorData->username,
                'status' => $pendingUser->status,
                'country' => $country,
                'failed_reason' => $pendingUser->failed_reason
            ]);

            if ($listData['payment'] == 'bank-transfer' && $pendingUser->bankReciept) {
                $listData->put('receipt', $pendingUser->bankReciept->receipt);
            }
            $resultArray->push($decodeData->merge($listData));
        }

        return $resultArray;
    }

    public function addToUsers($regData, $fatherData, $placementData, $productData, $sponsorData, $plan, $productValidity, $productStatus, $subscriptionStatus)
    {
        if (!$plan) {
            return false;
        }
        $password = $regData['password'];
        unset($regData['password']);
        unset($regData['date_of_joining']);
        if ($productStatus == 0 && $subscriptionStatus == 1) {
            $subscriptionConfig = SubscriptionConfig::first();
            $productValidity = date('Y-m-d H:i:s', strtotime('+' . $subscriptionConfig['subscription_period'] . ' months'));
        } elseif ($productStatus == 1 && $subscriptionStatus == 1) {
            $productValidity = $productValidity;
        } else {
            $productValidity = null;
        }
        $user = new User();
        $user->user_type = 'user';
        $user->email = $regData['email'];
        $user->father_id = $placementData->fatherId;
        $user->user_level = $fatherData->user_level + 1;
        $user->sponsor_level = $sponsorData->sponsor_level + 1;
        $user->sponsor_index = User::where('sponsor_id', $regData['sponsor_id'])->count() + 1;
        $user->personal_pv = $productData->pair_value ?? 0;
        $user->product_validity = $productValidity;
        $user->password = Hash::make($password);
        $user->date_of_joining = Carbon::now();
        $user->year = Carbon::now()->year;
        $user->year_month = Carbon::now()->format('Y-m');
        $user->from_tree = $regData['regFromTree'];
        $user->fill($regData);
        if ($plan == 'Binary') {
            $user->position = $regData['position'];
            $user->leg_position = ($regData['position'] == 'R') ? 2 : 1;
        } else {
            $user->position = $placementData->positionString ?? $placementData->position;
            $user->leg_position = $placementData->positionString ?? $placementData->position;
        }
        $user->save();

        return $user;
    }

    public function addToUserDetails($data)
    {
        $coreController = new CoreInfController;
        $user_details = [
            'sponsor_id' => $data['pendingUser']->sponsor_id ?? $data['regData']['sponsor_id'],
            'country_id' => $data['regData']['country'] ?? $coreController->signupSettings()['default_country'],
            'name' => $data['regData']['first_name'] ?? 'Your First Name',
            'second_name' => $data['regData']['last_name'] ?? '',
            'DOB' => date('Y.m.d', strtotime($data['regData']['date_of_birth'] ?? '1992-5-25')) ?? '1992-5-25',
            // 'email' => $data['regData']['email'] ?? 'youremail@email.com',
            'mobile' => $data['regData']['mobile'] ?? '9999999999',
            'join_date' => $data['regData']['date_of_joining'] ?? now(),
            'gender' => $data['regData']['gender'] ?? 'M',
            'payout_type' => PaymentGatewayConfig::where('slug', 'bank-transfer')->first()->id ?? null,
            'state_id' => isset($data['regData']['state']) ? (int)$data['regData']['state'] : null,
            'pin' => $data['regData']['zip_code'] ?? null
        ];
        return $data['user']->userDetails()->create($user_details);
    }

    public function addToRegistrationDetails($data)
    {
        $coreController = new CoreInfController;
        $userRegDetails = [
            'username' => $data['regData']['username'],
            'name' => $data['regData']['first_name'] ?? 'Your First Name',
            'second_name' => $data['regData']['last_name'] ?? '',
            'reg_amount' => $data['regData']['reg_amount'],
            'total_amount' => $data['regData']['totalAmount'] ?? $data['regData']['reg_amount'],
            'product_id' => $data['regData']['product_id'] ?? null,
            'product_amount' => $data['regData']['product_amount'] ?? $data['productData']->price ?? 0,
            'product_pv' => $data['regData']['product_pv'] ?? $data['productData']->pair_value ?? 0,
            'email' => $data['regData']['email'] ?? 'youremail@email.com',
            'payment_method' => $data['regData']['payment_method'] ?? PaymentGatewayConfig::where('slug', 'free-joining')->first()->id,
            'country_id' => $data['regData']['default_country'] ?? $data['regData']['country'] ?? $coreController->signupSettings()['default_country'],
            'oc_product_id' => $data['regData']['oc_product_id'] ?? NULL,
            'year' => Carbon::now()->year,
            'year_month' => Carbon::now()->format('Y-m'),
        ];

        return $data['user']->userRegDetails()->create($userRegDetails);
    }

    public function insertTreepath($fatherData, $user)
    {
        $prefix = config('database.connections.mysql.prefix');
        $treePathData['ancestor']    = $user->id;
        $treePathData['descendant']  = $user->id;
        $treePathData['depth']       = 0;
        $treePathData['created_at']  = now();
        $treePathData['updated_at']  = now();
        Treepath::insert($treePathData);
        $treepathInsertData = DB::table('treepaths')
                ->select('users.id as ancestor', DB::raw($user->user_level.'- '.$prefix.'users.user_level as depth'), DB::raw($user->id.' as descendant'))
                ->where('descendant', $fatherData->id)
                ->join('users', 'users.id', 'treepaths.ancestor')
                ->orderBy('ancestor', 'DESC')
                ->get();
        $insertData = [];
        foreach ($treepathInsertData as $k => $data){
            $insertData[$k] = (array)$data;
            $insertData[$k]['created_at'] = now();
            $insertData[$k]['updated_at'] = now();
        }
        Treepath::insert($insertData);
    }

    public function createTransPassword($user)
    {
        $tran_pas = Hash::make('12345678');
        if ($user->transPassword()->exists()) {
            DB::rollBack();

            return redirect()->back()->withErrors('Teansaction password Insertion failed');
        }
        $user->transPassword()->create([
            'password' => $tran_pas,
        ]);
    }

    public function addLegDetails($user)
    {
        $user->legDetails()->create([
            'total_left_count' => 0,
        ]);
        if (!$user->legDetails()->exists()) {
            DB::rollBack();

            return redirect()->back()->withErrors('Leg details Insertion failed');
        }
    }

    public function sponsorTreePath($sponsorData, $user)
    {
        $prefix = config('database.connections.mysql.prefix');
        $treePathData['ancestor']    = $user->id;
        $treePathData['descendant']  = $user->id;
        $treePathData['created_at']  = now();
        $treePathData['updated_at']  = now();
        $treePathData['depth']       = 0;

        SponsorTreepath::insert($treePathData);
        $treepathInsertData = DB::table('sponsor_treepaths')
                ->select('users.id as ancestor', DB::raw($user->sponsor_level.'- '.$prefix.'users.sponsor_level as depth'), DB::raw($user->id.' as descendant'))
                ->where('descendant', $sponsorData->id)
                ->join('users', 'users.id', 'sponsor_treepaths.ancestor')
                ->orderBy('ancestor', 'DESC')
                ->get();
        if (count($treepathInsertData) > 1000) {
            $treepathInsertData->chunk(10000, function($da){
                $da['created_at'] = now();
                $da['updated_at'] = now();
                SponsorTreepath::insert($da);
            });
        } else {
            $da = $treepathInsertData->map( function($item){
                $data['ancestor'] = $item->ancestor;
                $data['descendant'] = $item->descendant;
                $data['created_at'] = now();
                $data['updated_at'] = now();
                $data['depth'] = $item->depth;
                return $data;
            });
            SponsorTreepath::insert($da->toArray());
        }

    }

    public function addToUserBalance(User $user)
    {
        $userBalance = new UserBalanceAmount;
        $userBalance->balance_amount = 0;
        $userBalance->purchase_wallet = 0;
        $user->userBalance()->save($userBalance);
    }

    public function addToPaymentReceipt($pendingUserId, $user)
    {
        $PaymentReceipt = PaymentReceipt::where('pending_registrations_id', $pendingUserId)->first();
        $PaymentReceipt->update([
            'user_id'   =>  $user->id,
        ]);
    }

    public function getUnilevelPlacement($sponsor)
    {
        $sponsorDetails = User::findOrfail($sponsor);
        $placementDetails = User::where('father_id', $sponsor)->count();

        return (object) [
            'fatherId' => $sponsorDetails->id,
            'father' => $sponsorDetails->username,
            'positionString' => $placementDetails + 1,
        ];
    }

    public function updateGroupPV($user, $pv, $userId, $action = 'register')
    {
        if ($user) {
            $newPv  = $pv + $user->group_pv;
            $user->group_pv = $newPv;
            $user->save();
            $user->load('sponsor');
            if ($user->sponsor != null) {
                $this->updateGroupPV($user->sponsor, $pv, $userId , $action);
            }
            $this->insertPVhistoryDetails($user->id, $pv, 'group_pv', $userId, $action);
        }
    }
    public function insertPVhistoryDetailsNew($users, $pv, $pvType, $userId, $pvObtainedBy)
    {
        foreach ($users as $key => $user) {
            $insertData[$key]['user_id']        = $user;
            $insertData[$key]['from_id']        = $userId;
            $insertData[$key]['pv_obtained_by'] = $pvObtainedBy;
            if ($pvType == 'personal_pv') {
                $insertData[$key]['personal_pv']= $pv;
            } else {
                $insertData[$key]['group_pv']   = $pv;
            }
            $insertData[$key]['created_at']      = now();
            $insertData[$key]['updated_at']     = now();
        }
        PvHistoryDetail::insert($insertData);
    }

    public function insertPVhistoryDetails($user_id, $pv, $pvType, $userId, $pvObtainedBy)
    {
        $PvHistoryDetail = new PvHistoryDetail();
        if ($pvType == 'personal_pv') {
            $PvHistoryDetail->personal_pv = $pv;
        } else {
            $PvHistoryDetail->group_pv = $pv;
        }
        $PvHistoryDetail->user_id = $user_id;
        $PvHistoryDetail->from_id = $userId;
        $PvHistoryDetail->pv_obtained_by = $pvObtainedBy;
        $PvHistoryDetail->save();

        return $PvHistoryDetail;
    }

    public function getPendingSignupStatus($id)
    {
        $status = PaymentGatewayConfig::find($id);

        if ($status->slug == 'bank-trasfer') {
            return 1;
        }

        return $status->reg_pending_status;
    }

    public function addPendingRegistration($pendingData, $paymentTypeId, $emailVerificationStatus)
    {
        try {
            DB::beginTransaction();
            $paymentGateWay = PaymentGatewayConfig::findOrfail($paymentTypeId);
            $pendingData = PendingRegistration::create([
                'username' => $pendingData['username'],
                'email' => $pendingData['email'],
                'payment_method' => $paymentGateWay->id,
                'data' => json_encode($pendingData),
                'date_added' => now(),
                'email_verification_status' => $emailVerificationStatus,
                'package_id' => $pendingData['product_id'] ?? null,
                'sponsor_id' => $pendingData['sponsor_id'],
            ]);
            if ($paymentGateWay->slug == 'bank-transfer') {
                $paymentReceipt = PaymentReceipt::where('username', $pendingData['username'])->first();
                $paymentReceipt->update([
                    'pending_registrations_id' => $pendingData->id,
                ]);
            }
            DB::commit();

            return $pendingData->id ?? false;
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors('registration_failed');
        }
    }

    public function confirmRegister($regData, $moduleStatus, $productValidity)
    {
        try {
            DB::beginTransaction();
            $sponsorId = $regData['sponsor_id'];
            $position = $regData['position'];
            $mlmplan = $moduleStatus['mlm_plan'];
            $productData = [];
            if ($moduleStatus['product_status']) {
                $packageId = $regData['product_id'];
                $productData = Package::find($packageId);
            }
            if ($mlmplan == 'Binary') {
                $placementData = $this->getPlacementData($regData['position'], $regData['sponsor_id'], $regData['regFromTree']);
            } elseif ($mlmplan == 'Unilevel' || $mlmplan == 'Stair_Step') {
                $placementData = $this->getUnilevelPlacement($regData['sponsor_id']);
            } elseif ($mlmplan == 'Donation') {
                $placementData = $this->getDonationPlacement($regData['sponsor_id']);
            } elseif ($mlmplan == 'Matrix') {
                $placementData = $this->getMatrixPlacement($regData['sponsor_id'], $regData['regFromTree']);
            } elseif ($mlmplan == 'Party') {
                $placementData = $this->getPartyPlacement($regData['sponsor_id']);
            } elseif ($mlmplan == 'Stair_Step') {
                $placementData = $this->getStairStepPlacement($regData['sponsor_id']);
            } elseif ($mlmplan == 'Monoline') {
                $placementData = $this->monolinePlacement();
            }
            if ($regData['regFromTree'] && !$placementData) {
                $placementUserDetails = User::where('username', $regData['placement_username'])->first();
                if ($this->checkPositionAvailable($placementUserDetails->id, $regData['position'], $regData['sponsor_id'])) {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'error' => 'placement_not_available',
                    ];
                }
                $placementData = (object) [
                    'fatherId' => $placementUserDetails->id,
                    'father' => $placementUserDetails->username,
                    'positionString' => $regData['position'],
                ];
            }

            $fatherData = User::find($placementData->fatherId)->load('ancestors');
            $sponsorData = User::withCount('sponsorDescendant')->find($regData['sponsor_id'])->load('sponsorAncestors','userBalance');
            if ($regData['product_id'] != '') {
                $servicePackageupgrade = new PackageUpgradeService;
                $product_validity = $servicePackageupgrade->getPackageValidityDate($regData['product_id'], '', $moduleStatus);
            } else {
                $product_validity = '';
            }
            $user = $this->addToUsers($regData, $fatherData, $placementData, $productData, $sponsorData, $moduleStatus->mlm_plan, $product_validity, $moduleStatus->product_status, $moduleStatus->subscription_status);
            if (!$user) {
                DB::rollback();
                return [
                    'status' => false,
                    'error' => 'user_activation_failed',
                ];
            }
            if ($mlmplan == 'Binary') {
                $this->updatePlacementTable(sponsor: $sponsorData, user: $user, position: $regData['position'], fromTree: $regData['regFromTree'], fatherData: $fatherData);
            }
            $pendingUser = new stdClass();
            $pendingUser->sponsor_id = $sponsorId;

            $this->addToUserDetails(compact('pendingUser', 'regData', 'user', 'regData'));

            $this->addToRegistrationDetails(compact('regData', 'productData', 'user'));
            $this->insertTreepath($fatherData, $user);

            $this->createTransPassword($user);
            if (isset($regData['custom'])) {
                $this->addToCustomDetails($regData, $user->id);
            }

            if ($mlmplan == 'Binary') {
                $this->addLegDetails($user);
            } elseif ($mlmplan == 'Stair_Step') {
                $this->addToStairStep($user);
                $this->addToUserPvDetails($user);
            }

            $this->sponsorTreePath($sponsorData, $user);

            $this->addToUserBalance($user);

            if ($moduleStatus['product_status']) {
                $this->updateGroupPV($user->sponsor, $user->personal_pv, $user->id);
                $invoice_no = $this->generateSalesInvoiceNumber();
                $this->addToSalesOrder($regData, $invoice_no, null, $user->id);
            }

            $paymentType = PaymentGatewayConfig::findOrfail($regData['payment_method']);
            if ($moduleStatus->roi_status) {
                $this->insertRoi($user, $productData, $paymentType);
            }
            if($mlmplan == "Monoline") {
                $this->addToUserReentry($user);
            }

            $paymentStatus = $this->checkPaymentMethod($moduleStatus, $paymentType, $regData, $user, $sponsorData, 'registration');

            if ($paymentStatus) {
                DB::commit();
                $prefix = str_replace("_", "", config('database.connections.mysql.prefix'));
                $commissionStatus = $this->runCommission($user, $sponsorData, $productData, $prefix);
                if ($moduleStatus->mlm_plan == 'Monoline') {
                    $this->runReentryCommission($sponsorData->id, $user->id);
                }
                $serviceClass = new SendMailService;
                // uncomment for registration mails

                // $serviceClass->sendAllEmails('registration', $user, $regData);
                // UserRegistered::dispatch($user, $regData);
                return $commissionStatus;
            }
        } catch (Throwable $th) {
            throw $th;
            DB::rollBack();
            return [
                'status' => false,
                'error' => 'user_activation_failed',
            ];
        }
    }

    public function checkPaymentMethod($moduleStatus, $paymentType, $regData, $user, $sponsorData, $type = '')
    {
        $ewalletService = new EwalletService();
        try {
            switch ($paymentType->slug) {
                case 'e-pin':
                    $count = count($regData['epinOld']);
                    foreach ($regData['epinOld'] as $pinId => $value) {
                        $epin = PinNumber::findOrfail($pinId);
                        $balanceAmount = $epin->balance_amount;
                        $balance = $balanceAmount - $regData['epinUsedAmount'][$pinId];
                        if ($balance == 0) {
                            $epin->update([
                                'status' => 'used',
                            ]);
                        }
                        $epin->update([
                            'balance_amount' => $balance,
                        ]);
                        $usedEpin = new PinUsed;
                        $usedEpin->epin_id = $pinId;
                        $usedEpin->used_by = User::GetAdmin()->id;
                        $usedEpin->amount = $regData['epinUsedAmount'][$pinId];
                        $usedEpin->used_for = 'registration';
                        $usedEpin->save();
                    }

                    return true;
                    break;
                case 'e-wallet':
                    $ewalletUser = $sponsorData;
                    $transactionNumber = generateTransactionNumber();
                    $amount = $regData['totalAmount'];
                    $TransactionId = $this->insertTransaction($transactionNumber);
                    $insertUsedWallet = $ewalletService->insertUsedEwallet($moduleStatus, $user, $ewalletUser, $amount, $TransactionId, $type);
                    if ($insertUsedWallet) {
                        $ewalletService->deductUserBalance($sponsorData, $amount);
                    }

                    return true;
                    break;
                case 'purchase_wallet':
                    $ewalletUser = $sponsorData;
                    $transactionNumber = generateTransactionNumber();
                    $amount = $regData['totalAmount'];
                    $TransactionId = $this->insertTransaction($transactionNumber);
                    $ewalletService->deductPurchaseBalance($sponsorData, $amount, $moduleStatus, $user, $TransactionId, $type);
                    return true;
                    break;
                case 'stripe':
                    $serviceClass = new StripeService;
                    $serviceClass->payment($regData, $user);

                    return true;
                    break;
                case 'paypal':
                    $successTrans = PaypalOrder::where('order_id', $regData['paypalOrderId'])->where('status', 1)->get();
                    $successCount = $successTrans->count();
                    if ($successCount > 0) {
                        return true;
                        break;
                    }
                    return false;
                    break;
                case 'free-joining':
                    return true;
                    break;
            }
        } catch (Throwable $th) {
            DB::rollBack();
            Throw $th;

            return false;
        }
        // TODO other payment methods
    }

    public function insertTransaction($transactionNumber)
    {
        $transaction = new Transaction();
        $transaction->transaction_id = $transactionNumber;
        $transaction->save();

        return $transaction->id;
    }

    public function approveDummy($pendingUser, $moduleStatus)
    {
        $regData = json_decode($pendingUser->data, true);
        $regData['regFromTree'] = false;
        try {
            $productData = Package::find($regData['product_id']);
            $plan = $moduleStatus->mlm_plan;
            if ($plan == 'Binary') {
                $placementData = $this->getPlacementData($regData['position'], $regData['sponsor_id']);
            } elseif ($plan == 'Unilevel') {
                $placementData = $this->getUnilevelPlacement($regData['sponsor_id']);
            } elseif ($plan == 'Donation') {
                $placementData = $this->getDonationPlacement($regData['sponsor_id']);
            } elseif ($moduleStatus->mlm_plan == 'Matrix') {
                $placementData = $this->getMatrixPlacement($regData['sponsor_id']);
            } elseif ($plan == 'Stair_Step') {
                $placementData = $this->getStairStepPlacement($regData['sponsor_id']);
            } elseif ($plan == 'Party') {
                $placementData = $this->getPartyPlacement($regData['sponsor_id']);
            }
            $fatherData = User::find($placementData->fatherId)->load('ancestors');
            $sponsorData = User::find($regData['sponsor_id'])->load('sponsorAncestors')->loadCount(['sponsorDescendant' => fn($qry) => $qry->where('depth', 1) ]);
            if ($regData['product_id'] != '') {
                $servicePackageupgrade = new PackageUpgradeService;
                $product_validity = $servicePackageupgrade->getPackageValidityDate($regData['product_id'], '', $moduleStatus);
            } else {
                $product_validity = '';
            }
            $user = $this->addToUsers($regData, $fatherData, $placementData, $productData, $sponsorData, $moduleStatus->mlm_plan, $product_validity, $moduleStatus->product_status, $moduleStatus->subscription_status);
            if (!$user) {
                return redirect()->back()->withErrors('User activation failed');
            }

            $request = new stdClass();
            $request->gender = 'M';
            if ($plan == 'Binary') {
                $this->updatePlacementTable(sponsor: $sponsorData, user: $user, position: $regData['position'], fromTree: $regData['regFromTree'], fatherData: $fatherData);
            }
            $this->addToUserDetails(compact('pendingUser', 'regData', 'user', 'request'));
            $this->addToRegistrationDetails(compact('regData', 'productData', 'user'));

            $this->insertTreepath($fatherData, $user);

            $this->createTransPassword($user);

            if (isset($regData['custom'])) {
                $this->addToCustomDetails($regData, $user->id);
            }
            if ($plan == 'Binary') {
                $this->addLegDetails($user);
            } elseif ($plan == 'Stair_Step') {
                $this->addToStairStep($user);
                $this->addToUserPvDetails($user);
            }

            $this->sponsorTreePath($sponsorData, $user);

            $this->addToUserBalance($user);

            if ($moduleStatus->product_status) {
                $this->updateGroupPV($user->sponsor, $user->personal_pv, $user->id);
            }

            PendingRegistration::find($pendingUser->id)->update([
                'status' => 'active',
                'updated_id' => $user->id,
            ]);

            return $user->load('userDetail', 'userRegDetails');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
            // return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getDonationPlacement($sponsor_id)
    {
        $count = User::where('father_id', $sponsor_id)->count();
        $sponsor = User::find($sponsor_id);

        return (object) [
            'fatherId' => $sponsor_id,
            'father' => $sponsor->username,
            'positionString' => $count + 1,
        ];
    }

    public function getMatrixPlacement($sponsorId, $fromTree = false)
    {
        if ($fromTree) {
            return null;
        }

        $coreController = new CoreInfController;
        $widthCeiling = $coreController->configuration()->width_ceiling ?? 0;
        $sponsorData = User::findOrFail($sponsorId);
        $sponsorUserLevel = $sponsorData->user_level;
        $prefix = config('database.connections.mysql.prefix');

        $res = Treepath::selectRaw("{$prefix}member.id,{$prefix}member.username, (SELECT COUNT(*) FROM `{$prefix}users` WHERE `father_id` = `{$prefix}member`.`id`) AS leg_count, GROUP_CONCAT(`{$prefix}parent`.`leg_position` ORDER BY `{$prefix}parent`.`user_level` ASC SEPARATOR '-') AS groupOrder")
            ->join('users as member', 'member.id', '=', 'treepaths.descendant')
            ->join('treepaths as downlines', 'downlines.descendant', '=', 'treepaths.descendant')
            ->join('users as parent', 'parent.id', '=', 'downlines.ancestor')
            ->where('treepaths.ancestor', $sponsorData->id)
            ->where('parent.user_level', '>=', $sponsorUserLevel)
            ->groupBy('treepaths.descendant')
            ->orderBy('member.user_level')
            ->having('leg_count', '<', $widthCeiling)
            ->orderBy('groupOrder')
            ->limit(1)
            ->first();
        if ($res) {
            $fatherData = User::find($res->id);

            return (object) [
                'fatherId' => $res->id,
                'father' => $fatherData->username,
                'positionString' => $res->leg_count + 1,
            ];
        }

        return (object) [
            'fatherId' => $sponsorId,
            'father' => $sponsorData->username,
            'positionString' => 1,
        ];
    }

    public function getStairStepPlacement($placement)
    {
        $count = User::where('father_id', $placement)->count();
        $position = $count + 1;

        return (object) [
            'fatherId' => $placement,
            'father' => User::find($placement),
            'positionString' => $position,
        ];
    }

    public function addToStairStep($newUser)
    {
        $stairStep = new StairStep;
        $leader = StairStep::where('user_id', $newUser->sponsor_id)->first();

        $stairStep->step_id = null;
        $stairStep->breakaway_status = 0;
        $stairStep->leader_id = ($leader->leader_id) ? $leader->leader_id : $newUser->sponsor_id;
        $stairStep->user_id = $newUser->id;
        $stairStep->save();

        return true;
    }

    public function addToUserPvDetails($newUser)
    {
        $pvDetails = new UserpvDetails;
        $pvDetails->user_id = $newUser->id;
        $pvDetails->total_pv = 0;
        $pvDetails->total_gpv = 0;
        $pvDetails->save();

        return true;
    }

    public function getUserProductValidity($userId)
    {
        $user = User::where('id', $userId)->first();

        return $user->product_validity;
    }

    public function addDummyUsers($userCount = null)
    {
        try {
            $count = $userCount ?? 10;
            $moduleStatus = ModuleStatus::first();
            $signupFields = SignupField::Active()->get();
            $mandatoryFields = $signupFields->where('required', 1)->all();
            $customFields = $signupFields->where('required', 1)->where('is_custom', 1)->all();
            $regData = collect([]);
            $firstSponsor = User::where('user_type', '!=', 'employee')->with('userDetail', 'userRegDetails')->get();
            $faker = Factory::create();
            if ($moduleStatus->product_status) {
                $package = Package::ActiveRegPackage()->get();
                if ($moduleStatus['ecom_status']) {
                    // TODO functions and redirect store route
                }
            }

            switch ($moduleStatus->mlm_plan) {
                case 'Binary':
                    $width = 2;
                    break;
                case 'Matrix':
                    $width = Configuration::first()->width_ceiling ?? 2;
                    break;
                default:
                    $width = -1;
                    break;
            }

            for ($i = 1; $i <= $count; $i++) {
                DB::beginTransaction();
                $product = $package->random();
                $config = Configuration::first();
                $signupSettings = SignupSetting::first();
                $payment = PaymentGatewayConfig::where('slug', 'free-joining')->first();
                $totalAmount = round($config->reg_amount + $product->price);
                $defaultCountry = $signupSettings->default_country;
                $password = '123456';
                $position = collect(['L', 'R']);
                $sponsor = $firstSponsor->random();
                $position = $i % $width + 1;
                $username = generateUsername(8, 16);
                $regPack = $product;
                if ($width <= 0) {
                    $position = $i % 5 + 1;
                } elseif ($moduleStatus->mlm_plan == 'Binary') {
                    $position = $i % $width + 1;
                    $position = ($position <= 1) ? 'L' : 'R';
                } else {
                    $position = $i % $width + 1;
                }
                $regData = collect([
                    'sponsorName' => $sponsor->username,
                    'sponsorFullname' => $sponsor->userDetail->name . '' . $sponsor->userDetail->second_name,
                    'position' => $position,
                    'sponsor_id' => $sponsor->id,
                    'product_id' => $regPack->id,
                    'mlm_plan' => $moduleStatus->mlm_plan,
                    'username_type' => 'static',
                    'productStatus ' => 'yes',
                    'default_country' => 99,
                    'first_name' => $faker->name,
                    'last_name' => $faker->name,
                    'date_of_birth' => Carbon::now()->subYears(19),
                    'email' => $faker->unique()->safeEmail(),
                    'mobile' => $faker->phoneNumber,
                    'username' => $username,
                    'password' => $password,
                    'password_confirmation' => $password,
                    'payment_method' => $payment->id,
                    'terms' => 'yes',
                    'date_of_joining' => Carbon::now()->format('Y-m-d H:i:s'),
                    'totalAmount' => $totalAmount,
                    'reg_amount' => $config->reg_amount,
                    'product_amount' => $product->price,
                    'product_pv' => $product->pair_value,
                ]);

                if ($moduleStatus->mlm_plan == 'Unilevel' || $moduleStatus->mlm_plan == 'Stair_Step') {
                    $regData['placement_user_name'] = $sponsor->username;
                }
                $pendingData = PendingRegistration::create([
                    'username' => $username,
                    'package_id' => $regPack->id,
                    'sponsor_id' => $sponsor->id,
                    'data' => json_encode($regData),
                    'payment_method' => $payment->id,
                    'date_added' => Carbon::now()->format('Y-m-d H:i:s'),
                    'email_verification_status' => 'no',
                ]);
                $newUser = $this->approveDummy($pendingData, $moduleStatus);
                $firstSponsor->push($newUser);
                DB::commit();
                // $commissionStatus = $this->runCommission($newUser, $sponsor, $product);
            }

            return redirect()->route('dashboard')->with('success', 'Dummy users created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function checkPositionAvailable(int $placement, $position, int $sponsorId)
    {
        $checkPositionAncestor = User::with('ancestors')->findOrfail($placement);
        if ($checkPositionAncestor->ancestors->where('id', $sponsorId)->isEmpty()) {
            return true;
        }
        return User::where('father_id', $placement)->where('position', $position)->count();
    }

    public function rejectPendingRegistartion($pendingUser)
    {
        DB::beginTransaction();
        try {
            foreach ($pendingUser as $user) {
                $user->update([
                    'status' => 'rejected',
                    'date_modified' => now(),
                ]);
            }
            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();

            return false;
        }
    }

    public function insertRoi($user, $package, $paymentGateway)
    {
        $packageUpgradeService = new PackageUpgradeService;
        RoiOrder::create([
            'package_id' => $package->id,
            'user_id' => $user->id,
            'amount' => $package->price,
            'payment_method' => $paymentGateway->id,
            'pending_status' => 1,
            'roi' => $packageUpgradeService->getRoi($package->id),
            'days' => $packageUpgradeService->getRoiDays($package->id),
        ]);
    }

    public function runCommission($user, $sponsorData, $productData, $prefix)
    {

        $commission = new commissionService;
        $coreController = new CoreInfController;

        $compensation = $coreController->compensation();
        $error = false;
        if (!$commission->planCommission($user, $sponsorData, null, $productData->id ?? null, $productData->pair_value ?? null, $productData->price ?? null, $prefix)) {
            $error = 'error_in_plan_commission';
        }
        if (!$commission->referralCommission($user, $sponsorData, $productData, $prefix)) {
            $error = 'error_in_plan_commission';
        }
        if (!$commission->levelCommission($user, $sponsorData, null, $productData->id ?? null, $productData->pair_value ?? null, $productData->price ?? null, $prefix)) {
            $error = 'error_in_plan_commission';
        }

        // if ($compensation->pool_bonus) {
        //     if (!$commission->poolBonus($prefix)) {
        //         $error = 'error_in_pool_bonus';
        //     }
        // }
        if ($compensation['rank_commission']) {
            if (!$commission->rankCommission($user, $prefix)) {
                $error = 'error_in_ranks';
            }
        }
        // if ($compensation->roi_commission) {
        //     if (!$commission->roi($prefix)) {
        //         $error = 'error_in_roi';
        //     }
        // }
        // if ($compensation->fast_start_bonus) {
        //     if (!$commission->fastStartBonus($sponsorData, $prefix)) {
        //         $error = 'error_in_fast_start';
        //     }
        // }
        if ($compensation['performance_bonus']) {
            if (!$commission->performanceBonus($sponsorData, $prefix)) {
                $error = 'error_in_performance_bonus';
            }
        }
        if ($error) {
            return [
                'status' => false,
                'error' => $error,
            ];
        }

        return [
            'status' => true,
            'error' => '',
        ];
    }

    public function getPartyPlacement($placement)
    {
        $count = User::where('father_id', $placement)->count();
        $position = $count + 1;

        return (object) [
            'fatherId' => $placement,
            'father' => User::find($placement),
            'positionString' => $position,
        ];
    }

    public function addToSalesOrder($data, $invoice_no, $pendingUser = null, $userId = null)
    {
        try {
            $order = new SalesOrder();
            $order->product_id = $data['product_id'];
            $order->amount = $data['product_amount'];
            $order->product_pv = $data['product_pv'];
            $order->payment_method = $data['payment_method'];
            $order->user_id = $userId;
            $order->pending_user_id = $pendingUser;
            $order->invoice_no = $invoice_no;
            $order->reg_amount = $data['reg_amount'];
            $order->save();
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function generateSalesInvoiceNumber()
    {
        $invoice = SalesOrder::max('id') ?? 0;
        $invoice_no = 1000 + $invoice;
        $invoice_no = 'SALE' . $invoice_no;

        return $invoice_no;
    }
    public function addToCustomDetails($regData, $userId)
    {
        if (!isset($regData['custom'])) return true;
        try {
            $data = collect($regData['custom']);
            $fields = $data->map(function ($item, $key) use ($userId) {
                if ($item) {
                    return [
                        'customfield_id' => $key,
                        'value' => $item,
                        'user_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            })->filter();
            CustomfieldValues::insert($fields->toArray());
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function monolinePlacement()
    {
        $father = User::whereIn('user_type', ['admin', 'user'])->latest('user_level')->first();
        $position = $father->user_level + 1;
        return (object) [
            'fatherId' => $father->id,
            'father' => $father,
            'positionString' => $position,
            'user_level' => $father->user_level,
        ];
    }

    public function runReentryCommission($newUser)
    {
        try {
            $config = MonolineConfig::first();
            $prefix = session()->get('prefix').'_';
            $users = User::join('packages as userpackages', 'users.product_id', '=', 'userpackages.id')
                            ->join('treepaths', 'users.id', '=', 'treepaths.ancestor')
                            ->join('users as descendants', 'descendants.id', '=', 'treepaths.descendant')
                            ->select('users.*', 'userpackages.reentry_limit', DB::raw('COUNT(DISTINCT CASE WHEN '.$prefix.'descendants.user_type = "admin" OR '.$prefix.'descendants.user_type = "user" THEN '.$prefix.'descendants.id ELSE NULL END) as downline_count'))
                            ->havingRaw("{$prefix}users.rejoin_count < {$prefix}userpackages.reentry_limit")
                            ->having('downline_count', '>=', $config->downline_count)
                            ->groupBy('users.id')
                            ->whereIn('users.user_type', ['admin', 'user'])
                            ->with('nextReentry')
                            ->get();

            foreach ($users as $key => $user) {
                if($this->checkReentry($user, $config)) {
                    $rejoinCount        = $user->rejoin_count + 1;
                    // $totalReferrals     = $user->referrals_count;
                    $prefix             =  session()->get('prefix');
                    $commission         = new commissionService;
                    $reentryUsername    = (strlen($rejoinCount) < 2 )
                                            ? $user->username."-" . str_pad($rejoinCount, 2, 0, STR_PAD_LEFT)
                                            : $user->username."-" . $rejoinCount;

                    $reEntryId = $this->createRentryUser($reentryUsername, $user, $config, $newUser);
                    $commission->calculateReentryCommission($newUser, $user, $prefix, $reEntryId);
                }
            }
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function createRentryUser($username, $parentUser, $config, $newUser)
    {
        try {
            DB::beginTransaction();
            $coreController             = new CoreInfController;
            $moduleStatus               = $coreController->moduleStatus();
            $productData                = $parentUser->package ?? [];
            $regData                    = $parentUser->toArray();
            $placementData              = $this->monolinePlacement();
            $fatherData                 = User::find($placementData->fatherId)->load('ancestors');
            $product_validity           = $parentUser->product_validity ?? null;
            $regData['password']        = '12345678';
            $regData['username']        = $username;
            $regData['user_type']       = 'reentry';
            $regData['is_reentry_user'] = 1;
            $regData['active']          = 0;
            $regData['reg_amount']      = 0;
            $regData['father_id']       = $placementData->father->id;
            $regData['sponsor_id']      = $parentUser->id;
            $regData['user_level']      = $placementData->user_level + 1;
            $user                       = $this->addToUsers(
                                            $regData, $placementData->father, $placementData,
                                            $productData, $parentUser, $moduleStatus->mlm_plan,
                                            $product_validity, $moduleStatus->product_status,
                                            $moduleStatus->subscription_status
                                        );
            if (!$user) {
                return false;
            }
            $userDetails = $parentUser->userDetail;
            $data = new UserDetail();
            $data->fill($userDetails->replicate()->toArray());
            $data->user_id = $user->id;
            $data->save();

            $this->addToRegistrationDetails(compact('regData', 'productData', 'user'));
            $reentryRelation = new ReentryRelation;
            $reentryRelation->user_id = $user->id;
            $reentryRelation->parent_id = $newUser;
            $reentryRelation->save();
            // $this->insertTreepath($fatherData, $user);

            // $this->createTransPassword($user);

            // $this->sponsorTreePath($user, $user);

            // $this->addToUserBalance($user);

            $parentUser->rejoin_count = $parentUser->rejoin_count + 1;
            $parentUser->save();
            $parentUser->nextReentry->update(['next_reentry' => $parentUser->nextReentry->next_reentry + $config->downline_count]);

            DB::commit();
            // TODO insert Roi
            // TODO insert Group PV
            return $user->id;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function checkReentry($user, $config)
    {
        $totalDownline  = $user->downline_count -1;
        $nextReentry    = $user->nextReentry;
        return ($user->rejoin_count <= $user->reentry_limit) && (($totalDownline % $config->downline_count == 0) && $totalDownline == $nextReentry->next_reentry);
    }

    public function addToUserReentry($user)
    {
        $user->nextReentry()->create([
            'user_id'   => $user->id,
            'next_reentry' => MonolineConfig::first()->downline_count
        ]);
    }
    public function confirmEcomRegister($moduleStatus, $user, $paymentType , $regData,  $sponsorData)
    {
        $ewalletService     = new EwalletService();
        $ewalletUser        = ['id' => $regData['sponsor_id'],];
        $transactionNumber  = generateTransactionNumber();
        $amount             = $regData['reg_amount'];
        $TransactionId      = $this->insertTransaction($transactionNumber);
        $insertUsedWallet   = $ewalletService->insertUsedEwallet($moduleStatus, $ewalletUser, $user, $amount, $TransactionId, 'registration');

        if ($insertUsedWallet) {
            $ewalletService->deductUserBalance($sponsorData, $amount);
            return true;
        }else{
            return false;
        }
    }
    public function updatePlacementTable($sponsor, $user, $position, $fromTree = false, $fatherData)
    {
        $branchParent   = ($fromTree) ? $fatherData->id : $sponsor->id;
        $sponsorParent  = (!$fromTree)
                            ? UserPlacement::where('user_id', $sponsor->id)->with('branchParent')->first()
                            : UserPlacement::where('user_id', $fatherData->id)->with('branchParent')->first();
        $updateMost     = ($position === 'R')
                            ? ['right_most' => $user->id]
                            : ['left_most' => $user->id];
        if($fromTree) {
            if($fatherData->position == $position && $sponsorParent->branchParent) {
                $branchParent = $sponsorParent->branchParent->id;
            }
        } else {
            if($sponsor->position == $position && $sponsorParent->branchParent) {
                $branchParent = $sponsorParent->branchParent->id;
            }
        }
        UserPlacement::create([
            'user_id'       => $user->id,
            'branch_parent' => $branchParent,
        ]);
        UserPlacement::where('user_id', $branchParent)->update($updateMost);
    }
}
