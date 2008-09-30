<?php 
    echo $rss->header();
    $channel = $rss->channel(array(), $channelData, $rss->items($items));
    echo $rss->document(array(), $channel);
?>