<?php

namespace RonasIT\Chat\Console\Commands;

use Illuminate\Console\Command;
use RonasIT\Chat\Support\ConfigMigrations\ConfigMigrator;
use Winter\LaravelConfigWriter\ArrayFile;

class ConfigUpgradeCommand extends Command
{
    protected $signature = 'chat:config-upgrade';

    protected $description = 'Migrate the published chat config to the latest schema version.';

    public function handle(): int
    {
        $configPath = config_path('chat.php');

        if (!file_exists($configPath)) {
            $this->error("Published config not found at [{$configPath}]. Publish it first with vendor:publish.");

            return self::FAILURE;
        }

        $current = require $configPath;
        $fromVersion = (int) ($current['version'] ?? 0);

        $migrator = new ConfigMigrator(dirname(__DIR__, 3) . '/config_migrations');

        if (!$migrator->hasPending($fromVersion)) {
            $this->info("Chat config is already up to date (v{$fromVersion}).");

            return self::SUCCESS;
        }

        $migrated = $migrator->migrate($current, $fromVersion);
        $toVersion = $migrator->getLatestVersion();

        // Back up the original before rewriting it.
        $backupPath = "{$configPath}.v{$fromVersion}.backup";
        copy($configPath, $backupPath);

        // winter/laravel-config-writer edits the existing file in place, keeping the
        // consumer's comments and formatting for keys it doesn't touch. set() accepts
        // the whole migrated array, so the up() result is applied as one operation.
        ArrayFile::open($configPath)
            ->set($migrated)
            ->write();

        $this->info("Chat config migrated v{$fromVersion} -> v{$toVersion}.");
        $this->line("A backup of the previous version was saved to [{$backupPath}].");

        return self::SUCCESS;
    }
}
