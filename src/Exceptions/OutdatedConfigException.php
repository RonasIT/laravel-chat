<?php

namespace RonasIT\Chat\Exceptions;

use RuntimeException;

class OutdatedConfigException extends RuntimeException
{
    public function __construct(int $currentVersion, int $latestVersion)
    {
        parent::__construct(
            "Your chat config is at v{$currentVersion}, the package expects v{$latestVersion}. "
            . 'Run "php artisan chat:config-upgrade" to migrate (a backup will be created).',
        );
    }
}
