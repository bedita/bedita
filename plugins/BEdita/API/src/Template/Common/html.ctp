<?php
/* @var \Psr\Http\Message\ServerRequestInterface $request */
/* @var \Psr\Http\Message\ResponseInterface $response */
$this->assign('title', __('BEdita 4 - API Response'));

$this->Html->css('BEdita/API.jquery.jsonview', ['block' => true]);
$this->Html->script('BEdita/API.jquery.min', ['block' => true]);
$this->Html->script('BEdita/API.jquery.jsonview', ['block' => true]);

$colors = [
    1 => '#08C', // Informational
    2 => '#0A0', // Success
    3 => '#C80', // Redirect
    4 => '#A00', // Client error
    5 => '#A00', // Server error
];
$color = $colors[intval($response->getStatusCode() / 100)];

?>
<h1>
    <span style="<?= $this->Html->style(['color' => $color, 'font-size' => '1em', 'margin-right' => '.5em']); ?>">&#9673;</span>
    <?= sprintf('%s %s', $request->getMethod(), urldecode((string)$request->getUri()->getPath())) ?>
</h1>

<script type="application/json" id="response"><?= $response->getBody() ?></script>
<script type="text/javascript">
    if (jQuery) {
        var jsonData = JSON.parse(document.getElementById("response").textContent);
        $(function() {
            $("#main").JSONView(jsonData);
        });
    }
</script>

<div id="main"></div>
