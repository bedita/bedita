<?php 
	echo $rss->header();
	$channel = $rss->channel(array(), $channelData,$rss->serialize($items,array('format'=>'tags','slug' => false)));
	echo $rss->document($attrib, $channel);
?>