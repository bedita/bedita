
{$view->set('method', $view->action)}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

{assign_associative var="params" itemName="cards"}
{$view->element('toolbar', $params)}



<div class="mainfull">

	{include file="inc/list_objects.tpl"}
	

</div>

