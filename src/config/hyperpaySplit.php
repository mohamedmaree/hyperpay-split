<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hyperpay Split Mode
    |--------------------------------------------------------------------------
    |
    | Mode only values: "test" or "live"
    |
    */

    "mode" => "test" ,

    /*
    |--------------------------------------------------------------------------
    | Hyperpay Split currency
    |--------------------------------------------------------------------------
    | EGP , SAR , USD, .. etc
    */

    "currency" => "SAR",

    /*
    |--------------------------------------------------------------------------
    | Your Credentials
    |--------------------------------------------------------------------------
    |
    | Your Credentials to enable integration with hyperpay split
    |
    */

    "email"    => "",
    "password" => "",

    /*
     |--------------------------------------------------------------------------
     | hyper split config keys
     |--------------------------------------------------------------------------
     | 
     |
     */

    "config_id"         => "",
    "configuration_key" => "",

    /*
    |--------------------------------------------------------------------------
    | Payment Request urls
    |--------------------------------------------------------------------------
    */

    "test_login_url"  => "https://splits.sandbox.hyperpay.com/api/v1/login",
    "live_login_url"  => "https://splits.hyperpay.com/api/v1/login",
    
    "test_order_url"  => "https://splits.sandbox.hyperpay.com/api/v1/orders",
    "live_order_url"  => "https://splits.hyperpay.com/api/v1/orders",

    "test_account_id" => "SA4280000621608010034790",

];