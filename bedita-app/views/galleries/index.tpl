{$html->css('module.galleries')}
{$html->css('tree')}

{$javascript->link("jquery.treeview")}
{$javascript->link("module.galleries")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
<script type="text/javascript">
<!--
var URLBase = "{$html->url('index/')}" ;
//-->
</script>
</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
{include file="submenu.tpl" method="index"}
{include file="list_galleries.tpl" method="index"}
</div>