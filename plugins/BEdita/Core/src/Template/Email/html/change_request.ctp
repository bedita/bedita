<p><?= __d('bedita', 'Dear {0},', [$user->username]) ?></p>
<p>
    <?= __d('bedita', 'you asked to modify your password in {0}.', [$projectName]) ?>
    <br>
<?= __d('bedita', 'To do so please follow this link:') ?>
</p>

<p><?= $this->Html->link($changeUrl, $changeUrl, ['target' => '_blank']); ?></p>

<p></p>
<p><?= __d('bedita', 'Thank you!') ?></p>
