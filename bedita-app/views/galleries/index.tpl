{$html->css('module.galleries')}
{$html->css('tree')}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("module.galleries", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}

</head>

<body>

{include file="modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

{include file="../toolbar.tpl"}

<div class="mainfull">

	{include file="../pages/list_objects.tpl" method="index" assocToSections=false}

</div>


