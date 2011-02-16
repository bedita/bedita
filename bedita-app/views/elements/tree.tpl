{assign_associative var="cssOptions" inline=false}
{$html->css('../js/jquery/treeview/jquery.treeview', null, $cssOptions)}
{$html->script("jquery/treeview/jquery.treeview", false)}

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