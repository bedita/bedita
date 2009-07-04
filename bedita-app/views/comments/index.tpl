{$html->css('tree')}
{$javascript->link("jquery.treeview")}
{$javascript->link("jquery.changealert")}

</head>

<body>

{assign var="p" value=$beToolbar->params}
{assign var="toolbarstring" value=$p.named}


{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

{include file="../common_inc/toolbar.tpl"}

<div class="mainfull">

	{include file="inc/list_objects.tpl" method="index"}
	
</div>

