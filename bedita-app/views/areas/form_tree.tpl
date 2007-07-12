{*
template incluso.
Visualizza il form per la gestione dell'albero aree/sezioni.
*}
{php}
$vs = &$this->get_template_vars() ;
{/php}

	<div id="containerPage">
	
		<div class="FormPageHeader">
		<h1>albero delle aree</h1>
		</div>
		<div id="treecontrol">
			<a href="#">Chiudi tutti</a>
			<a href="#">Espandi tutto</a>
		</div>
	
		<div>
		{formHelper fnc="create" args="'frmTree', array('action' => '/areas/saveTree', 'type' => 'POST')"}
				<input type="hidden" name="URLFrmArea" 		value="{$html->url('viewArea/')}">
				<input type="hidden" name="URLFrmSezione" 	value="{$html->url('viewSection/')}">
			
			{$beTree->tree("tree", $tree)}
			<br />
			<p align="center">
			{formHelper fnc="submit" args="' salva le modifiche ', array('name' => 'modify')"}
			</p>
		</form>
			
		</div>
	
	</div>
