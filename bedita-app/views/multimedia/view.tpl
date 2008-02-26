{$html->css('module.multimedia')}
{$html->css("ui.datepicker")}
{if (isset($agent.IE))}{$html->css('jquery.ie.autocomplete')}{else}{$html->css('jquery.autocomplete')}{/if}
{$javascript->link("form")}
{$javascript->link("jquery.treeview")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.delegate")}
{$javascript->link("jquery.metadata")}
{$javascript->link("jquery.validate")}
{$javascript->link("validate.tools")}
{$javascript->link("jquery.autocomplete")}
{$javascript->link("jquery.translatefield")}
{$javascript->link("module.multimedia")}
{$javascript->link("interface")}
{$javascript->link("datepicker/ui.datepicker")}
{if $currLang != "eng"}{$javascript->link("datepicker/ui.datepicker-$currLang.js")}{/if}
<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	$('#properties').show() ;
	$('.showHideBlockButton').bind("click", function(){
		$(this).next("div").toggle() ;
	}) ;
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