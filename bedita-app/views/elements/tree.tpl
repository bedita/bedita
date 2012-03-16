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
	
	{* this hidden input field has to be empty to empty tree associations when no checkbox selected *}
	<input type='hidden' name='data[destination]' value=''/>
	{$beTree->view($tree, "checkbox", $parents)}
	
{else}
		
	{$beTree->view($tree)}
	
{/if}	