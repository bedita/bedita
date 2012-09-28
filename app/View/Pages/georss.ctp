<?php 
	echo $this->Rss->header();
	$channel = $this->Rss->channel(array(), $channelData,$this->Rss->serialize($items,array('format'=>'tags','slug' => false)));
	echo $this->Rss->document($attrib, $channel);
?>