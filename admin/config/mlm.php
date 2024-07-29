<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DEMO status
    |--------------------------------------------------------------------------
    |
    | Is DEMO or not
    |
    */
    'demo_status' => env('DEMO_STATUS', 'no'),

    /*
    |--------------------------------------------------------------------------
    | Commision container name
    |--------------------------------------------------------------------------
    |
    | URl for commissions
    |
    */

    'settings_route' => [
        'binaryConfig', 'roi', 'matching-bonus', 'pool-bonus', 'fast-start-bonus', 'performance-bonus',
        'rank', 'plan-settings', 'recieved-donation', 'given-donation', 'missed-donation', 'manage-user-level',
    ],

    'common_routes' => ['compensation', 'apiKey', 'signup', 'payment.view', 'payout', 'payout.update', 'mail'],
    'lcp'   => env('LCP_LINK', ''),

    'user_replica_url'  => env('USER_REPLICA_URI', ''),
    'user_lcp_url'  => env('USER_LCP_URL', ''),
    'user_url'  => env('USER_URL', ''),
    'ecom_database_url' => env('ECOM_DB_URL', ''),
    'ecom_revamp_database_url' => env('ECOM_RVMP_DB_URL', ''),

    'tree_depth' => env('TREE_DEPTH', 4),
    'tree_width' => env('TREE_WIDTH', 3),
    'local_timezone' => env('APP_LOCAL_TIMEZONE', 'UTC'),

];
