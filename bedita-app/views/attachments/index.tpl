{$html->css('module.attachments')}
{$html->css('tree')}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/interface", false)}
{$javascript->link("module.general")}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}



</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

{include file="../common_inc/toolbar.tpl"}



<div class="mainfull">

	{include file="../common_inc/list_streams.tpl" method="index" streamTitle="attachments"}
	
</div>