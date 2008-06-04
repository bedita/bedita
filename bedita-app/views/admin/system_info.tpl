{$html->css('module.superadmin')}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}
</head>



<body>

{include file="modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="systemInfo"}

{include file="inc/menucommands.tpl" method="systemInfo" fixed=true}

<div class="head">
	
	<h2>{t}System info{/t}</h2>

</div>

<div class="main">
	
	{include file="inc/form_info.tpl" method="systemInfo"}

</div>