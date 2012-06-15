<div data-role="header" data-position="fixed">
	{if empty($home)}
		<a href="{$html->url('/')}" data-icon="home" class="ui-btn-left">home</a>
	{/if}
	
	<h1>{$publication.public_name|default:$publication.title}</h1>
	
	{if empty($home)}
		<a href="{$html->url($referer)}" data-rel="back" data-back-btn-text="{t}back{/t}" data-icon="arrow-l" data-direction="reverse" class="ui-btn-right">indietro</a>
	{/if}
	
</div><!-- /header -->