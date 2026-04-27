<?php

namespace Takielias\Lab\Tests\Feature;

use Takielias\Lab\Tests\TestCase;

/**
 * Locks the test environment contract: tests run on SQLite :memory:
 * without requiring an external MySQL/Postgres server. Catches future
 * regressions that re-introduce host-DB probes (which break stripped-
 * down CI runners and contributor laptops).
 */
class EnvironmentSetupTest extends TestCase
{
    public function test_default_connection_is_sqlite(): void
    {
        $driver = config('database.default');

        $this->assertSame('testing', $driver, 'Testbench default connection name should be "testing".');
        $this->assertSame('sqlite', config('database.connections.testing.driver'));
    }

    public function test_default_database_is_in_memory(): void
    {
        $this->assertSame(':memory:', config('database.connections.testing.database'));
    }

    public function test_no_mysql_probes_in_test_files(): void
    {
        $testsDir = realpath(__DIR__.'/../');

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($testsDir, \FilesystemIterator::SKIP_DOTS));

        $offenders = [];
        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            // Skip this test class — its probe-list strings trip its own assertion.
            if ($file->getBasename() === 'EnvironmentSetupTest.php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            // Block these MySQL/external-DB probe patterns.
            foreach ([
                'DB_CONNECTION=mysql',
                "'driver' => 'mysql'",
                'mysql:host=',
                'pdo_mysql',
            ] as $needle) {
                if (str_contains($contents, $needle)) {
                    $offenders[] = $file->getFilename().' — '.$needle;
                }
            }
        }

        $this->assertEmpty(
            $offenders,
            "MySQL probes found in test files:\n  - ".implode("\n  - ", $offenders)
        );
    }
}
