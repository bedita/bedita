<!DOCTYPE html> 
<html> 
	<head>
	{$html->charset()}
	<title>{$beFront->title()}</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	{$beFront->metaAll()}

	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$html->url("/img/h/apple-touch-icon.png")}">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{$html->url("/img/m/apple-touch-icon.png")}">
	<link rel="apple-touch-icon-precomposed" href="{$html->url("/img/l/apple-touch-icon-precomposed.png")}">
	<link rel="shortcut icon" href="{$html->url("/img/l/apple-touch-icon.png")}">

	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	{literal}
	<script>(function(){var a;if(navigator.platform==="iPad"){a=window.orientation!==90||window.orientation===-90?"img/startup-tablet-landscape.png":"img/startup-tablet-portrait.png"}else{a=window.devicePixelRatio===2?"img/startup-retina.png":"img/startup.png"}document.write('<link rel="apple-touch-startup-image" href="'+a+'"/>')})()</script>
  	{/literal}
  	
  	<meta http-equiv="cleartype" content="on">

	{$html->css("jquery.mobile-1.1.1")}
	{$html->css('photoswipe')}
	{$html->css("common")}

	{$html->script("libs/jquery-1.7.1.min")}
	
	<script type="text/javascript">
		// workaround: if android avoid transitions (issues in android 4.0)
		var ua = navigator.userAgent.toLowerCase();
		var isAndroid40 = ua.indexOf("android 4.0") > -1;
		var defaultTransition = (isAndroid40)? 'none': 'slide';
		$(document).bind("mobileinit", function() {
			$.mobile.defaultPageTransition = defaultTransition;
		});
	</script>

	{$html->script("libs/jquery.mobile-1.1.1.min")}

	{* photoswipe *}
	{$html->script('libs/klass.min')}
	{$html->script('libs/code.photoswipe.jquery-3.0.4.min')}
	
	{$beFront->feeds()}
	{$scripts_for_layout}

</head> 
<body> 

{$content_for_layout}

{* menu page *}
<div data-role="page" id="menu">

	{$view->element('header', ["home" => false])}

	<div data-role="content">		
		<ul data-role="listview">
			{foreach $sectionsTree as $branch}
				<li>
					<a href="{$html->url($branch.canonicalPath)}"><h1>{$branch.title}</h1></a>
				</li>
			{/foreach}
		</ul>
	</div>
	{$view->element('footer', ["active" => "menu"])}
</div>

{* search page *}
<div data-role="page" id="search">

	{$view->element('header', ["home" => false])}

	<div data-role="content">
		{$view->element('form_search')}
	</div>
	{$view->element('footer', ["active" => "search"])}
</div>

{* credits page *}
<div data-role="page" id="credits">

	{$view->element('header', ["home" => false])}

	<div data-role="content">
		{$view->element('credits')}
	</div>
	{$view->element('footer', ["active" => "credits"])}
</div>

{$beFront->stats()}
</body>
</html>