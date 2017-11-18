<?php

use VCR\VCR;
use VCR\Request;

require __DIR__ . '/../vendor/autoload.php';

$config = VCR::configure();

$config->addRequestMatcher('authorization', function (Request $first, Request $second) {
    return $first->getHeader('Authorization') === $second->getHeader('Authorization');
});

$config->enableLibraryHooks('curl')
    ->enableRequestMatchers([
        'authorization',
        'url',
        'query_string',
        'body',
    ])
    ->setCassettePath(__DIR__ . '/fixtures');

VCR::turnOn();
