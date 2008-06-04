{$html->css('tree')}
{$javascript->link("jquery.treeview")}
{$javascript->link("interface")}
{$javascript->link("jquery.changealert")}

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

{include file="../common_inc/toolbar.tpl"}



<div class="mainfull">

	{include file="../pages/list_objects.tpl" method="index" areasectiontree=$areasectiontree}
	
</div>


