<?php

namespace App\Services;

use App\Http\Controllers\CoreInfController;
use App\Models\ModuleStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Package;
use App\Models\PurchaseRank;
use App\Models\RankConfiguration;
use App\Models\RankDetail;
use App\Models\Treepath;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService extends CoreInfController
{
    public function updateUserPv($moduleStatus, $orderId)
    {
        // dd($moduleStatus);

        $order = Order::find($orderId);
        $user = User::find($order->user_id);

        //dd($order->user_id);

        $updatePv = true;
        $uplineId = $user->father_id;
        $position = $user->position;

        $productPairValue = 0;
        $productAmount = 0;
        $quantity = 0;
        $orderDetails = OrderDetail::where('order_id', $order->id)->get();

        foreach ($orderDetails as $orderDetail) {
            $product = Package::find($orderDetail->package_id);
            if ($moduleStatus->ecom_status == '0' || $moduleStatus->ecom_status_demo == '0') {
                $pair_value = $product->pair_value;
                $product_value = $product->product_value;
            } else {
                //TODO
            }

            $product_pv = $pair_value * $orderDetail->quantity;
            $productPairValue += $product_pv;

            $productAmount += $orderDetail->amount * $orderDetail->quantity;

            $quantity += $orderDetail->quantity;
        }

        $oc_order_id = 0;
        $action = 'repurchase';

        $sponsorId = $user->sponsor_id;
        if ($moduleStatus->mlm_plan == 'Matrix') {
            $uplineId = $sponsorId;
        }
        $data = [];
        $data['sponsorId'] = $sponsorId;
        $updatePv = 0;
        //CALCULATION SECTION STARTS//

        //TODO Commission
        //  $updatePv = $this->runCalculation($moduleStatus,$action, $user->id, $orderDetail->id, $productPairValue, $productAmount, $oc_order_id, $uplineId, 0, $position, $data);

        //CALCULATION SECTION ENDS//

        if (! $updatePv) {
            return false;
        }

        return $updatePv;
    }

    public function updateUplineRank($userId)
    {
        $rankConfiguration = RankConfiguration::query();
        $compensation = $this->compensation();
        $user = User::find($userId);

        //TODOCondition checking
    //    if($rankConfiguration->where('slug','rank-expiry')->first()->status)
    //    {

        if (! $rankConfiguration->where('slug', 'joiner-package')->first()->status) {
            $old_rank = $user->user_rank_id;

            $new_rank = $this->checkNewRank(0, 0, 0, $user->id, $old_rank);
            if ($new_rank != $old_rank) {
                $user->update([
                    'user_rank_id' => $new_rank,
                ]);
                if ($compensation->rank_commission == '1') {
                    $this->rankBonus($new_rank, $user->id, $user->id);
                }
            }

            return true;
        } else {
            $sponsor_upline = User::orderBy('user_level', 'desc')->with('ancestors', 'rankDetail')->whereRelation('ancestors', 'descendant', $user->id)->get();

            foreach ($sponsor_upline as $uplines) {
                $user_id = $uplines['id'];
                $personal_pv = $uplines['personal_pv'];
                $group_pv = $uplines['group_pv'];
                $old_rank = $uplines['user_rank_id'];
                $user_status = $uplines['active'];

                $referal_count = User::where('sponsor_id', $user->id)->get()->count();
                if ($user_status == 'yes') {
                    $new_rank = $this->checkNewRank($referal_count, $personal_pv, $group_pv, $user_id, $old_rank);

                    if ($new_rank == $old_rank) {
                        $user->update([
                            'user_rank_id' => $new_rank,
                        ]);

                        if ($compensation->rank_commission == '1') {
                            $this->rankBonus($new_rank, $user_id, $user->id);
                        }
                    }
                }
            }
        }
        // }
    }

    public function rankBonus($new_rank, $user_id, $purchseUserId)
    {
        $rank_bonus = $this->getActiveRankDetails($new_rank);

        $configuration = $this->configuration();

        $date_of_sub = date('Y-m-d H:i:s');
        $amount_type = 'rank_bonus';

        $tds_db = $configuration->tds;
        $service_charge = $configuration->service_charge;
        //TODO
        //$rank_amount    = $rank_bonus[0]['rank_bonus'];
        // $tds_amount     = ($rank_amount * $tds_db) / 100;
        // $service_charge = ($rank_amount * $service_charge) / 100;
        // $amount_payable = $rank_amount - ($tds_amount + $service_charge);

        // $this->calculation_model->insertRankBonus($user_id, $rank_amount, $amount_payable, $tds_amount, $service_charge, $date_of_sub, $amount_type, $purchse_user_id, 1);
    }

    public function checkNewRank($referal_count, $personal_pv, $group_pv, $user_id, $curent_rank)
    {
        $user = User::find($user_id);
        $downline_rank_query = User::with('descendants', 'rankDetail.rank');

        $moduleStatus = ModuleStatus::first();
        if (empty($group_pv)) {
            $group_pv = 0;
        }
        $criteria = [
            'referal_count' => false,
            'joinee_package' => false,
            'personal_pv' => false,
            'group_pv' => false,
            'downline_member_count' => false,
            'downline_purchase_count' => false,
            'downline_rank' => false,
        ];

        if (RankConfiguration::where('slug', 'referral-count')->first()->status) {
            $criteria['referal_count'] = true;
        }

        if (RankConfiguration::where('slug', 'personal-pv')->first()->status) {
            $criteria['personal_pv'] = true;
        }
        if (RankConfiguration::where('slug', 'group-pv')->first()->status) {
            $criteria['group_pv'] = true;
        }

        if (! RankConfiguration::where('slug', 'downline-member-count')->first()->status && in_array($moduleStatus->mlm_plan, ['Binary', 'Matrix'])) {
            $total_downline_count = Treepath::where('ancestor', $user_id)->get()->count();

            $criteria['downline_member_count'] = true;
        }

        if (RankConfiguration::where('slug', 'downline-package-count')->first()->status) {
            $criteria['downline_purchase_count'] = true;
        }

        if (RankConfiguration::where('slug', 'downline-rank-count')->first()->status) {
            $criteria['downline_rank'] = true;
        }

        if (RankConfiguration::where('slug', 'joiner-package')->first()->status) {
            $criteria = [
                'referal_count' => false,
                'personal_pv' => false,
                'group_pv' => false,
                'downline_member_count' => false,
                'downline_purchase_count' => false,
                'downline_rank' => false, ];
            $criteria['joinee_package'] = true;
            //TODOabout joinee rank table

            $joinee_rank_id = PurchaseRank::where('package_id', $user->product_id)->first()->rank_id;
        }
        // dd($criteria);

        if ($criteria['downline_purchase_count']) {
            $downline_package_details = $this->getDownlinePackageDetails($user->id, 'father', $moduleStatus);

            if ($downline_package_details) {
                foreach ($downline_package_details as $details) {
                    $packageColumns[] = Package::where('product_id', $details['product_id'])->first()->id;
                }

                //  $downline_package_query = PurchaseRank::with('rankDetails')
    //                             ->whereRelation('rankDetails','status',"active")
    //                             ->whereRelation('rankDetails', 'delete_status', "yes");
            }
        }

        if ($criteria['downline_rank']) {
            $downline_rank_details = $this->getLeftRightDownlineRankWiseCount($user->id);

            if ($downline_rank_details) {
                foreach ($downline_rank_details as $downlineDet) {
                    $rank_columns[] = $downlineDet['rankName'];
                }

                $downline_rank_query = User::with('descendants', 'rankDetail.rank')->whereRelation('descendants', 'ancestor', $user->id)->whereRelation('rankDetail', 'status', 'active')->whereRelation('rankDetail', 'delete_status', 'yes');
            }
        }

        if ($criteria['referal_count']) {
            $downline_rank_query = $downline_rank_query->whereRelation('rankDetail', 'referral_count', '<=', $referal_count);
        }

        if (! $criteria['joinee_package']) {
            $downline_rank_query = $downline_rank_query->whereRelation('rankDetail', 'rank_id', $joinee_rank_id);
        }

        if ($criteria['personal_pv']) {
            $downline_rank_query = $downline_rank_query->whereRelation('rankDetail', 'personal_pv', '<=', $personal_pv);
        }
        if ($criteria['group_pv']) {
            $downline_rank_query = $downline_rank_query->whereRelation('rankDetail', 'group_pv', '<=', $group_pv);
        }
        if ($curent_rank != null) {
            $downline_rank_query = $downline_rank_query->whereRelation('rankDetail', 'rank_id', '>', $curent_rank);
        }

        if ($criteria['downline_member_count']) {
            $downline_rank_query = $downline_rank_query->whereRelation('rankDetail', 'downline_count', '<=', $total_downline_count);
        }

        if ($criteria['downline_purchase_count']) {
            $package_columns = implode(',', $packageColumns);
            //TODO

            // $this->db->select($package_columns);
            // $this->db->join("({$downline_package_query}) AS p", 'p.rank_id=r.rank_id');
            // $this->db->group_start();
            // foreach($downline_package_details as $d) {
            //     $this->db->where("p.`{$d['package_id']}` <=", $d['count']);
            // }
            // $this->db->group_end();
        }
        // if ($criteria['downline_rank'])
        //  {
        //TODO
        //     $rank_columns = implode(',', array_unique($rank_columns));

        //     // $this->db->join("({$downline_rank_query}) AS dwr", 'dwr.rank_id=r.rank_id');
        //     // foreach($downline_rank_details as $d) {
        //     //     $d['rank_name']=str_replace(' ', '',$d['rank_name']);
        //     //    $this->db->where("dwr.`{$d['rank_name']}` <=", $d['count']);
        //     // }
        // }

        // $this->db->where('r.rank_status', 'active');
        // $this->db->where('r.delete_status', 'yes');
        // $this->db->order_by('r.rank_id', 'ASC');

        // $query = $this->db->get();
        // $rank_id = $curent_rank;

        // foreach ($query->result() as $row) {
        //     $rank_id = $row->rank_id;
        //     $this->insertIntoRankHistory($curent_rank, $rank_id, $user_id);
        //     $curent_rank = $rank_id;
        // }

        $rank_id = $curent_rank;

        return $rank_id;
    }

    public function getDownlinePackageDetails($userId, $type, $moduleStatus)
    {
        $resArray = [];
        $result = User::whereHas('descendants', function ($qry) use ($userId) {
            $qry->where('ancestor', $userId);
        })->withCount('descendants');

        if ($moduleStatus->ecom_status && $moduleStatus->ecom_status_demo) {
            //TODO
        } else {
            $result = $result->whereHas('package', function ($qry) {
                $qry->where('type', 'registration');
            })->with('package');
        }
        $result = $result->get();

        foreach ($result as $key => $res) {
            $resArray[$key] = [
                'count' => $res->descendants_count,
                'package_id' => $res->product_id,
                'product_id' => $res->package->product_id,
            ];
        }

        return $resArray;
    }

    public function getLeftRightDownlineRankWiseCount($userId)
    {
        $resultArray = [];

        $result = User::with('descendants', 'rankDetail.rank')->whereRelation('descendants', 'ancestor', $userId)->whereRelation('rankDetail', 'status', 'active')->get();

        foreach ($result as $key => $data) {
            $resultArray[$key] = [
                'userId' => $data->id,
                'userRank' => $data->user_rank_id,
                'rankName' => $data->rankDetail->rank->name,

            ];
        }

        return $resultArray;
    }

    public function getActiveRankDetails($rank = '')
    {
        $rankDetails = RankDetail::query();

        if ($rank != '') {
            $rankDetails = $rankDetails->where('rank_id', $rank);
        }

        $rankDetails = $rankDetails->where('status', 'active')
                                            ->where('delete_status', 'yes')->get();

        return $rankDetails;
    }

    public function updatePendingOrder($orderId)
    {
        $order = Order::find($orderId);

        $order->order_status = '1';
        $order->save();

        $orderDetails = OrderDetail::where('order_id', $orderId)->get();

        foreach ($orderDetails as $detail) {
            $detail->order_status = '1';
            $detail->save();
        }

        // $order->update([
        //     'order_status'   =>  '1',
        // ]);
        // $orderDetail->update([
        //     'order_status'   =>  '1',
        // ]);
        return true;
    }
}
