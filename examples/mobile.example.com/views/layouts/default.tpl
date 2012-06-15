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
	  
	  <script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")</script>
  	{/literal}
  	
  	<meta http-equiv="cleartype" content="on">

	{$html->css("jquery.mobile-1.1.0")}
	{$html->css('photoswipe')}
	{$html->css("common")}

	{$html->script("libs/jquery-1.7.1.min")}
	
	<script type="text/javascript">
	{literal}
	$(document).bind("mobileinit", function(){
	  $.mobile.defaultPageTransition = 'slide';
	});
	{/literal}
	</script>

	{$html->script("libs/jquery.mobile-1.1.0.min")}

	{* photoswipe *}
	{$html->script('libs/klass.min')}
	{$html->script('libs/code.photoswipe.jquery-3.0.4.min')}
	
	{$beFront->feeds()}
	{$scripts_for_layout}

</head> 
<body> 

{$content_for_layout}

<div data-role="page" id="menu">

	{$view->element('header')}

	<div data-role="content">		
		<ul data-role="listview">
			{foreach $sectionsTree as $branch}
				<li>
					<a href="{$html->url($branch.canonicalPath)}"><h1>{$branch.title}</h1></a>
				</li>
			{/foreach}
		</ul>
	</div>
	{$view->element('footer')}
</div>


{$beFront->stats()}
</body>
</html>