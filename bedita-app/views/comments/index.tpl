{$html->css('tree', null, null, false)}
{$javascript->link("jquery.treeview", false)}
{$javascript->link("jquery.changealert", false)}


{assign var="p" value=$beToolbar->params}
{assign var="toolbarstring" value=$p.named}


{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

{$view->element('toolbar')}

<div class="mainfull">

	{include file="inc/list_objects.tpl"}
	
</div>

