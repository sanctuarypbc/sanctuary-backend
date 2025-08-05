<?php

namespace App\Enum;

/**
 * Class OrganizationEnum
 * @package App\Enum
 */
class OrganizationEnum
{
    const CREATE_ORGANIZATION_API_REQUIRED_FIELDS = [
        'username',
        'password',
        'name',
        'street_address',
        'city',
        'zip_code',
        'state',
        'contact_name',
        'contact_phone',
        'contact_email',
        'type_id',
        'client_type_ids'
    ];

    const ORGANIZATION_FORM_FIELDS_WITH_PROPERTIES = [
        'name' => 'Name',
        'street_address' => 'StreetAddress',
        'city' => 'City',
        'zip_code' => 'ZipCode',
        'state' => 'State',
        'contact_name' => 'ContactName',
        'contact_phone' => 'ContactPhone',
        'contact_email' => 'ContactEmail',
        'lat' => 'Lat',
        'lng' => 'lng'
    ];

    const ORGANIZATION_POSSIBLE_UPDATE_FIELDS = [
        'name',
        'street_address',
        'city',
        'zip_code',
        'state',
        'contact_name',
        'contact_phone',
        'contact_email',
        'type_id',
        'client_type_ids'
    ];
}