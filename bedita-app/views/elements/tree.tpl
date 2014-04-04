{$html->css('../js/libs/jquery/plugins/treeview/jquery.treeview', null, ['inline' => false])}
{$html->script('libs/jquery/plugins/treeview/jquery.treeview', false)}

{if isset($treeParams) && is_array($treeParams)}
	{$beTree->setTreeParams($treeParams)}	
{/if}

{if !empty($checkbox)}			
	
	{* this hidden input field has to be empty to empty tree associations when no checkbox selected *}
	<input type='hidden' name='data[destination]' value=''/>
	{$beTree->view($tree, "checkbox", $parents)}
	
{elseif !empty($option)}			
	
	<input type='hidden' name='data[destination]' value=''/>
	{$beTree->option($tree, $parents)}
	
{else}

	{$beTree->view($tree, null, array())}
	
{/if}

{if isset($treeParams)}
	{$beTree->resetTreeParams()}
{/if}