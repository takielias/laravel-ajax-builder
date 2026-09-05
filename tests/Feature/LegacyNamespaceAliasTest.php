<?php

namespace TakiElias\Lab\Tests\Feature;

use TakiElias\Lab\Facades\Lab;
use TakiElias\Lab\Tests\TestCase;

class LegacyNamespaceAliasTest extends TestCase
{
    public function test_the_old_facade_name_still_resolves(): void
    {
        $this->assertTrue(class_exists('Takielias\Lab\Facades\Lab'));
        $this->assertSame(Lab::class, (new \ReflectionClass('Takielias\Lab\Facades\Lab'))->getName());
    }

    public function test_the_old_enum_and_builder_names_still_resolve(): void
    {
        $this->assertTrue(class_exists('Takielias\Lab\Lab'));
        $this->assertTrue(enum_exists('Takielias\Lab\Enums\AlertType'));
    }

    public function test_an_unknown_legacy_class_is_not_aliased(): void
    {
        $this->assertFalse(class_exists('Takielias\Lab\DoesNotExist'));
    }
}
