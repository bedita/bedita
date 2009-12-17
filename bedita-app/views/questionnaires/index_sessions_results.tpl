

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="viewResults"}

{include file="inc/menucommands.tpl" method="viewResults" fixed = true}

{assign_associative var="params" itemName="sessions" title="Sessions of «Titolo del questionario»"}
{$view->element('toolbar', $params)}

<div class="mainfull">
	
	{include file="./inc/list_sessions.tpl"}
	
</div>