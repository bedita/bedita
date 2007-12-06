{*
Pagina entrata modifica/creazioen multimedia.
*}
{php}$vs = &$this->get_template_vars() ;{/php}

{agent var="agent"}

{$html->css('module.multimedia')}
{$html->css("jquery.calendar")}
{if ($agent.IE)} 	{$html->css('jquery.ie.autocomplete')}
{else}				{$html->css('jquery.autocomplete')}
{/if}

{$javascript->link("form")}
{$javascript->link("jquery.treeview")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.validate")}
{$javascript->link("jquery.autocomplete")}
{$javascript->link("jquery.translatefield")}
{$javascript->link("module.multimedia")}
{$javascript->link("interface")}
{$javascript->link("jquery.calendar")}


<script type="text/javascript">
<!--

{literal}

$(document).ready(function(){

	$('#properties').show() ;

	// aggiunge i comandi per i blocchi
	$('.showHideBlockButton').bind("click", function(){
		$(this).next("div").toggle() ;
	}) ;

	// handler cambiamenti dati della pagina
	$("#handlerChangeAlert").changeAlert($('input, textarea, select').not($("#addCustomPropTR TD/input, #addCustomPropTR TD/select, #addPermUserTR TD/input, #addPermGroupTR TD/input"))) ;
	$('.gest_menux, #menuLeftPage a, #headerPage a, #buttonLogout a, #headerPage div').alertUnload() ;

});


{/literal}
//-->
</script>

</head>
<body>

{include file="head.tpl"}

<div id="centralPage">
	
	{include file="submenu.tpl" method="index"}
	
	{include file="form.tpl"}	
</div>


