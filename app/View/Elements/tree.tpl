{assign_associative var="cssOptions" inline=false}
{$this->Html->css('../js/jquery/treeview/jquery.treeview', null, $cssOptions)}
{$this->Html->script("jquery/treeview/jquery.treeview", false)}

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

{if !empty($checkbox)}			
	
	{* this hidden input field has to be empty to empty tree associations when no checkbox selected *}
	<input type='hidden' name='data[destination]' value=''/>
	{$this->BeTree->view($tree, "checkbox", $parents)}
	
{else}
		
	{$this->BeTree->view($tree)}
	
{/if}