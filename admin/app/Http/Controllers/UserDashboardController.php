<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestUserDashboard;
use App\Models\UserDashboard;

class UserDashboardController extends CoreInfController
{
    public function index()
    {
        $moduleStatus = $this->moduleStatus();
        $dashboarditems = UserDashboard::select('id', 'name', 'status', 'slug', 'parent_id')->where('parent_id', null)->with('children')->get();

        return view(
            'admin.settings.advancedSettings.userDashboard.index',
            compact('dashboarditems', 'moduleStatus')
        );
    }

    public function update(RequestUserDashboard $request)
    {
        // dd($request->all());
        UserDashboard::Active()->update(['status' => 0]);
        UserDashboard::whereIn('id', $request->all()['parent'])->update(['status' => 1]);
        if (isset($request->all()['child'])) {
            UserDashboard::whereIn('id', $request->all()['child'])->update(['status' => 1]);
        }
        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Updated succesfully',
            ]);
        } else {
            return back()->with('success', 'Updated succesfully');
        }
        return response()->json([
                'status' => true,
                'message' => 'gdfgdf succesfully',
            ]);
    }
}
