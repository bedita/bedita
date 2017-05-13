<p><?= __d('bedita', 'Dear {0},', [$user->username]) ?></p>
<p>
    <?= __d('bedita', 'thank you for registering to {0}.', [$appName]) ?>
    <br>
    <?= __d('bedita', 'To confirm your registration, please follow this link:') ?>
</p>

<p><?= $this->Html->link($activationUrl, $activationUrl, ['target' => '_blank']); ?></p>

<p></p>
<p><?= __d('bedita', 'Regards') ?></p>
