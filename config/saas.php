<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Free trial length (days)
    |--------------------------------------------------------------------------
    | New pharmacies get this many days of full access before a paid plan is
    | required. Applied at signup by RegisteredUserController.
    */
    'trial_days' => (int) env('SAAS_TRIAL_DAYS', 14),

    /*
    |--------------------------------------------------------------------------
    | GST charged on subscription invoices (percent)
    |--------------------------------------------------------------------------
    | SaaS in India attracts 18% GST. Applied when generating billing invoices.
    */
    'gst_percent' => (float) env('SAAS_GST_PERCENT', 18),

    /*
    |--------------------------------------------------------------------------
    | Base currency
    |--------------------------------------------------------------------------
    | ISO code used when creating Razorpay orders. Razorpay expects amounts in
    | the smallest unit (paise for INR).
    */
    'currency' => env('SAAS_CURRENCY', 'INR'),

];
