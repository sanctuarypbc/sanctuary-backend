<?php

namespace App\Enum;

/**
 * Class NewsfeedEnum
 * @package App\Enum
 */
final class NewsfeedEnum
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    const SHOW_TO_CLIENT = true;
    const DONT_SHOT_TO_CLIENT = false;
    const MAX_SHOW_TO_CLIENT = 3;

    const FILE_TYPE_IMAGE = "image";
    const FILE_TYPE_VIDEO = "video";

    const S3_IMAGES_DIR = "newsfeed/images/";
    const S3_VIDEOS_DIR = "newsfeed/videos/";

    const POSSIBLE_FIELDS = [
        "headline" => "Headline",
        "description" => "Description",
        "show_to_client" => "ShowToClient",
        "link" => "Link"
    ];
}