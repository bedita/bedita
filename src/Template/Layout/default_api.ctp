<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEdita4 - API response</title>
</head>
<body>

<pre>
<?php
    $d = [];
    foreach ($_serialize as $k) {
        $d[$k] = ${$k};
    }
    $json = json_encode($d, JSON_PRETTY_PRINT);
    echo $json;

?>
</pre>

</body>
