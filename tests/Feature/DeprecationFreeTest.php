<?php

namespace Takielias\Lab\Tests\Feature;

use Takielias\Lab\Lab;
use Takielias\Lab\Tests\TestCase;

/**
 * Smoke a representative slice of the Lab API under a custom error
 * handler that captures E_DEPRECATED + E_USER_DEPRECATED. Asserts the
 * package emits zero deprecation triggers when exercised on the
 * supported framework version.
 */
class DeprecationFreeTest extends TestCase
{
    public function test_lab_fluent_chain_emits_no_deprecations(): void
    {
        $captured = [];

        $previous = set_error_handler(
            function (int $errno, string $errstr, string $errfile = '', int $errline = 0) use (&$captured): bool {
                if (in_array($errno, [E_DEPRECATED, E_USER_DEPRECATED], true)) {
                    $captured[] = sprintf('%s in %s:%d', $errstr, $errfile, $errline);

                    return true;
                }

                return false;
            },
            E_DEPRECATED | E_USER_DEPRECATED
        );

        try {
            $lab = new Lab;
            $lab->setStatus(200)
                ->setMessage('Hi')
                ->setSubmitButtonLabel('Save')
                ->setRedirect('/dashboard')
                ->setRedirectDelay(2000)
                ->setFadeOutTime(5000)
                ->disableFadeOut()
                ->asAjax()
                ->enableScrollToTop()
                ->enableTopValidationError()
                ->disableIndividualValidationError()
                ->setSuccess('OK')
                ->setInfo('FYI')
                ->setWarning('Heads up')
                ->setDanger('Bad');
        } finally {
            restore_error_handler();
        }

        $this->assertEmpty(
            $captured,
            "Deprecation triggers fired during Lab API smoke:\n  - ".implode("\n  - ", $captured)
        );
    }
}
