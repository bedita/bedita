{agent var="agent"}
{$html->css('tree')}
{$html->css('module.area')}
{if ($agent.IE)}{$html->css('jquery.ie.autocomplete')}{else}{$html->css('jquery.autocomplete')}{/if}
{$html->css('ui.tabs')}
{$javascript->link("ui/jquery.dimensions")}
{$javascript->link("ui/ui.tabs")}
{$javascript->link("jquery.treeview")}
{$javascript->link("interface")}
{$javascript->link("module.area")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.selectboxes.pack")}
{$javascript->link("jquery.cmxforms")}
{$javascript->link("jquery.metadata")}
{$javascript->link("jquery.delegate")}
{$javascript->link("jquery.validate")}
{$javascript->link("validate.tools")}
{$javascript->link("jquery.autocomplete")}

<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	$('#properties').show() ;
	$('.showHideBlockButton').bind("click", function(){ $(this).next("div").toggle() ; }) ;
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