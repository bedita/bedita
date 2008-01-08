{*
Pagina d'entrata modulo Attachments.
*}
{php}$vs = &$this->get_template_vars() ;{/php}

{$html->css('module.attachments')}
	
{$javascript->link("jquery.treeview")}
{$javascript->link("interface")}
{$javascript->link("module.multimedia")}
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
	
	
	{include file="list_attachments.tpl" method="index"}
	
</div>
