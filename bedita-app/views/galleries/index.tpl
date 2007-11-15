{$html->css('module.galleries')}
{$javascript->link("jquery.treeview.pack")}
{$javascript->link("module.galleries")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
<script type="text/javascript">
var URLBase = "{$html->url('index/')}" ;
{literal}
$(document).ready(function(){
	designTree() ;
});
{/literal}
</script>
</head>
<body>
{include file="head.tpl"}
<div id="centralPage">
{include file="submenu.tpl" method="index"}
{include file="list_galleries.tpl" method="index"}
</div>