<div id="containerPage">
	<div id="listAree">
	{$beTree->tree("tree", $tree)}
	</div>

	<div id="listGalleries">
	{if $galleries}
	<p class="toolbar">
		{t}Gallery{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	<table class="indexList">
	<tr>
		<th>{$beToolbar->order('id', 'id')}</th>
		<th>{$beToolbar->order('title', 'Title')}</th>
		<th>{$beToolbar->order('status', 'Status')}</th>
		<th>{$beToolbar->order('created', 'Created')}</th>
		<th>{$beToolbar->order('lang', 'Language')}</th>
		<th>-</th>
	</tr>
	{section name="i" loop=$galleries}
		<tr>
			<td>{$galleries[i].id}</td>
			<td>{$galleries[i].title}</td>
			<td>{$galleries[i].status}</td>
			<td>{$galleries[i].created|date_format:'%b %e, %Y'}</td>
			<td>{$galleries[i].lang}</td>
			<td>
				<input type="submit" id="edit_{$galleries[i].id}" name="edit_{$galleries[i].id}" value="{t}Edit{/t}"/>
				<input type="submit" id="del_{$galleries[i].id}" name="del_{$galleries[i].id}" value="{t}Delete{/t}"/>
			</td>
		</tr>
	{/section}
	</table>
	<p class="toolbar">
		{t}Gallery{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
  	{else}
  	{t}No galleries found{/t}
  	{/if}
	</div>

</div>