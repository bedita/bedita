<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

$this->layout = null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--
    <link rel="stylesheet" href="https://rawgithub.com/yesmeck/jquery-jsonview/master/dist/jquery.jsonview.css" />
    <script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
    <script type="text/javascript" src="https://rawgithub.com/yesmeck/jquery-jsonview/master/dist/jquery.jsonview.js"></script>
    -->

    <?= $this->Html->css('BEdita/API.jquery.jsonview'); ?>
    <?= $this->Html->script('BEdita/API.jquery.min'); ?>
    <?= $this->Html->script('BEdita/API.jquery.jsonview'); ?>

    <title><?= __('BEdita 4 - API response') ?></title>

    <script type="text/javascript">
        var jsonData = <?= $responseBody ?>;
        $(function() {
            $("#main").JSONView(jsonData);
        });
    </script>
</head>
<body>
    <h1><?= sprintf('%s %s', $method, h($url)) ?></h1>

    <div id="main"></div>

</body>
