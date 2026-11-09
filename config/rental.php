<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Profit Sharing
    |--------------------------------------------------------------------------
    |
    | Persentase bagian pemilik dari total pendapatan penyewaan. Sisa pendapatan
    | menjadi bagian platform/admin. Nilai dapat diubah melalui variabel env.
    |
    */

    'owner_share_percent' => (float) env('OWNER_SHARE_PERCENT', 70),

    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    */

    'payment_methods' => [
        'transfer' => 'Transfer',
        'ewallet' => 'E-Wallet',
        'tunai' => 'Tunai',
    ],

    'bank' => [
        'name' => env('PLATFORM_BANK_NAME', 'Bank Mandiri'),
        'account' => env('PLATFORM_BANK_ACCOUNT', '1370012345678'),
        'holder' => env('PLATFORM_BANK_HOLDER', 'PT RideGo Indonesia'),
    ],

];
