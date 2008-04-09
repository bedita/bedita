{$html->css('module.galleries')}
{$html->css('tree')}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("module.galleries", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}

</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
{include file="submenu.tpl" method="index"}
{include file="../pages/list_objects.tpl" method="index" assocToSections=false}
</div>