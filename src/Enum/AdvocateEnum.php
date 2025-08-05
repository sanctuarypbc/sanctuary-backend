<?php

namespace App\Enum;

/**
 * Class AdvocateEnum
 * @package App\Enum
 */
class AdvocateEnum
{
    const CREATE_Advocate_API_REQUIRED_FIELDS = [
        'identifier',
        'email',
        'username',
        'password',
        'first_name',
        'last_name',
        'phone',
        'service_type_id',
        'organization_id',
        'language_ids'
    ];

    const ADVOCATE_POSSIBLE_UPDATE_FIELDS_ALL = [
        'identifier',
        'first_name',
        'last_name',
        'username',
        'email',
        'phone',
        'additional_phone',
        'emergency_contact',
        'service_type_id',
        'organization_id',
        'language_ids'
    ];

    const ADVOCATE_USER_ENTITY_POSSIBLE_UPDATE_FIELDS = [
        "first_name" => "FirstName",
        "last_name" => "LastName",
        "phone" => "Phone",
        "username" => "Username",
        "email" => "Email"
    ];
}