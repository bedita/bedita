<?php 
    echo $this->Rss->header();
    $channel = $this->Rss->channel(array(), $channelData, $this->Rss->items($items));
    echo $this->Rss->document(array(), $channel);
?>