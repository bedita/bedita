{$view->element('modulesmenu')}

{include file='./inc/menuleft.tpl' method='categories'}

<div class="head">
	
	<h1>{t}Categories{/t}</h1>

</div>

{include file='./inc/menucommands.tpl' method='categories'}


<div class="mainfull">

{$view->element('list_categories', ['method' => 'categories'])}

</div>


