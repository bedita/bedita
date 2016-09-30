<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= $this->Html->css('jquery.jsonview'); ?>
    <?= $this->Html->script('jquery.min'); ?>
    <?= $this->Html->script('jquery.jsonview'); ?>

    <title><?= $pageTitle ?></title>

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
