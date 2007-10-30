{$html->css('module.superadmin')}
{$javascript->link("jquery.treeview.pack")}
{$javascript->link("interface")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
{$javascript->link("module.admin")}
</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
	{include file="submenu.tpl" method="index"}
	{include file="list_users.tpl" method="index"}
</div>