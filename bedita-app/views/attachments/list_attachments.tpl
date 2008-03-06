<script type="text/javascript">
var URLBase = "{$html->url('index/')}" ;
{literal}
$(document).ready(function(){

	$("#tree").designTree(URLBase) ;

	$("TABLE.indexList TR.rowList").click(function(i) { 
		document.location = $("A", this).attr('href') ;
	} );
});
{/literal}
</script>	

	<div id="containerPage">
	
		{if !empty($tree)}
		<div id="listAreas">
		{$beTree->tree("tree", $tree)}
		</div>
		{/if}
	
		<div id="listElements">
		{if !empty($objects)}
		<p class="toolbar">
			{t}Attachments{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
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
		</tr>
		{section name="i" loop=$objects}
		<tr class="rowList">
			<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].id}</a></td>
			<td>{$objects[i].title}</td>
			<td>{$objects[i].status}</td>
			<td>{$objects[i].created|date_format:'%b %e, %Y'}</td>
			<td>{$objects[i].lang}</td>
		</tr>				
		{/section}
		</table>
		<p class="toolbar">
			{t}Attachments{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
			{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
			{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
			{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
		</p>
		{else}
			{t}No attachments item found{/t}
		{/if}
		</div>
		
	</div>
