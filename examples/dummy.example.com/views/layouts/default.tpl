<!doctype html>
<html lang="it">
<head>
	{$html->charset()}
	<title>{$beFront->title()}</title>

	{$beFront->metaAll()}
	{$beFront->metaDc()}

	<link rel="icon" href="{$html->webroot}favicon.png" type="image/png" />

	{$beFront->feeds()}

	{$scripts_for_layout}
	
	{$html->css('base')}
	
	{$javascript->link('jquery')}
	{$javascript->link('frontend')}
	{$javascript->link('flowplayer-3.2.6.min')}
	{$javascript->link('flowplayer.ipad-3.2.2.min')}	
	{$javascript->link('modernizr-2.6.1.js')}
	
</head>

<body>
	<div id="wrapper">
		<div style="float:left; background-color:white; width:890px;">
	
	
	{$content_for_layout}
	
	
	
	{$beFront->stats()}
	</div>
	</div>
</body>
</html>