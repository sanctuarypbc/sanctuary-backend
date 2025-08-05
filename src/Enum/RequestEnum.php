<?php

namespace App\Enum;

/**
 * Class RequestEnum
 * @package App\Enum
 */
class RequestEnum
{
    const STATUS_DEFAULT = 1;
    const STATUS_NOT_DEFAULT = 0;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    const CLIENT_REQUEST_DENIED = 0;
    const CLIENT_REQUEST_APPROVED = 1;
    const CLIENT_REQUEST_PENDING = 2;
    const CLIENT_REQUEST_FLAGGED = 3;
    const CLIENT_REQUEST_REJECTED = 4;

    const CLIENT_REQUEST_STATUS_ARRAY = [
        "Denied" => self::CLIENT_REQUEST_DENIED,
        "Approved" => self::CLIENT_REQUEST_APPROVED,
        "Pending" => self::CLIENT_REQUEST_PENDING,
        "Flagged" => self::CLIENT_REQUEST_FLAGGED,
        "Rejected" => self::CLIENT_REQUEST_REJECTED
    ];
}