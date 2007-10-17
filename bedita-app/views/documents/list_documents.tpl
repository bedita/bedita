{*
template incluso.
l'albero delle aree e sezioni per la scleta della porzione di doc. da visualizzare
e l'elenco dei doc. trovato con la toolbar di navigazione.
*}
	<div id="containerPage">
	
		<div id="listAree">
		{$beTree->tree("tree", $tree)}
		</div>
	
		<div id="listDocuments" style="">
		<p class="toolbar">
		{t}Documenti{/t}: {$beToolbar->size()} | {t}pagina{/t} {$beToolbar->current()} {t}di{/t} {$beToolbar->pages()} &nbsp;
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
				{section name="i" loop=$documents}
					<tr>
						<td>{$documents[i].id}</td>
						<td>{$documents[i].title}</td>
						<td>{$documents[i].status}</td>
						<td>{$documents[i].created|date_format:'%b %e, %Y'}</td>
						<td>{$documents[i].lang}</td>
					</tr>				
				{/section}
			</table>
		
		<p class="toolbar">
		{t}Documenti{/t}: {$beToolbar->size()} | {t}pagina{/t} {$beToolbar->current()} {t}di{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp; 
		{t}Dimensioni{/t}: {$beToolbar->changeDim()} &nbsp;
		{t}Vai alla pagina{/t}: {$beToolbar->changePage()}
		</p>
		</div>
		
	</div>
