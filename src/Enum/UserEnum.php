<?php

namespace App\Enum;

/**
 * Class UserEnum
 * @package App\Enum
 */
final class UserEnum
{
    CONST DEFAULT_PASSWORD_PREFIX = 'dummyPass_';

    const ROLE_SUPER_ADMIN = "ROLE_SUPER_ADMIN";
    const ROLE_CLIENT = "ROLE_CLIENT";
    const ROLE_FACILITY = "ROLE_FACILITY";
    const ROLE_FIRST_RESPONDER = "ROLE_FIRST_RESPONDER";
    const ROLE_ADVOCATE = "ROLE_ADVOCATE";
    const ROLE_ORGANIZATION = "ROLE_ORGANIZATION";
}