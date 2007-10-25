<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	$("TABLE.indexList TR.rowList").click(function(i) {
		document.location = $("A", this).attr('href') ;
	} );
});
{/literal}
//-->
</script>

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
	<table class="indexList" cellpadding="0" cellspacing="0" style="width:578px">
	<thead>
	<tr>
		<th>{$beToolbar->order('id', 'id')}</th>
		<th>{$beToolbar->order('title', 'Title')}</th>
		<th>{$beToolbar->order('status', 'Status')}</th>
		<th>{$beToolbar->order('created', 'Created')}</th>
		<th>{$beToolbar->order('lang', 'Language')}</th>
		<th>-</th>
	</tr>
	</thead>
	<tbody>
	{section name="i" loop=$galleries}
	<tr class="rowList">
		<td><a href="{$html->url('view/')}{$galleries[i].id}">{$galleries[i].id}</a></td>
		<td>{$galleries[i].title}</td>
		<td>{$galleries[i].status}</td>
		<td>{$galleries[i].created|date_format:$conf->date_format}</td>
		<td>{$galleries[i].lang}</td>
		<td><a href="{$html->url('delete/')}{$galleries[i].id}">{t}Delete{/t}</a></td>
	</tr>
	{/section}
	</tbody>
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