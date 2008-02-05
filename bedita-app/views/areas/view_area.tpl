{agent var="agent"}
{$html->css('tree')}
{$html->css('module.area')}
{if ($agent.IE)}{$html->css('jquery.ie.autocomplete')}{else}{$html->css('jquery.autocomplete')}{/if}
{$javascript->link("jquery.treeview")}
{$javascript->link("interface")}
{$javascript->link("module.area")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.cmxforms")}
{$javascript->link("jquery.metadata")}
{$javascript->link("jquery.delegate")}
{$javascript->link("jquery.validate")}
{$javascript->link("validate.tools")}
{$javascript->link("jquery.autocomplete")}
{$javascript->link("jquery.translatefield")}

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
{include file="submenu.tpl" method="viewArea"}
{assign var='object' value=$area}
{include file="form_area.tpl"}
<br style="clear:both"/>