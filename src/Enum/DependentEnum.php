<?php

namespace App\Enum;

/**
 * Class DependentEnum
 * @package App\Enum
 */
class DependentEnum
{
    const CREATE_DEPENDENT_API_REQUIRED_FIELDS = [
        'first_name',
        'age'
    ];

    const DEPENDENT_POSSIBLE_UPDATE_FIELDS_ALL = [
        'first_name',
        'last_name',
        'gender',
        'parent',
        'phone',
        'age',
        'clothing_size',
        'shoe_size'
    ];

    const DEPENDENT_POSSIBLE_UPDATE_FIELDS = [
        "first_name" => "FirstName",
        "last_name" => "LastName",
        "phone" => "Phone",
        "gender" => "Gender",
        "parent" => "Parent",
        "age" => "Age",
        "clothing_size" => "ClothingSize",
        "shoe_size" => "ShoeSize",
    ];
}