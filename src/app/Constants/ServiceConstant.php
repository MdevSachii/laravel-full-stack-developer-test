<?php

namespace App\Constants;

class ServiceConstant extends BaseConstant
{
    public const SECTIONS = [
        'Washing section' => [
            'full_wash' => 'Full wash',
            'body_wash' => 'Body wash',
        ],
        'Interior cleaning section' => [
            'vacum' => 'Vacum',
            'shampoo' => 'Shampoo',
        ],
        'Service section' => [
            'engine_oil_replacement' => 'Engine oil replacement',
            'break_oil_replacement' => 'Break oil replacement',
            'coolant_replacement' => 'Coolant replacement',
            'air_filter_replacement' => 'Air filter replacement',
            'oil_filter_replacement' => 'Oil filter replacement',
            'ac_filter_replacement' => 'AC filter replacement',
            'break_shoes_replacement' => 'Break shoes replacement',
        ],
    ];
}
