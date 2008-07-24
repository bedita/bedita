{*<form id="frmTree" method="post" action="{$html->url('/areas/saveTree')}">
	<input type="hidden" name="URLFrmArea" 		value="{$html->url('viewArea/')}"/>
	<input type="hidden" name="URLFrmSezione" 	value="{$html->url('viewSection/')}"/>
	<input type="hidden" id="data_tree" name="data[tree]" 			value=""/>
*}


	<div class="publishingtree" style="width:380px; margin:0px;">
			
		{$beTree->view($tree)}
		
	</div>


{*
</form>
*}


