{strip}

{$view->element('header')}

<div class="main">
	
	<div class="content-main sitemap">
	{assign var="public_url" value=""}
	
	{$beTree->sitemap($sections_tree,$public_url)}
	
	</div>

</div>

{$view->element('footer')}

{/strip}