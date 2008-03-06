{$html->css('module.attachments')}
{$html->css('tree')}
{$javascript->link("jquery.treeview")}
{$javascript->link("interface")}
{$javascript->link("module.general")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}

</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
{include file="submenu.tpl" method="index"}
{include file="list_attachments.tpl" method="index"}
</div>