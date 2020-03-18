<?php

return [
    'role_structure' => [
        'admin' => [
            'admin'             => 'm',
            'rider'             => 'c,r,u,d',
            'driver'            => 'c,r,u,d',
            'company'           => 'c,r,u,d',
            'vehicle_type'      => 'm',
            'send_message'      => 'm',
            'api_credentials'   => 'm',
            'payment_gateway'   => 'm',
            'site_settings'     => 'm',
            'map'               => 'm',
            'statements'        => 'm',
            'trips'             => 'm',
            'wallet'            => 'm',
            'owe_amount'        => 'm',
            'promo_code'        => 'm',
            'driver_payments'   => 'm',
            'cancel_trips'      => 'm',
            'rating'            => 'm',
            'fees'              => 'm',
            'join_us'           => 'm',
            'requests'          => 'm',
            'currency'          => 'm',
            'static_pages'      => 'm',
            'metas'             => 'm',
            'locations'         => 'm',
            'peak_based_fare'   => 'm',
            'send_email'        => 'm',
            'email_settings'    => 'm',
            'language'          => 'm',
            'help'              => 'm',
            'country'           => 'm',
            'heat_map'          => 'm',
            'manual_booking'    => 'm',
            'company_payment'   => 'm',
            'payments'          => 'm',
            'vehicle'           => 'm',
            'referral_settings' => 'm',
            'rider_referrals'   => 'm',
            'driver_referrals'  => 'm',
            'manage_reason'     => 'c,r,u,d',
            'additional_reason' => 'c,r,u,d',
        ],
        'dispatcher' => [
            'manual_booking'    => 'm',
        ],
    ],
    'user_roles' => [
        'admin' => [
            ['username' => 'admin', 'email' => 'admin@trioangle.com', 'password' => '123456', 'status' => 'Active', 'created_at' => date('Y-m-d H:i:s')],
        ],
        'dispatcher' => [
            ['username' => 'dispatcher', 'email' => 'dispatcher@trioangle.com', 'password' => '123456', 'status' => 'Active', 'created_at' => date('Y-m-d H:i:s')],
        ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'view',
        'u' => 'update',
        'd' => 'delete',
        'm' => 'manage',
    ],
];