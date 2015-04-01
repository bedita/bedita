{$view->element('modulesmenu', ['substringSearch' => false])}

{include file = './inc/menuleft.tpl'}

{include file = './inc/menucommands.tpl'}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
		{include file="./inc/toolbar.tpl" label_items='users'}
	</div>
</div>

<div class="mainfull">

	{include file = './inc/list_users.tpl' method = 'index'}
	
</div>