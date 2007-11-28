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
	
		<div id="listMultimedia" style="">
		<p class="toolbar">
		{t}Multimedia{/t}: {$beToolbar->size()} | {t}pagina{/t} {$beToolbar->current()} {t}di{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp; 
		{t}Dimensioni{/t}: {$beToolbar->changeDim()} &nbsp;
		{t}Vai alla pagina{/t}: {$beToolbar->changePage()}
		</p>
			<table class="indexList">
				<tr>
					<th>{$beToolbar->order('id', 'id')}</th>
					<th>{$beToolbar->order('title', 'titolo')}</th>
					<th>{$beToolbar->order('status', 'status')}</th>
					<th>{$beToolbar->order('created', 'creato il')}</th>
					<th>{$beToolbar->order('lang', 'lingua')}</th>
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
		{t}Multimedia{/t}: {$beToolbar->size()} | {t}pagina{/t} {$beToolbar->current()} {t}di{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp; 
		{t}Dimensioni{/t}: {$beToolbar->changeDim()} &nbsp;
		{t}Vai alla pagina{/t}: {$beToolbar->changePage()}
		</p>
		</div>
		
	</div>
