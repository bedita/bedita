{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>{$beFront->title()}</title>
	{$beFront->metaAll()}
	{$beFront->metaDc()}

	<link rel="icon" href="{$html->webroot}favicon.ico" type="image/x-icon" />

	{$beFront->feeds()}
	
	{$html->css('base')}
	
	{if !empty($feedNames)}
	{foreach from=$feedNames item=feed}
		<link rel="alternate" type="application/rss+xml" title="{$feed.title}" href="{$html->url('/rss')}/{$feed.nickname}" />
	{/foreach}
	{/if}

	{$javascript->link('jquery')}

	{$scripts_for_layout}

	<script type="text/javascript">
	{literal}
	$(document).ready(function() {
		$('.open-close-link').click(function(){
				$(this).next('div').toggle();
			}
		);
	}
	);
	{/literal}
	</script>
</head>

<body>

{$content_for_layout}

<div id="footerPage">
</div>

{if empty($conf->staging) && !empty($publication.stats_code)}{$publication.stats_code}{/if}
</body>
</html>