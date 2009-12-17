

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="newsletters"}

{include file="inc/menucommands.tpl" method="newsletters" fixed=true}

{$view->element('toolbar')}

<div class="mainfull">

	{include file="inc/list_objects_newsletter.tpl" method="index"}
	
</div>