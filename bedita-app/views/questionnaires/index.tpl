
{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

{$view->element('toolbar')}

<div class="mainfull">
	
	{include file="./inc/list_objects.tpl" method="index"}
	
</div>