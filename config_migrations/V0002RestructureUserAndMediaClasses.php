<?php

use RonasIT\Chat\Support\ConfigMigrations\ConfigMigration;

/**
 * v0 -> v1 (PR #65): nest the flat `classes.user_model` / `classes.media_model`
 * under `classes.user.model` / `classes.media.model` and introduce
 * `classes.user.columns` (full_name / full_name_separator / avatar).
 *
 * The consumer's custom model values are carried over verbatim; only the new
 * `columns` block falls back to defaults.
 */
return new class extends ConfigMigration {
    public function version(): int
    {
        return 2;
    }

    public function up(array $config): array
    {
        $config['new_param'] = 'test';

        return $config;
    }
};
