<?php

namespace Takielias\Lab\Tests\Feature;

use Takielias\Lab\Tests\TestCase;

/**
 * Locks the contract that the bundled JS modules use native browser
 * APIs (fetch, FormData, addEventListener, querySelector) instead of
 * jQuery. Catches future regressions back to `$()` / `$.ajax`.
 */
class JsModernizationTest extends TestCase
{
    private const JS_DIR = __DIR__.'/../../resources/js';

    private function ajaxRequest(): string
    {
        return file_get_contents(self::JS_DIR.'/modules/ajax-request.js');
    }

    private function buttonLoader(): string
    {
        return file_get_contents(self::JS_DIR.'/plugins/button-loader/button.loader.js');
    }

    public function test_ajax_request_uses_fetch_not_jquery(): void
    {
        $js = $this->ajaxRequest();

        $this->assertStringContainsString('fetch(', $js, 'Must use native fetch()');
        $this->assertStringContainsString('FormData', $js);
        $this->assertStringNotContainsString('$.ajax', $js, 'Drop $.ajax');
        $this->assertStringNotContainsString('$.param', $js, 'Drop $.param');
    }

    public function test_ajax_request_uses_native_dom_apis(): void
    {
        $js = $this->ajaxRequest();

        $this->assertStringContainsString('document.querySelector', $js);
        $this->assertStringContainsString('addEventListener', $js);
        $this->assertStringContainsString('URLSearchParams', $js);
    }

    public function test_ajax_request_drops_jquery_selectors(): void
    {
        $js = $this->ajaxRequest();

        // No raw jQuery `$('selector')` or `$('#id')` patterns.
        $this->assertDoesNotMatchRegularExpression(
            "/\\\$\(['\"]/",
            $js,
            'No jQuery $(...) selectors allowed.'
        );

        // No `$.delegate` event delegation.
        $this->assertStringNotContainsString('.delegate(', $js);
    }

    public function test_ajax_request_preserves_window_api_surface(): void
    {
        $js = $this->ajaxRequest();

        foreach ([
            'window.ajaxRequest',
            'window.ajaxGet',
            'window.ajaxPost',
            'window.ajaxPut',
            'window.ajaxPatch',
            'window.executeAjaxCall',
            'window.resetForm',
            'window.fadeOutAndClear',
            'window.loadModal',
        ] as $api) {
            $this->assertStringContainsString(
                $api,
                $js,
                "Public API surface must preserve {$api} for backwards compatibility."
            );
        }
    }

    public function test_button_loader_dropped_jquery(): void
    {
        $js = $this->buttonLoader();

        $this->assertStringNotContainsString('jQuery', $js);
        $this->assertStringNotContainsString('$.fn.', $js);
        $this->assertStringContainsString('window.buttonLoader', $js, 'Public API marker.');
        $this->assertStringContainsString('document.querySelectorAll', $js);
    }

    public function test_legacy_jquery_plugin_files_removed(): void
    {
        $this->assertFileDoesNotExist(self::JS_DIR.'/plugins/button-loader/jquery.button.loader.js');
        $this->assertFileDoesNotExist(self::JS_DIR.'/plugins/button-loader/jquery.button.loader.min.js');
    }

    public function test_csrf_token_read_via_meta_content(): void
    {
        $js = $this->ajaxRequest();

        $this->assertMatchesRegularExpression(
            '/meta\[name="csrf-token"\]/',
            $js,
            'Must read the CSRF token from <meta name="csrf-token"> like every Laravel install.'
        );
        $this->assertStringContainsString('X-CSRF-TOKEN', $js);
    }
}
