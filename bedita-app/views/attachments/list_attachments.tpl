<script type="text/javascript">
{literal}
$(document).ready(function(){
	$("TABLE.indexList TR.rowList").click(function(i) { 
		document.location = $("A", this).attr('href') ;
	} );
});
{/literal}
</script>	

	<div id="containerPage">
	
		{if !empty($tree)}
		<div id="listAree">
		{$beTree->tree("tree", $tree)}
		</div>
		{/if}
	
		<div id="listAttachments">
		{if !empty($multimedia)}
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
		{section name="i" loop=$multimedia}
		<tr class="rowList">
			<td><a href="{$html->url('view/')}{$multimedia[i].id}">{$multimedia[i].id}</a></td>
			<td>{$multimedia[i].title}</td>
			<td>{$multimedia[i].status}</td>
			<td>{$multimedia[i].created|date_format:'%b %e, %Y'}</td>
			<td>{$multimedia[i].lang}</td>
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
