<?php
return [
    /**
     * Requiremnts:
     *  - min php version
     *  - extensions
     */
    'Requirements' => [
        'phpMin' => '5.5.9',
        'extensions' => ['mbstring', 'intl'],
        'writable' => [TMP, LOGS]
     ],
];
