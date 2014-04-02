{assign_associative var="cssOptions" inline=false}
{$html->css('../js/jquery/treeview/jquery.treeview', null, $cssOptions)}
{$html->script("jquery/treeview/jquery.treeview", false)}

{if isset($treeParams) && is_array($treeParams)}
	{$beTree->setTreeParams($treeParams)}	
{/if}

{if !empty($checkbox)}			
	
	{* this hidden input field has to be empty to empty tree associations when no checkbox selected *}
	<input type='hidden' name='data[destination]' value=''/>
	{$beTree->view($tree, "checkbox", $parents)}
	
{else}

	{$beTree->view($tree, null, array())}
	
{/if}

{if isset($treeParams)}
	{$beTree->resetTreeParams()}
{/if}