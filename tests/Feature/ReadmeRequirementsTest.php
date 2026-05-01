<?php

namespace Takielias\Lab\Tests\Feature;

use Takielias\Lab\Tests\TestCase;

class ReadmeRequirementsTest extends TestCase
{
    private const README = __DIR__.'/../../README.md';

    private function readme(): string
    {
        return file_get_contents(self::README);
    }

    public function test_requirements_section_present(): void
    {
        $this->assertStringContainsString('## Requirements', $this->readme());
    }

    public function test_advertises_modern_php_and_laravel(): void
    {
        $readme = $this->readme();

        $this->assertStringContainsString('8.3', $readme, 'README must state PHP 8.3 minimum.');
        $this->assertMatchesRegularExpression('/Laravel.+11\.x.+12\.x.+13\.x/s', $readme, 'README must list L11/L12/L13.');
        $this->assertStringContainsString('Node', $readme, 'README must mention Node requirement.');
    }

    public function test_no_jquery_advertising_in_pitch(): void
    {
        $readme = $this->readme();

        $this->assertStringNotContainsString(
            'jQuery AJAX calls',
            $readme,
            'Drop the legacy "jQuery AJAX calls" pitch — package now uses native fetch.'
        );
    }

    public function test_native_fetch_example_present(): void
    {
        $readme = $this->readme();

        $this->assertStringContainsString('window.ajaxPost', $readme, 'README should show the native window.ajaxPost helper.');
        $this->assertStringContainsString('FormData', $readme);
        $this->assertStringContainsString('addEventListener', $readme);
    }
}
