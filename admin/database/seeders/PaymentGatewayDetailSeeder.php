<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayConfig;
use App\Models\PaymentGatewayDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewayDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_gateway_details')->delete();

        $data = [];
        $gateways = PaymentGatewayConfig::where('gate_way',1)->get();

        foreach( $gateways as $key => $detail ){
            $data[$key]['payment_gateway_id'] = $detail->id;
            if($detail->slug == 'stripe'){
                $data[$key]['public_key'] = 'pk_test_51LHkzeKD7ZhA819wxNlZjA7rJtL4QnhqxPdo0BX5QF7L2QxN9Z8X0eQi3YTuWMs5izAI02xEk88TPaMx1bHUMVqc00CDofyNSN';
                $data[$key]['secret_key'] = 'sk_test_51LHkzeKD7ZhA819wBfEjkV40TdHtSX50P81OngNSzFxzwDFgw1rxBvxBvBcPhmpClZiocSo9GbcU2JXSSgdI24mA00wnOzee6D';
            }elseif($detail->slug == 'paypal'){
                $data[$key]['public_key'] = 'AYD1axYrIg27ZijTfPoyn5JQDKTJL6Kfl2nf89gvelJQ3OuprHQITYha7VSWm2XDjd1IZ2o3Ybs1WOT3';
                $data[$key]['secret_key'] = 'EHQxuqr2ZTuu_tP0EB5TwiigVT2KFjXcaaRUWeUVE2vUzS5I_XJQpcBrFvfDLlYF8nygaZQJmbROy5BW';
            }else{
                $data[$key]['public_key'] = '';
                $data[$key]['secret_key'] = '';
            }
            $data[$key]['created_at'] = now();
            $data[$key]['updated_at'] = now();
        }

        // $data = [

        //     [
        //         'payment_gateway_id' => 5,
        //         'public_key' => 'pk_test_51LHkzeKD7ZhA819wxNlZjA7rJtL4QnhqxPdo0BX5QF7L2QxN9Z8X0eQi3YTuWMs5izAI02xEk88TPaMx1bHUMVqc00CDofyNSN',
        //         'secret_key' => 'sk_test_51LHkzeKD7ZhA819wBfEjkV40TdHtSX50P81OngNSzFxzwDFgw1rxBvxBvBcPhmpClZiocSo9GbcU2JXSSgdI24mA00wnOzee6D',
        //     ],

        // ];
        PaymentGatewayDetail::insert($data);
    }
}
