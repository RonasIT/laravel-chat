<?php

namespace RonasIT\Chat\Tests\Support;

use RonasIT\Support\Testing\TableTestState as BaseTableTestState;

class TableTestState extends BaseTableTestState
{
    protected function getFixturePath(string $fixtureName): string
    {
        $path = parent::getFixturePath($fixtureName);

        return str_replace('vendor/orchestra/testbench-core/laravel/', '', $path);
    }
}
