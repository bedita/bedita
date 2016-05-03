<pre>
<?php
    $jsonData = [];
    foreach ($_serialize as $k) {
        $jsonData[$k] = ${$k};
    }
    echo json_encode($jsonData, JSON_PRETTY_PRINT);
?>
</pre>
