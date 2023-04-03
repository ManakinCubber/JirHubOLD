<?php

namespace App\Constant;

class GithubLabels
{
    public const PRIO = 'Prio';
    public const BUG = 'bug';
    public const WIP = 'WIP';
    public const WAIT = 'WAIT';
    public const STANDBY = 'Standby';

    public static function getDevelopmentInProgressLabels()
    {
        return [
            self::WAIT,
            self::WIP,
            self::STANDBY,
        ];
    }
}
