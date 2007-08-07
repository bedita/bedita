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
	
		<div id="test">
		{formHelper fnc="create" args="'area', array('id' => 'frmTree', 'action' => 'saveTree', 'type' => 'POST')"}
				<input type="hidden" name="URLFrmArea" 		value="{$html->url('viewArea/')}">
				<input type="hidden" name="URLFrmSezione" 	value="{$html->url('viewSection/')}">
				<input type="hidden" id="data_tree" name="data[tree]" 			value="">
				
			{$beTree->tree("tree", $tree)}
			<br />
			<p align="center">
			{formHelper fnc="submit" args="' salva le modifiche ', array('name' => 'modify')"}
			</p>
		</form>
<textarea id="debug" cols="120" rows="60"></textarea>
		</div>
	
	</div>
