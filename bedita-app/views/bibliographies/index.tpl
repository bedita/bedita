
{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}

{$view->element('toolbar')}



<div class="mainfull">

	{assign_associative var="params" method="index"}
	{$view->element('list_objects',$params)}
	

</div>

