

{$view->element('modulesmenu')}

{include file = './inc/menuleft.tpl' method = 'index'}

{include file = './inc/menucommands.tpl' method = 'index'}

{$view->element('toolbar')}



<div class="mainfull">

    {$view->element('filters')}

	{$view->element('list_objects')}
	

</div>

