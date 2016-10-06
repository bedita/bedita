<?php

$this->layout = null;

$res = [
            'error' => [
                'status' => $code,
                'title' => $message,
                'meta' => array_filter(compact('trace'))
            ]
        ];
$responseBody = json_encode($res);
$pageTitle = 'Internal Error';

echo $this->element('json_display', compact('pageTitle', 'responseBody'));
