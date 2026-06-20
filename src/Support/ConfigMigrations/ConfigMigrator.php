<?php

namespace RonasIT\Chat\Support\ConfigMigrations;

class ConfigMigrator
{
    public function __construct(
        protected string $migrationsPath,
    ) {
    }

    /**
     * The highest version shipped by the package (the target every config is migrated up to).
     */
    public function getLatestVersion(): int
    {
        $versions = array_keys($this->migrationFiles());

        return empty($versions) ? 0 : max($versions);
    }

    public function hasPending(int $fromVersion): bool
    {
        return $fromVersion < $this->getLatestVersion();
    }

    /**
     * Apply every migration newer than $fromVersion, in ascending numeric version order,
     * and stamp the resulting version into the returned config array.
     */
    public function migrate(array $config, ?int $fromVersion = null): array
    {
        $fromVersion ??= (int) ($config['version'] ?? 0);

        foreach ($this->getPendingSteps($fromVersion) as $version => $migrations) {
            foreach ($migrations as $migration) {
                $config = $migration->up($config);
            }

            $config['version'] = $version;
        }

        return $config;
    }

    /**
     * @return array<int, array<int, ConfigMigration>> version => [migrations], sorted ascending.
     */
    protected function getPendingSteps(int $fromVersion): array
    {
        $steps = [];

        foreach ($this->migrationFiles() as $version => $files) {
            if ($version <= $fromVersion) {
                continue;
            }

            // Explicit sort — never rely on glob()/filesystem iteration order (differs across OSes).
            sort($files);

            $steps[$version] = array_map(fn (string $file): ConfigMigration => require $file, $files);
        }

        // Explicit numeric ascending order, independent of how the directory was iterated.
        ksort($steps);

        return $steps;
    }

    /**
     * Discover migration files and group them by the numeric version parsed from the
     * `V{n}` filename prefix (e.g. `V1RestructureUserAndMediaClasses.php` -> 1).
     *
     * @return array<int, array<int, string>> version => [absolute file paths]
     */
    protected function migrationFiles(): array
    {
        $files = [];

        foreach (glob("{$this->migrationsPath}/*.php") ?: [] as $file) {
            if (preg_match('/^V(\d+)/', basename($file), $matches)) {
                $files[(int) $matches[1]][] = $file;
            }
        }

        return $files;
    }
}
