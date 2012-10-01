{$this->Html->script("jquery/jquery.changealert", false)}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="categories"}

<div class="head">
	
	<h1>{t}Categories{/t}</h1>

</div>

{include file="inc/menucommands.tpl" method="categories"}


<div class="mainfull">

{assign_associative var="params" method="categories"}
{$view->element('list_categories', $params)}

</div>


