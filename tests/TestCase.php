<?php

namespace TakiElias\Lab\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use TakiElias\Lab\LabServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LabServiceProvider::class,
        ];
    }
}
