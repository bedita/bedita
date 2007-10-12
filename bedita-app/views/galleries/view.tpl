{$html->css('module.gallery.css')}
{$javascript->link("jquery.treeview.pack")}
{$javascript->link("interface")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
	{include file="submenu.tpl" method="index"}
	{include file="list_galleries.tpl" method="index"}
</div>