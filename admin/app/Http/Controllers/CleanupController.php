<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CleanupController extends Controller
{
    protected $secret;
    protected $URL;

    public function __construct()
    {
        $this->URL = config('services.commission.url');
        $this->secret = config('services.commission.secret');
    }
    public function cleanUp()
    {
        $prefix = session()->get('prefix');
        $response = Http::timeout(60 * 60)->withHeaders([
            'prefix' => $prefix,
            'SECRET_KEY' => encryptData($this->secret),
        ])->asForm()->post("{$this->URL}cleanup");
        return redirect()->back()->with('success', 'Cleanup Completed');
    }
    public function insertDummy($count = 10)
    {
        $prefix = session()->get('prefix');
        $response = Http::timeout(60 * 60)->withHeaders([
            'prefix' => $prefix,
            'SECRET_KEY' => encryptData($this->secret),
        ])->asForm()->post("{$this->URL}insert-Dummy", [
            'count'     => $count
        ]);
        return redirect()->back()->with('success', 'Dummy inserted');

    }
}
