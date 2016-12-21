<?php

$urlParts = explode('/', urldecode($_SERVER['REQUEST_URI']));

if (count($urlParts) >= 1 && $urlParts[1] == 'p') {
    $project = $urlParts[2];
    define('BE_PROJECT_URL', 'p/' . $project);

    $projectResources = [
        'be4_dev' => [
            'DATABASE_URL' => 'mysql://bedita:b3d1t4@localhost/be4_dev?encoding=utf8&timezone=UTC&cacheMetadata=true'
        ],
        'be4_test' => [
            'DATABASE_URL' => 'mysql://bedita:b3d1t4@localhost/be4_test?encoding=utf8&timezone=UTC&cacheMetadata=true'
        ]
    ];

    if (!empty($projectResources[$project])) {
        foreach ($projectResources[$project] as $key => $value) {
            $_ENV[$key] = $value;
        }
    }

}
