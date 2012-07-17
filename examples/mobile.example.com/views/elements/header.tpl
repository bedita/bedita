<div data-role="header" data-position="fixed" data-id="persistent-bar">

	{if empty($home)}
		<a href="{$html->url('/')}" data-icon="home" class="ui-btn-left">home</a>
	{/if}
	
	<h1><a href="{$html->url('/')}">{$publication.public_name|default:$publication.title}</a></h1>
	
	{if empty($home)}
		<a href="{$html->url($referer)}" data-rel="back" data-back-btn-text="{t}back{/t}" data-icon="arrow-l" data-direction="reverse" class="ui-btn-right">indietro</a>
	{/if}
	
</div><!-- /header -->