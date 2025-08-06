<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// Default tariff data
$defaultTariffs = [
    'TRIAL' => ['NAME' => 'Trial', 'PRICE' => '0', 'PERIOD' => 'for 14 days', 'SPEED' => 'up to 100 Mbit/s', 'FEATURES' => '10 GB of cloud storage,Basic support', 'IS_POPULAR' => 'N'],
    'BASE' => ['NAME' => 'Base', 'PRICE' => '30', 'PERIOD' => 'per month', 'SPEED' => 'up to 300 Mbit/s', 'FEATURES' => '50 GB of cloud storage,Standard support,HD video streaming', 'IS_POPULAR' => 'Y'],
    'PREMIUM' => ['NAME' => 'Premium', 'PRICE' => '50', 'PERIOD' => 'per month', 'SPEED' => 'up to 500 Mbit/s', 'FEATURES' => '200 GB of cloud storage,Priority support,4K video streaming,Advanced security', 'IS_POPULAR' => 'N'],
    'UNLIMITED' => ['NAME' => 'Unlimited', 'PRICE' => '100', 'PERIOD' => 'per month', 'SPEED' => 'up to 1 Gbit/s', 'FEATURES' => '1 TB of cloud storage,24/7 premium support,All features included,Personal manager', 'IS_POPULAR' => 'N'],
];

$arComponentParameters = [
    'GROUPS' => [
        'TARIFF_1' => ['NAME' => 'Tariff 1 (Trial)', 'SORT' => 100],
        'TARIFF_2' => ['NAME' => 'Tariff 2 (Base)', 'SORT' => 200],
        'TARIFF_3' => ['NAME' => 'Tariff 3 (Premium)', 'SORT' => 300],
        'TARIFF_4' => ['NAME' => 'Tariff 4 (Unlimited)', 'SORT' => 400],
    ],
    'PARAMETERS' => [],
];

$i = 1;
foreach ($defaultTariffs as $code => $defaults) {
    $group = 'TARIFF_' . $i;
    $arComponentParameters['PARAMETERS'][$code . '_NAME'] = [
        'PARENT' => $group,
        'NAME' => 'Name',
        'TYPE' => 'STRING',
        'DEFAULT' => $defaults['NAME'],
    ];
    $arComponentParameters['PARAMETERS'][$code . '_PRICE'] = [
        'PARENT' => $group,
        'NAME' => 'Price',
        'TYPE' => 'STRING',
        'DEFAULT' => $defaults['PRICE'],
    ];
    $arComponentParameters['PARAMETERS'][$code . '_PERIOD'] = [
        'PARENT' => $group,
        'NAME' => 'Period',
        'TYPE' => 'STRING',
        'DEFAULT' => $defaults['PERIOD'],
    ];
    $arComponentParameters['PARAMETERS'][$code . '_SPEED'] = [
        'PARENT' => $group,
        'NAME' => 'Speed',
        'TYPE' => 'STRING',
        'DEFAULT' => $defaults['SPEED'],
    ];
    $arComponentParameters['PARAMETERS'][$code . '_FEATURES'] = [
        'PARENT' => $group,
        'NAME' => 'Features (comma-separated)',
        'TYPE' => 'STRING',
        'DEFAULT' => $defaults['FEATURES'],
    ];
    $arComponentParameters['PARAMETERS'][$code . '_IS_POPULAR'] = [
        'PARENT' => $group,
        'NAME' => 'Is Popular',
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => $defaults['IS_POPULAR'],
    ];
    $i++;
}
