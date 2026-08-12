<?php

return [
    'name'    => 'Garage A. Lingiah',
    'version' => '1.0.0',
    'debug'   => filter_var(getenv('APP_DEBUG') ?: 'true', FILTER_VALIDATE_BOOLEAN),
    'url'     => getenv('APP_URL') ?: 'http://localhost',
    'timezone'=> getenv('APP_TIMEZONE') ?: 'Indian/Mauritius',

    'session' => [
        'name'     => 'garling_session',
        'lifetime' => 7200, // 2 hours
    ],

    'currency' => [
        'symbol'    => 'Rs',
        'code'      => 'MUR',
        'separator' => ',',
        'decimal'   => '.',
        'decimals'  => 2,
    ],

    'vat_rate' => 15, // default VAT %

    'expense_categories' => [
        'Rent',
        'Utilities',
        'Supplies',
        'Salaries',
        'Equipment',
        'Insurance',
        'Maintenance',
        'Fuel',
        'Other',
    ],
];
