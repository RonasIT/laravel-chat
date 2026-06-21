<?php

namespace RonasIT\Chat\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use RonasIT\Chat\Support\ConfigMigrations\ConfigMigrator;

class MakeConfigMigrationCommand extends Command
{
    protected $signature = 'chat:make-config-migration {name : Descriptive name, e.g. RenameSeparator}';

    protected $description = 'Scaffold a new chat config migration with the next version number.';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));

        if ($name === '') {
            $this->error('Migration name must not be empty.');

            return self::FAILURE;
        }

        $migrationsPath = dirname(__DIR__, 3) . '/config_migrations';

        $version = (new ConfigMigrator($migrationsPath))->getLatestVersion() + 1;
        $fileName = 'V' . str_pad((string) $version, 4, '0', STR_PAD_LEFT) . "{$name}.php";
        $path = "{$migrationsPath}/{$fileName}";

        if (file_exists($path)) {
            $this->error("Migration [{$fileName}] already exists.");

            return self::FAILURE;
        }

        $stub = strtr(file_get_contents(__DIR__ . '/stubs/config-migration.stub'), [
            '{{ version }}' => $version,
            '{{ previous }}' => $version - 1,
        ]);

        file_put_contents($path, $stub);

        $this->info("Created config migration [config_migrations/{$fileName}] (v{$version}).");
        $this->warn("Don't forget to update config/chat.php to the new schema and bump its \"version\" to {$version}.");

        return self::SUCCESS;
    }
}
