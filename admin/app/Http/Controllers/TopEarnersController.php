<?php

namespace App\Http\Controllers;

use App\Models\User;

class TopEarnersController extends CoreInfController
{
    public function index()
    {
        $legAmount = User::where('user_type', 'user')->with('legamtDetails', 'userDetails')->withSum('LegAmount as total', 'total_amount')->get();

        return view(
            'admin.report.top-earners',
            compact('legAmount')
        );
    }
}
