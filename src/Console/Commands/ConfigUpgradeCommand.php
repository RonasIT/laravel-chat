<?php

namespace RonasIT\Chat\Console\Commands;

use Illuminate\Console\Command;
use RonasIT\Chat\Support\ConfigMigrations\ConfigMigrator;

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

        // 1. Backup the original file before touching it.
        $backupPath = "{$configPath}.v{$fromVersion}.backup";
        copy($configPath, $backupPath);
        $this->line("Backup written to [{$backupPath}].");

        // 2. Show the diff for human review.
        // TODO: render a readable diff between $current and $migrated.

        // 3. Write the migrated array back as a valid config file.
        // TODO: this is the non-trivial part — serialize $migrated to pretty,
        //       PSR-compatible PHP (var_export() is not enough: short array syntax,
        //       FQCN `::class` references, preserved comments). Until implemented,
        //       do not overwrite the consumer's file.
        $this->warn('File writing is not implemented yet — the config was NOT modified.');
        // file_put_contents($configPath, $this->dump($migrated));

        $this->info("Chat config can be migrated v{$fromVersion} -> v{$migrator->getLatestVersion()}.");

        return self::SUCCESS;
    }
}
