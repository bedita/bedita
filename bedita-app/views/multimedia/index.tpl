{$html->css('tree')}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}

</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
{include file="submenu.tpl" method="index"}
{include file="../pages/list_streams.tpl" method="index" streamTitle="multimedia"}
</div>
