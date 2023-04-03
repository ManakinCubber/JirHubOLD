<?php

namespace App\Repository\GitHub\Constant;

class PullRequestUpdatableFields
{
    public const BODY = 'body';
    public const TITLE = 'title';

    public static function getConstants(): array
    {
        return [
            self::BODY,
            self::TITLE,
        ];
    }
}
