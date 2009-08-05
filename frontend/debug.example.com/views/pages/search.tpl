<div>{$view->element('form_search')}</div>
<div>
	{$beToolbar->init($searchResult.toolbar)}
	{$beToolbar->first()}&nbsp;&nbsp; 
	{$beToolbar->prev()}&nbsp;&nbsp;
	
	{$beToolbar->current()} / {$beToolbar->pages()}&nbsp;&nbsp;
	
	{$beToolbar->next()}&nbsp;&nbsp;
	{$beToolbar->last()}&nbsp;&nbsp;
	
	<ul>
	{foreach from=$searchResult.items item="object"}
		<li><a href="{$html->url('/')}{$object.nickname}">{$object.title}</a></li>
	{/foreach}
	</ul>	
</div>	



