<?php if (\Cake\Core\Plugin::loaded('BEdita/API')): ?>

    <?= $this->Html->css('BEdita/API.jquery.jsonview', ['block' => true]); ?>
    <?= $this->Html->script('BEdita/API.jquery.min', ['block' => true]); ?>
    <?= $this->Html->script('BEdita/API.jquery.jsonview', ['block' => true]); ?>

    <script type="text/javascript">
        if (jQuery) {
            var jsonData = <?= $responseBody ?>;
            $(function() {
                $("#main").JSONView(jsonData);
            });
        }
    </script>

    <div id="main"></div>

<?php else: ?>

    <pre><?= json_encode(json_decode($responseBody), JSON_PRETTY_PRINT); ?></pre>

<?php endif; ?>
