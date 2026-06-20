<?php

namespace RonasIT\Chat\Support\ConfigMigrations;

abstract class ConfigMigration
{
    /**
     * The schema version this migration produces. Authoritative — the applied order is
     * derived from this, not from the file name (the file name prefix is only for readability).
     */
    abstract public function version(): int;

    /**
     * Transform the consumer's config array from the previous schema version to this one.
     *
     * IMPORTANT: transform the consumer's existing values, never stamp the new defaults
     * over them — otherwise a customized config silently resets to defaults on upgrade.
     */
    abstract public function up(array $config): array;
}
