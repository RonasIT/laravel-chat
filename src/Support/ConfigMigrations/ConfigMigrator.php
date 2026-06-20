<?php

namespace RonasIT\Chat\Support\ConfigMigrations;

use RuntimeException;

class ConfigMigrator
{
    /**
     * Memoized migrations, ordered ascending by version.
     *
     * @var array<int, ConfigMigration>|null
     */
    protected ?array $migrations = null;

    public function __construct(
        protected string $migrationsPath,
    ) {
    }

    /**
     * The highest version shipped by the package (the target every config is migrated up to).
     */
    public function getLatestVersion(): int
    {
        $migrations = $this->migrations();

        return empty($migrations) ? 0 : max(array_map(fn (ConfigMigration $m): int => $m->version(), $migrations));
    }

    public function hasPending(int $fromVersion): bool
    {
        return $fromVersion < $this->getLatestVersion();
    }

    /**
     * Apply every migration newer than $fromVersion, in ascending version order,
     * and stamp the resulting version into the returned config array.
     */
    public function migrate(array $config, ?int $fromVersion = null): array
    {
        $fromVersion ??= (int) ($config['version'] ?? 0);

        foreach ($this->pendingMigrations($fromVersion) as $migration) {
            $config = $migration->up($config);
            $config['version'] = $migration->version();
        }

        return $config;
    }

    /**
     * @return array<int, ConfigMigration> ordered ascending by version.
     */
    protected function pendingMigrations(int $fromVersion): array
    {
        return array_values(array_filter(
            $this->migrations(),
            fn (ConfigMigration $migration): bool => $migration->version() > $fromVersion,
        ));
    }

    /**
     * Load every migration once and order it by the version it declares.
     *
     * Ordering is driven by version() — independent of file name, glob() and filesystem
     * iteration order, so it is identical on every OS.
     *
     * @return array<int, ConfigMigration>
     */
    protected function migrations(): array
    {
        if ($this->migrations !== null) {
            return $this->migrations;
        }

        $migrations = array_map(
            fn (string $file): ConfigMigration => require $file,
            glob("{$this->migrationsPath}/*.php") ?: [],
        );

        usort($migrations, fn (ConfigMigration $a, ConfigMigration $b): int => $a->version() <=> $b->version());

        $this->assertUniqueVersions($migrations);

        return $this->migrations = $migrations;
    }

    /**
     * @param array<int, ConfigMigration> $migrations
     */
    protected function assertUniqueVersions(array $migrations): void
    {
        $versions = array_map(fn (ConfigMigration $migration): int => $migration->version(), $migrations);

        if (count($versions) !== count(array_unique($versions))) {
            throw new RuntimeException('Two config migrations declare the same version.');
        }
    }
}
