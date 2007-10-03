{*
Pagina con il form per la modifica/aggiunta di un'area.
*}
{php}$vs = &$this->get_template_vars() ;{/php}
{agent var="agent"}
	{$html->css('module.area')}
	{if ($agent.IE)}
		{$html->css('jquery.ie.autocomplete')}
	{else}
		{$html->css('jquery.autocomplete')}
	{/if}

	{$javascript->link("form")}
	{$javascript->link("jquery.changealert")}
	{$javascript->link("jquery.form")}
	{$javascript->link("jquery.validate")}
	{$javascript->link("jquery.autocomplete")}
	{$javascript->link("jquery.translatefield")}
	
<script type="text/javascript">
{literal}

$(document).ready(function(){

	$('#proprieta').show() ;
			
	// aggiunge i comandi per i blocchi
	$('.showHideBlockButton').bind("click", function(){
		$(this).next("div").toggle() ;
	}) ;

	// handler cambiamenti dati della pagina
	$("#handlerChangeAlert").changeAlert($('input, textarea, select').not($("#addCustomPropTR TD/input, #addCustomPropTR TD/select, #addPermUserTR TD/input, #addPermGroupTR TD/input"))) ;
	$('.gest_menux, #menuLeftPage a, #headerPage a, #buttonLogout a, #headerPage div').alertUnload() ;
	
});

{/literal}
</script>	

</head>
<body>

{include file="head.tpl"}

<!-- br style="clear:both;"-->
	{include file="submenu.tpl" method="viewArea"}
	
	{include file="form_area.tpl"}
	
<br style="clear:both">
