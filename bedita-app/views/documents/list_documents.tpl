{*
template incluso.
l'albero delle aree e sezioni per la scleta della porzione di doc. da visualizzare
e l'elenco dei doc. trovato con la toolbar di navigazione.
*}
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
	
		<div id="listAree">
		{$beTree->tree("tree", $tree)}
		</div>
	
		<div id="listDocuments">
		{if $documents}
		<p class="toolbar">
		{t}Documents{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
		</p>
		<table class="indexList">
		<thead>
		<tr>
			<th>{$beToolbar->order('id', 'id')}</th>
			<th>{$beToolbar->order('title', 'Title')}</th>
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('created', 'Created')}</th>
			<th>{$beToolbar->order('lang', 'Language')}</th>
		</tr>
		</thead>
		<tbody>
		{section name="i" loop=$documents}
		<tr class="rowList">
			<td><a href="{$html->url('view/')}{$documents[i].id}">{$documents[i].id}</a></td>
			<td>{$documents[i].title}</td>
			<td>{$documents[i].status}</td>
			<td>{$documents[i].created|date_format:'%b %e, %Y'}</td>
			<td>{$documents[i].lang}</td>
		</tr>				
		{/section}
		</tbody>
		</table>
		<p class="toolbar">
		{t}Documents{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
		</p>
		{else}
		{t}No documents found{/t}
		{/if}
		</div>
		
	</div>
