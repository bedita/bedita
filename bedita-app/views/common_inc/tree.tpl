{$html->css('../js/jquery/treeview/jquery.treeview', false)}
{$javascript->link("jquery/treeview/jquery.treeview", false)}

{literal}
<script type="text/javascript">
$(document).ready(function(){
	// third example
	$(".menutree").treeview({
		animated: "slow",
		collapsed: true,
		unique: true,
		persist: "cookie"
	});

});
</script>
{/literal}

{if !empty($checkbox)}			
	
	{$beTree->view($tree, "checkbox", $parents)}
	
{else}
		
	{$beTree->view($tree)}
	
{/if}	