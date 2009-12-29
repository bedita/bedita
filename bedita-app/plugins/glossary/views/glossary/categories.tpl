{$javascript->link("jquery/jquery.changealert", false)}
	
{$view->element("modulesmenu")}

{assign_associative var="params" method="categories"}
{$view->element("menuleft", $params)}


<div class="head">
	
	<h1>{t}Categories{/t}</h1>

</div>

{assign_associative var="params" method="index"}
{$view->element("menucommands", $params)}

<div class="main">
{$view->element("list_categories")}
</div>


