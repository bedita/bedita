{$html->script("form", false)}
{$html->script("libs/jquery/plugins/jquery.form", false)}
{$html->script("libs/jquery/plugins/jquery.metadata", false)}

{$view->element('modulesmenu', ['searchDestination' => $view->action, 'substringSearch' => false])}

{include file="inc/menuleft.tpl" method="groups"}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
	{$toolbarOptions = ['headerName' => 'System groups', 'name' => 'Group', 'newAction' => 'viewGroup']}
	{include file="./inc/toolbar.tpl" toolbarOptions=$toolbarOptions}
	</div>
</div>


{include file="inc/menucommands.tpl" method="groups" fixed=true}


<div class="main">

	{include file="inc/index_groups.tpl"}

</div>


{$view->element('menuright')}