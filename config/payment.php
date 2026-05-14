<?php
return [
    'finansbank' =>[
        'merchant_id'    => env('FINANSBANK_MERCHANT_ID', '085300000009746'),
        'merchant_pass'  => env('FINANSBANK_MERCHANT_PASS', '12345678'),
        'store_key'      => env('FINANSBANK_STORE_KEY', '12345678'),
        'user_code'      => env('FINANSBANK_USER_CODE', 'QNB_ISYERI_KULLANICI_3DPAY'),
        'user_pass'      => env('FINANSBANK_USER_PASS', 'a1234'),
        'mbr_id'         => env('FINANSBANK_MBR_ID', '5'),
        'secure_type'    => env('FINANSBANK_SECURE_TYPE', 'Payfor3DHost'),
        'api_url'        => env('FINANSBANK_API_URL', 'https://vpostest.qnb.com.tr/Gateway/3DHost.aspx'),
        'success_url'    => env('FINANSBANK_SUCCESS_URL'),
        'fail_url'       => env('FINANSBANK_FAIL_URL'),
    ],
];