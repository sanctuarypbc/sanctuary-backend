<?php

namespace App\Enum;

/**
 * Class FacilityEnum
 * @package App\Enum
 */
final class FacilityInventoryEnum
{
    const FACILITY_INVENTORY_POSSIBLE_UPDATE_FIELDS_ALL = [
        'name',
        'capacity',
        'total_available',
        'inventory_type_id'
    ];

    const INVENTORY_ASSIGNMENT_POSSIBLE_FIELDS = [
        'assigned_at',
        'client_id',
        'inventory_data'
    ];

    const CLIENT_INVENTORY_ACTION_API_POSSIBLE_FIELDS = [
        'type',
        'time',
        'client_id',
    ];

    const ACTION_CHECKIN = 'checkin';
    const ACTION_CHECKOUT = 'checkout';
}