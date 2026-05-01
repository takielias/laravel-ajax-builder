<?php

namespace Takielias\Lab\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Takielias\Lab\Commands\InstallLAB;
use Takielias\Lab\Lab;
use Takielias\Lab\Tests\TestCase;

/**
 * Regression guard. Locks current state of views, view components,
 * commands, facades, and publish tags before modernization work begins.
 */
class SnapshotBaselineTest extends TestCase
{
    private string $baselineDir;

    private string $packageRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baselineDir = __DIR__.'/../__snapshots__/baseline';
        $this->packageRoot = realpath(__DIR__.'/../../');
    }

    public function test_view_inventory_unchanged(): void
    {
        $files = $this->listFiles('resources/views');
        sort($files);
        $this->assertSnapshot('view-inventory.txt', implode("\n", $files)."\n");
    }

    public function test_view_hashes_unchanged(): void
    {
        $hashes = [];
        foreach ($this->listFiles('resources/views') as $rel) {
            $hashes[$rel] = hash_file('sha256', $this->packageRoot.'/resources/views/'.$rel);
        }
        ksort($hashes);
        $this->assertSnapshot('view-hashes.json', json_encode($hashes, JSON_PRETTY_PRINT)."\n");
    }

    public function test_view_components_unchanged(): void
    {
        $files = $this->listFiles('src/View/Components');
        sort($files);
        $this->assertSnapshot('view-components.txt', implode("\n", $files)."\n");
    }

    public function test_lab_install_command_signature_unchanged(): void
    {
        $command = $this->app->make(InstallLAB::class);
        $this->assertSnapshot('lab-install-signature.txt', $command->getName()."\n");
    }

    public function test_facade_class_unchanged(): void
    {
        $reflection = new \ReflectionClass(\Takielias\Lab\Facades\Lab::class);
        $methods = array_map(fn ($m) => $m->getName(), $reflection->getMethods(\ReflectionMethod::IS_PUBLIC));
        sort($methods);
        $this->assertSnapshot('facade-methods.txt', implode("\n", $methods)."\n");
    }

    public function test_lab_class_public_api_unchanged(): void
    {
        $reflection = new \ReflectionClass(Lab::class);
        $methods = array_map(fn ($m) => $m->getName(), $reflection->getMethods(\ReflectionMethod::IS_PUBLIC));
        sort($methods);
        $this->assertSnapshot('lab-class-methods.txt', implode("\n", $methods)."\n");
    }

    public function test_publish_tags_unchanged(): void
    {
        $tags = ServiceProvider::publishableGroups();
        sort($tags);
        $this->assertSnapshot('publish-tags.txt', implode("\n", $tags)."\n");
    }

    private function listFiles(string $relDir): array
    {
        $fs = new Filesystem;
        $abs = $this->packageRoot.'/'.$relDir;
        if (! is_dir($abs)) {
            return [];
        }
        $files = [];
        foreach ($fs->allFiles($abs) as $file) {
            $files[] = ltrim(str_replace($abs, '', $file->getPathname()), '/\\');
        }

        return $files;
    }

    private function assertSnapshot(string $name, string $actual): void
    {
        $path = $this->baselineDir.'/'.$name;
        if (! is_dir($this->baselineDir)) {
            mkdir($this->baselineDir, 0755, true);
        }
        if (getenv('UPDATE_SNAPSHOTS') === '1' || ! file_exists($path)) {
            file_put_contents($path, $actual);
            $this->markTestSkipped("Wrote baseline: {$name}");
        }
        $expected = file_get_contents($path);
        $this->assertSame($expected, $actual, "Snapshot drift in {$name}. If intentional, re-run with UPDATE_SNAPSHOTS=1 and commit changes.");
    }
}
