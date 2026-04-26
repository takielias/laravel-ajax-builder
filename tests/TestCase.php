<?php

namespace Takielias\Lab\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Takielias\Lab\LabServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LabServiceProvider::class,
        ];
    }
}
