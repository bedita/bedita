<div id="containerPage">
	<div class="FormPageHeader">
	<h1>{t}Tree of Areas{/t}</h1>
	</div>
	<div id="treecontrol">
		<a href="#">{t}Close all{/t}</a>
		<a href="#">{t}Expand all{/t}</a>
	</div>
	<div id="test">
	<form id="frmTree" method="post" action="{$html->url('/areas/saveTree')}">
		<input type="hidden" name="URLFrmArea" 		value="{$html->url('viewArea/')}"/>
		<input type="hidden" name="URLFrmSezione" 	value="{$html->url('viewSection/')}"/>
		<input type="hidden" id="data_tree" name="data[tree]" 			value=""/>
		{$beTree->tree("tree", $tree)}
		
		<br/>
		<input type="submit" value="{t}Save{/t}" name="modify"/>
	</form>
	</div>
</div>