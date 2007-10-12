{*
template incluso.
l'albero delle aree e sezioni per la scleta della porzione di doc. da visualizzare
e l'elenco dei doc. trovato con la toolbar di navigazione.
*}
	<div id="containerPage">
	
		<div id="listAree">
		{$beTree->tree("tree", $tree)}
		</div>
	
		<div id="listDocuments">

		</div>
		<pre>{dump var=$documents}</pre>
		
		<p>
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()}
		</p>
	</div>
