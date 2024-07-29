<?php

namespace App\Services;

use App\Http\Controllers\CoreInfController;
use App\Models\CommissionStatusHistory;
use Illuminate\Support\Facades\Http;
use Junges\Kafka\Facades\Kafka;

class commissionService
{
    protected $URL;

    protected $secret;

    public function __construct()
    {
        $this->URL = config('services.commission.url');
        $this->secret = config('services.commission.secret');
    }

    public function referralCommission($newUser, $sponsor, $product, $prefix)
    {
        $coreController = new CoreInfController;
        $compensation = $coreController->compensation();
        try {

            if ($compensation->referral_commission) {
                $history = CommissionStatusHistory::create([
                    'commission' => 'referral',
                    'user_id'    =>  $sponsor->id, // sponsor_id based on referral commission
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);
                $postData = [
                    'user_id' => $newUser->id,
                    'referral_id' => $sponsor->id,
                    'product_pv' => $product->pair_value ?? 0,
                    'status_id' => $history->id,
                ];

                $history->update([
                    'data' => json_encode($postData),
                ]);


                $referralCommission = Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])
                    ->asForm()->post("{$this->URL}calculatecommission", [
                        'enc_data' => encryptData($postData),
                    ]);
            }
        } catch (\Throwable $th) {
            throw $th;

            return false;
        }

        return true;
    }

    public function levelCommission($newUser, $sponsor, $orderId, $productId, $totalProductPv, $totalProductPrice, $prefix, $action = 'register')
    {
        $coreController = new CoreInfController;
        $compensation = $coreController->compensation();
        $moduleStatus = $coreController->moduleStatus();
        try {
            if ($compensation->sponsor_commission) {
                $history = CommissionStatusHistory::create([
                    'commission' => 'level',
                    'user_id'    =>  $newUser->id, // new user id
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);
                $postData = [
                    'from_user' => $newUser->id,
                    'action' => $action,
                    'sponsor_id' => $sponsor->id,
                    'product_id' => $productId,
                    'product_pair_value' => $totalProductPv,
                    'product_amount' => $totalProductPrice,
                    'oc_order_id' => $newUser->order_id ?? 0,
                    'order_id' => $orderId,
                    'status_id' => $history->id,
                ];

                $history->update([
                    'data' => json_encode($postData),
                ]);

                $levelCommission = Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])
                    ->asForm()->post("{$this->URL}calculateLevelCommission", ['enc_data' => encryptData($postData)]);
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function planCommission($newUser, $sponsor, $orderId, $productId, $productPv, $productPrice, $prefix, $action = 'register')
    {
        $coreController = new CoreInfController;
        $compensation = $coreController->compensation();
        $plan = $coreController->moduleStatus()['mlm_plan'];

        try {
            if ($plan == 'Binary') {
                if ($compensation->plan_commission) {
                    $history = CommissionStatusHistory::create([
                        'commission' => 'binary',
                        'user_id'    =>  $newUser->id, // new user id
                        'status'     =>  0, // initialised
                        'date'       => now(),
                    ]);
                    $postData = [
                        'action'        => $action,
                        'user_id'       => $newUser->id,
                        'sponsor_id'    => $sponsor->id ?? 0,
                        'product_id'    => $productId ?? 0,
                        'product_pv'    => $productPv ?? 0,
                        'price'         => $productPrice ?? 0,
                        'oc_order_id'   => $newUser->order_id ?? 0,
                        'upline_id'     => $newUser->father_id,
                        'order_id'      => $orderId,
                        'position'      => $newUser->position,
                        'status_id'     => $history->id,
                    ];

                    $history->update([
                        'data' => json_encode($postData),
                    ]);

                    $binaryCommission = Http::timeout(60 * 60)->withHeaders([
                        'prefix' => $prefix,
                        'SECRET_KEY' => encryptData($this->secret),
                    ])
                        ->asForm()->post("{$this->URL}binarycommission", ['enc_data' => encryptData($postData)]);
                }
            } elseif ($plan == 'Stair_Step') {

                $history = CommissionStatusHistory::create([
                    'commission' => 'stair_step',
                    'user_id'    =>  $newUser->id, // new user id
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);

                $postData = [
                    'action' => $action,
                    'user_id' => $newUser->id,
                    'product_id' => $productId,
                    'product_pv' => $productPv,
                    'price' => $productPrice,
                    'sponsor_id' => $sponsor->id ?? 0,
                    'oc_order_id' => $newUser->order_id ?? 0,
                    'order_id' => $orderId,
                    'status_id' => $history->id,
                ];

                $history->update([
                    'data' => json_encode($postData),
                ]);

                $stairStepCommission = Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])
                    ->asForm()->post("{$this->URL}stairstepcommission", ['enc_data' => encryptData($postData)]);
            } elseif ($plan == 'Donation') {

                $history = CommissionStatusHistory::create([
                    'commission' => 'donation',
                    'user_id'    =>  $newUser->id, // new user id
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);

                $postData = [
                    'action' => 'register',
                    'user_id' => $newUser->id,
                    'price' => $productPrice,
                    'sponsor_id' => $sponsor->id ?? 0,
                    'status_id' => $history->id,
                ];

                $history->update([
                    'data' => json_encode($postData),
                ]);
                Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])
                    ->asForm()->post("{$this->URL}calculateDonation", ['enc_data' => encryptData($postData)]);
            }

            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public function rankCommission($newUser, $prefix)
    {
        $coreController = new CoreInfController;
        $compensation = $coreController->compensation();
        try {
            if ($compensation->rank_commission) {

                $history = CommissionStatusHistory::create([
                    'commission' => 'rank',
                    'user_id'    =>  $newUser->id, // new user id
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);
                $postData = [
                    'user_id'   => $newUser->id,
                    'status_id' => $history->id,
                ];

                $history->update([
                    'data' => json_encode($postData),
                ]);
                $rankBonus = Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])->asForm()->post("{$this->URL}rank", ['enc_data' => encryptData($postData)]);

                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function poolBonus($prefix)
    {
        $coreController = new CoreInfController;
        $compensation = $coreController->compensation();
        try {
            if ($compensation->pool_bonus) {
                $history = CommissionStatusHistory::create([
                    'commission' => 'pool_bonus',
                    'user_id'    =>  null,
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);

                $postData = [
                    'status_id' => $history->id,
                ];
                $history->update([
                    'data' => json_encode($postData),
                ]);
                $poolBonus = Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])->asForm()->post("{$this->URL}calculatePoolBonus", ['enc_data' => encryptData($postData)]);

                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function roi($prefix)
    {
        $coreController = new CoreInfController;
        $compensation = $coreController->compensation();

        try {
            if ($compensation->roi_commission) {
                $history = CommissionStatusHistory::create([
                    'commission' => 'roi',
                    'user_id'    =>  null,
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);
                $postData = [
                    'status_id' => $history->id,
                ];
                $history->update([
                    'data' => json_encode($postData),
                ]);
                $roiCommission = Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])->asForm()->post("{$this->URL}roi", ['enc_data' => encryptData($postData)]);

                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function fastStartBonus($sponsor, $prefix)
    {
        $coreController = new CoreInfController;
        $compensation = $coreController->compensation();

        try {
            if ($compensation->fast_start_bonus) {

                $history = CommissionStatusHistory::create([
                    'commission' => 'fast_start_bonus',
                    'user_id'    =>  $sponsor->id, // new user id
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);
                $postData = [
                    'sponsor_id' => $sponsor->id ?? 0,
                    'status_id' => $history->id,
                ];
                $history->update([
                    'data' => json_encode($postData),
                ]);
                $faststartbonus = Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])->asForm()->post("{$this->URL}faststartbonus", ['enc_data' => encryptData($postData)]);

                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function performanceBonus($newUser, $prefix)
    {
        $coreController = new CoreInfController;
        $compensation = $coreController->compensation();

        try {
            if ($compensation->performance_bonus) {

                
                $history = CommissionStatusHistory::create([
                    'commission' => 'performance_bonus',
                    'user_id'    =>  $newUser->id, // new user id
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);

                $postData = [
                    'user_id' => $newUser->id,
                    'status_id' => $history->id,
                ];
                $history->update([
                    'data' => json_encode($postData),
                ]);


                $performance = Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])->asForm()->post("{$this->URL}performance_bonus", ['enc_data' => encryptData($postData)]);

                return true;
            }else
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function updateUplineRank($newUser, $prefix)
    {
        $coreController = new CoreInfController;
        $compensation = $coreController->compensation();
        try {
            if ($compensation->rank_commission) {
                $history = CommissionStatusHistory::create([
                    'commission' => 'update_upline_rank',
                    'user_id'    =>  $newUser, // new user id
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);
                $postData = [
                    'user_id'   =>  $newUser,
                    'status_id' =>  $history->id,
                ];
                $history->update([
                    'data' => json_encode($postData),
                ]);
                $performance = Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])->asForm()->post("{$this->URL}rank", ['enc_data' => encryptData($postData)]);
                return true;
            }
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public function SalesCommission($newUser, $sponsor, $orderId, $productId, $totalProductPv, $totalProductPrice, $prefix, $action)
    {
        try {
            $coreInf = new CoreInfController;
            $compensation = $coreInf->compensation();
            $salesLevelCommission = $compensation->sales_Commission;
            if ($salesLevelCommission) {
                $history = CommissionStatusHistory::create([
                    'commission' => 'sales_commission',
                    'user_id'    =>  $newUser->id, // new user id
                    'status'     =>  0, // initialised
                    'date'       => now(),
                ]);

                $postData = [
                    'from_user'     => $newUser->id,
                    'status_id'     => $history->id,
                    'product_id'    => $productId,
                    'product_pair_value' => $totalProductPv ?? 0,
                    'action'         =>  'repurchase',
                    'product_amount' => $totalProductPrice ?? 0,
                    'oc_order_id'    => $newUser->order_id ?? 0,
                    'order_id'      => $orderId,
                    'sponsor_id'     => $sponsor->id ?? 0,
                ];
                $history->update([
                    'data' => json_encode($postData),
                ]);

                $salesLevelCommission = Http::timeout(60 * 60)->withHeaders([
                    'prefix' => $prefix,
                    'SECRET_KEY' => encryptData($this->secret),
                ])->asForm()->post("{$this->URL}calculateSalesCommission", ['enc_data' => encryptData($postData)]);
            }
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public function calculateReentryCommission($newUser, $user, $prefix, $reEntryId)
    {
        try {
            $history = CommissionStatusHistory::create([
                'commission' => 'reentry_commission',
                'user_id'    =>  $user->id,
                'status'     =>  0, // initialised
                'date'       => now(),
            ]);

            $postData = [
                'user_id' => $user->id,
                'status_id' => $history->id,
                'new_user' => $newUser,
                're_entry_id' => $reEntryId
            ];

            $history->update([
                'data' => json_encode($postData),
            ]);

            Http::timeout(60 * 60)->withHeaders([
                'prefix' => $prefix,
                'SECRET_KEY' => encryptData($this->secret),
            ])->asForm()->post("{$this->URL}calculateRejoinBonus", ['enc_data' => encryptData($postData)]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
