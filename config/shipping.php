<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Origin City
    |--------------------------------------------------------------------------
    |
    | Kota asal pengiriman toko Anda.
    | Untuk starter API, gunakan ID kota yang tersedia.
    | Contoh: Jakarta Selatan = 152, Bandung = 23, Surabaya = 444
    |
    */
    'origin_city' => env('SHIPPING_ORIGIN_CITY', 495),

    /*
    |--------------------------------------------------------------------------
    | Default Weight (gram)
    |--------------------------------------------------------------------------
    */
    'default_weight' => env('SHIPPING_DEFAULT_WEIGHT', 250),

    /*
    |--------------------------------------------------------------------------
    | Free Shipping Threshold
    |--------------------------------------------------------------------------
    */
    'free_shipping_threshold' => env('SHIPPING_FREE_THRESHOLD', 200000),

    'couriers' => [
        'jne',
        'jnt',
        'sicepat',
        'ninja',
        'tiki',
        'spx',
    ],
];
