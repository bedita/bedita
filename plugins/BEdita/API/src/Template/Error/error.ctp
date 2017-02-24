<?php
$statusCode = $this->response->httpCodes($this->response->statusCode());
$this->layout = 'html';
$this->assign('title', __('BEdita 4 - API Error') . ': ' . key($statusCode) . ' ' . current($statusCode));

$this->append('css', '
    <style>
        h1::before {
            content: "\26D4";
            color: #D33C44;
            font-size: 1em;
            margin-right: 0.5em;
        }
    </style>
');

echo $this->element('json_display');
