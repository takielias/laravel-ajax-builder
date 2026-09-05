<?php

spl_autoload_register(function (string $class): void {
    $legacy = 'Takielias\\Lab\\';

    if (! str_starts_with($class, $legacy)) {
        return;
    }

    $path = __DIR__.'/'.str_replace('\\', '/', substr($class, strlen($legacy))).'.php';

    if (is_file($path)) {
        require_once $path;
    }
});
