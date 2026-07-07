<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Symbol displayed in all monetary outputs (invoices, reports, dashboard).
    | Changing this here updates every view that uses the @currency directive
    | or config('pharmacy.currency_symbol').
    |
    */
    'currency_symbol' => env('PHARMACY_CURRENCY_SYMBOL', '₹'),
    'currency_code'   => env('PHARMACY_CURRENCY_CODE', 'INR'),

    /*
    |--------------------------------------------------------------------------
    | Date / Time Display Format
    |--------------------------------------------------------------------------
    |
    | The format used when displaying dates to the user in views and PDFs.
    | This is a PHP date() format string.
    |
    */
    'date_format'     => env('PHARMACY_DATE_FORMAT', 'd M Y'),
    'datetime_format' => env('PHARMACY_DATETIME_FORMAT', 'd M Y, H:i'),

    /*
    |--------------------------------------------------------------------------
    | GST Slab Rates
    |--------------------------------------------------------------------------
    |
    | Common Indian GST slabs for medicines. Used to populate dropdowns in
    | purchase and batch forms so staff don't have to type free-form rates.
    |
    */
    'gst_rates' => [0, 5, 12, 18, 28],

    /*
    |--------------------------------------------------------------------------
    | Stock Alert Thresholds
    |--------------------------------------------------------------------------
    |
    | Number of days ahead at which a batch is considered "expiring soon"
    | for the scheduled notification command and the dashboard widget.
    |
    */
    'expiry_alert_days' => (int) env('PHARMACY_EXPIRY_ALERT_DAYS', 30),
    'low_stock_default' => (int) env('PHARMACY_LOW_STOCK_DEFAULT', 10),

    /*
    |--------------------------------------------------------------------------
    | PDF / Report Settings
    |--------------------------------------------------------------------------
    */
    'pdf_paper_size'        => 'A4',
    'pdf_paper_orientation' => 'portrait',

    /*
    |--------------------------------------------------------------------------
    | Backup Retention
    |--------------------------------------------------------------------------
    |
    | How many days to keep database dump files before the backup command
    | prunes them automatically.
    |
    */
    'backup_retention_days' => (int) env('PHARMACY_BACKUP_RETENTION_DAYS', 30),

];
