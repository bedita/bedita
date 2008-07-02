{*
** news view template
** @author ChannelWeb srl
*}

{$html->css('tree')}
{$html->css("ui.datepicker")}
{$html->css('ui.tabs')}
{$javascript->link("jquery/ui/ui.tabs", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}
{$javascript->link("validate.tools", false)}
{$javascript->link("jquery/interface", false)}
{$javascript->link("jquery/ui/datepicker/ui.datepicker", false)}
{if $currLang != "eng"}
	{$javascript->link("jquery/ui/datepicker/ui.datepicker-$currLang.js", false)}
{/if}

<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){

	$('#properties').show() ;
	$('#extendedtext').show() ;
	$('#attachments').show() ;
	
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