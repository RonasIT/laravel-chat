<?php

namespace RonasIT\Chat\Tests\Support;

use RonasIT\Support\Tests\ModelTestState as ModelTestStateBase;

class ModelTestState extends ModelTestStateBase
{
    protected function getFixturePath(string $fixtureName): string
    {
        $path = parent::getFixturePath($fixtureName);

        return str_replace('vendor/orchestra/testbench-core/laravel/', '', $path);
    }
}