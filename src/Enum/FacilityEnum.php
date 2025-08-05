<?php

namespace App\Enum;

/**
 * Class FacilityEnum
 * @package App\Enum
 */
final class FacilityEnum
{
    const DESKTOP_LOGO_TYPE = 'desktop';
    const MOBILE_LOGO_TYPE = 'mobile';
    const ADD_BOOKING = 1;

    const CREATE_FACILITY_API_REQUIRED_FIELDS = [
        'contact_email',
        'contact_name',
        'name',
        'zip_code',
        'street_address',
        'city',
        'state',
        'primary_color',
        'secondary_color',
        'url_prefix',
        'username',
        'password'
    ];

    const FACILITY_POSSIBLE_UPDATE_FIELDS_ALL = [
        'contact_email',
        'contact_name',
        'name',
        'zip_code',
        'street_address',
        'city',
        'state',
        'opening_time',
        'closing_time',
        'total_dependents',
        'pets_allowed',
        'work_all_day',
        'contact_phone'
    ];

    const DEFAULT_RADIUS = 50;
    const FACILITY_LOGOS_DIR = "/private/facility_logos";

    const BEDS_AVAILABLE = 1;
    const BEDS_NOT_AVAILABLE = 0;

    const DESKTOP_LOGO_MAX_WIDTH = 200;
    const DESKTOP_LOGO_MAX_HEIGHT = 60;
    const MOBILE_LOGO_MAX_WIDTH = 60;
    const MOBILE_LOGO_MAX_HEIGHT = 60;
}