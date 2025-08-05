<?php

namespace App\Enum;

/**
 * Class WellnessTipEnum
 * @package App\Enum
 */
final class WellnessTipEnum
{
    /* Required constant parameters */
    const PARM_HEADING = 'heading';
    const PARM_BODY = 'body';
    const PARM_IMAGE = 'image';
    const PARM_MEDIA = 'media';
    const PARM_ICON = 'icon';
    const PARM_DELETE_MEDIA = 'deleteMedia';
    const PARM_DELETE_IMAGE = 'deleteImage';

    /* Required response parameters */
    const STATUS = 'status';
    const MESSAGE = 'message';

    /* Required error messages */
    const ERROR_MESSAGE_BAD_REQUEST = 'Some required parameters are missing.';
    const ERROR_MESSAGE_INTERNAL_SERVER = 'Something went wrong.';
    const ERROR_MESSAGE_INVALID_ROLE = "You don't have rights to perform this action.";
    const ERROR_MESSAGE_NOT_FOUND = "This WellnessTip doesn't exist.";

    const WELLNESSTIP_API_REQUIRED_FIELDS = [
        self::PARM_HEADING,
        self::PARM_BODY,
        self::PARM_ICON
    ];

    const WELLNESSTIP_IMAGES_DIR = "wellnesstip/images/";
    const WELLNESSTIP_MEDIA_DIR = "wellnesstip/media/";

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;
}