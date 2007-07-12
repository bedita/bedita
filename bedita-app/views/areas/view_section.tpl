{*
Pagina con il form per la modifica/aggiunta di una sezione.
*}
{php}$vs = &$this->get_template_vars() ;{/php}

	{$html->css('module.area')}

	{$javascript->link("form")}

</head>
<body>

{include file="head.tpl"}

<div id="centralPage">
	
	{include file="submenu.tpl" method="viewSection"}
	
	{include file="form_section.tpl"}
	
</div>
