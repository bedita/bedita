{$html->css('../js/jquery/treeview/jquery.treeview', false)}
{$javascript->link("jquery/treeview/jquery.treeview", false)}

{literal}
<script type="text/javascript">
$(document).ready(function(){
	// third example
	$(".menutree").treeview({
		animated: "normal",
		collapsed: true,
		unique: false,
		persist: "cookie"
	});

$(".menutree input:checked").parent().css("background-color","#dedede").parents("ul, li").show();


});
</script>
{/literal}

{if !empty($checkbox)}			
	
	{$beTree->view($tree, "checkbox", $parents)}
	
{else}
		
	{$beTree->view($tree)}
	
{/if}	