{*
Pagina con il form per la modifica/aggiunta di una sezione.
*}
{php}$vs = &$this->get_template_vars() ;{/php}
</head>
<body>

{include file="head.tpl"}

<div id="centralPage">
	
	{include file="submenu.tpl" method="index"}
	
	{include file="form_section.tpl"}
	
</div>
