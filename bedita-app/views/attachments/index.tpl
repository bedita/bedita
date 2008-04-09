{$html->css('module.attachments')}
{$html->css('tree')}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/interface", false)}
{$javascript->link("module.general")}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}

</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
{include file="submenu.tpl" method="index"}
{include file="list_attachments.tpl" method="index"}
</div>