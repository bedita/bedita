{*
Pagina d'entrata modulo Documents.
*}
{php}$vs = &$this->get_template_vars() ;{/php}

{$html->css('module.multimedia')}
	
{$javascript->link("jquery.treeview.pack")}
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
	
	
	{include file="list_multimedia.tpl" method="index"}
	
</div>
