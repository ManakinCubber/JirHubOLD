<?php

namespace App\Constant;

class GithubLabels
{
    const PRIO    = 'Prio';
    const BUG     = 'bug';
    const WIP     = 'WIP';
    const WAIT    = 'WAIT';
    const STANDBY = 'Standby';

    public static function getDevelopmentInProgressLabels()
    {
        return [
            self::WAIT,
            self::WIP,
            self::STANDBY,
        ];
    }
}
