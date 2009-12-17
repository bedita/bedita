
{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="indexQuestions"}

{include file="inc/menucommands.tpl" method="indexQuestions" fixed = false}

{assign_associative var="params" itemName="questions"}
{$view->element('toolbar', $params)}

<div class="mainfull">
	
	{include file="./inc/list_all_questions.tpl"}

</div>