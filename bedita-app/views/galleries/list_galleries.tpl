<div id="containerPage">
	<h2>{t}Areas{/t}</h2>
	<div id="listAree">
	{$beTree->tree("tree", $tree)}
	</div>

	<h2>{t}Galleries{/t}</h2>
	<div id="listGalleries">
	{if $galleries}
	{foreach from=$galleries item=g}
	<li>{$g.Gallery.id}: {$g.Gallery.name}</li>
  	{/foreach}
  	{else}
  	{t}No galleries found{/t}
  	{/if}
	</div>

	<p>
	{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()}
	</p>
</div>