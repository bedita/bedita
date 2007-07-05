{*
Pagina d'entrata modulo Areas.
*}
{php}$vs = &$this->get_template_vars() ;{/php}
</head>
<body>

{include file="head.tpl"}

<div id="centralPage">
	
	{include file="submenu.tpl" method="index"}
	
	{include file="form_tree.tpl" method="index"}
	
</div>
