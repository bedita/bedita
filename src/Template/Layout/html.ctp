<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']) ?>

    <title><?= $this->fetch('title') ?></title>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <h1><?= sprintf('%s %s', $method, urldecode($url)) ?></h1>
    <?= $this->fetch('content') ?>
</body>
