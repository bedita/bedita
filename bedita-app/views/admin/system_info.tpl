{$html->css('module.superadmin')}
{$javascript->link("jquery.treeview.pack")}
{$javascript->link("interface")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
	{include file="submenu.tpl" method="systemInfo"}
	{include file="form_info.tpl" method="systemInfo"}
</div>