<?php

namespace App\Enum;

/**
 * Class RequestEnum
 * @package App\Enum
 */
class GoalEnum
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    const ACTION_UNASSIGN = "unassign";
    const ACTION_ASSIGN = "assign";
    const ACTION_ARRAY = [self::ACTION_ASSIGN, self::ACTION_UNASSIGN];
}