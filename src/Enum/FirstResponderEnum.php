<?php

namespace App\Enum;

/**
 * Class FirstResponderEnum
 * @package App\Enum
 */
class FirstResponderEnum
{
    const CREATE_FR_API_REQUIRED_FIELDS = [
        'email',
        'username',
        'password',
        'first_name',
        'last_name',
        'phone',
        'office_phone',
        'gender',
        'identification_number',
        'type_id',
        'organization_id'
    ];

    const FR_POSSIBLE_UPDATE_FIELDS_ALL = [
        'first_name',
        'last_name',
        'username',
        'email',
        'nick_name',
        'phone',
        'office_phone',
        'gender',
        'identification_number',
        'type_id',
        'organization_id'
    ];

    const FR_USER_ENTITY_POSSIBLE_UPDATE_FIELDS = [
        "first_name" => "FirstName",
        "last_name" => "LastName",
        "phone" => "Phone",
        "gender" => "Gender",
        "username" => "Username",
        "email" => "Email"
    ];
}