{*
template incluso.
Visualizza il form per la gestione dell'albero aree/sezioni.
*}
{php}$vs = &$this->get_template_vars() ;{/php}

	<div id="containerPage">
	
		<h3>Albero delle aree</h3>
			
		<div id="treecontrol">
			<a href="#">Chiudi tutti</a>
			<a href="#">Espandi tutto</a>
		</div>
	
		<div>
			<form action="" method="POST">
			{$beTree->tree("tree", $tree)}
			<br />
			<p align="center">
			<input type="submit" name="modify" value=" salva le modifiche ">
			</p>
			</form>
			
		</div>
	
	</div>
