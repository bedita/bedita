<div id="containerPage">
	<div class="FormPageHeader">
	<h1>{t}Tree of Areas{/t}</h1>
	</div>
	<div id="treecontrol">
		<a href="#">{t}Close all{/t}</a>
		<a href="#">{t}Open all{/t}</a>
	</div>
	<div id="test">
	{formHelper fnc="create" args="'area', array('id' => 'frmTree', 'action' => 'saveTree', 'type' => 'POST')"}
		<input type="hidden" name="URLFrmArea" 		value="{$html->url('viewArea/')}"/>
		<input type="hidden" name="URLFrmSezione" 	value="{$html->url('viewSection/')}"/>
		<input type="hidden" id="data_tree" name="data[tree]" 			value=""/>
		{$beTree->tree("tree", $tree)}
		<br/>
		{formHelper fnc="submit" args="' salva le modifiche ', array('name' => 'modify')"}
	</form>
	</div>
</div>