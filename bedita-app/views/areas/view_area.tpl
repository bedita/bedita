{*
Pagina con il form per la modifica/aggiunta di un'area.
*}
{php}$vs = &$this->get_template_vars() ;{/php}

	{$html->css('module.area')}
	{$html->css('jquery.autocomplete')}

	{$javascript->link("form")}
	{$javascript->link("jquery.changealert")}
	{$javascript->link("jquery.form")}
	{$javascript->link("jquery.autocomplete")}
	
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

<div id="centralPage">
	
	{include file="submenu.tpl" method="viewArea"}
	
	{include file="form_area.tpl"}
	
</div>
