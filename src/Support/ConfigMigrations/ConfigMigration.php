<?php

namespace RonasIT\Chat\Support\ConfigMigrations;

abstract class ConfigMigration
{
    /**
     * Transform the consumer's config array from the previous schema version to this one.
     *
     * IMPORTANT: transform the consumer's existing values, never stamp the new defaults
     * over them — otherwise a customized config silently resets to defaults on upgrade.
     */
    abstract public function up(array $config): array;
}
