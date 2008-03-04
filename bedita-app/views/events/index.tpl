{$html->css('module.events')}
{$html->css('tree')}
{$javascript->link("jquery.treeview")}
{$javascript->link("interface")}
{$javascript->link("module.events")}
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
{include file="list_events.tpl" method="index"}
</div>