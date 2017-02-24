<?php
$this->layout = 'html';
$this->assign('title', __('BEdita 4 - API Response'));

$this->append('css', '
    <style>
        h1::before {
            content: "\266A \266B";
            color: #42BD41;
            font-size: 1em;
            margin-right: 0.5em;
        }
    </style>
');

echo $this->element('json_display');
