<?php
return [
    /**
     * Accepted Content Types configuration
     *
     */
    'Accept' => [
        'html' => filter_var(env('ACCEPT_HTML', 'false'), FILTER_VALIDATE_BOOLEAN),
        //'xml' => filter_var(env('ACCEPT_XML', 'false'), FILTER_VALIDATE_BOOLEAN),
    ]
];
