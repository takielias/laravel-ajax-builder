<?php

namespace Takielias\Lab\Tests\Feature;

use Takielias\Lab\Tests\TestCase;

/**
 * Asserts every public/protected method in src/ has explicit param +
 * return type declarations. Catches future drift back to untyped
 * parameters or missing return types.
 */
class TypeCoverageTest extends TestCase
{
    private const SRC = __DIR__.'/../../src';

    public function test_all_public_methods_have_return_types(): void
    {
        $offenders = [];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::SRC, \FilesystemIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace(realpath(self::SRC).'/', '', $file->getPathname());
            $contents = file_get_contents($file->getPathname());

            // Skip migration stubs and similar non-class files.
            if (! preg_match('/\bclass\s+\w+/', $contents)) {
                continue;
            }

            // public function name(...)  — must have a `: type` after the closing paren.
            // Allow `: void`, `: static`, `: self`, `: T`, `: ?T`, `: T|U`, etc.
            if (preg_match_all(
                '/(?:public|protected)\s+(?:static\s+)?function\s+(\w+)\s*\([^)]*\)(?!\s*:)/',
                $contents,
                $matches,
                PREG_OFFSET_CAPTURE
            )) {
                foreach ($matches[1] as $match) {
                    if ($match[0] === '__construct') {
                        continue;
                    }
                    $line = substr_count(substr($contents, 0, $match[1]), "\n") + 1;
                    $offenders[] = "{$relative}:{$line} {$match[0]}";
                }
            }
        }

        $this->assertEmpty(
            $offenders,
            "Methods missing return-type declarations:\n  - ".implode("\n  - ", $offenders)
        );
    }

    public function test_all_method_parameters_are_typed(): void
    {
        $offenders = [];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::SRC, \FilesystemIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace(realpath(self::SRC).'/', '', $file->getPathname());
            $contents = file_get_contents($file->getPathname());

            // Match `function name(<params>)` then inspect each param.
            if (preg_match_all(
                '/function\s+(\w+)\s*\(([^)]*)\)/',
                $contents,
                $matches,
                PREG_OFFSET_CAPTURE
            )) {
                foreach ($matches[1] as $i => $nameMatch) {
                    $params = trim($matches[2][$i][0]);
                    if ($params === '') {
                        continue;
                    }

                    foreach (explode(',', $params) as $param) {
                        $param = trim($param);
                        if ($param === '') {
                            continue;
                        }

                        // Each param must start with a type (word/leading-? or a primitive),
                        // before the `$` variable marker.
                        if (preg_match('/^\$\w+/', $param)) {
                            $line = substr_count(substr($contents, 0, $matches[1][$i][1]), "\n") + 1;
                            $offenders[] = "{$relative}:{$line} {$nameMatch[0]}() — untyped param: {$param}";
                        }
                    }
                }
            }
        }

        $this->assertEmpty(
            $offenders,
            "Untyped parameters:\n  - ".implode("\n  - ", $offenders)
        );
    }
}
