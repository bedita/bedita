{*
** multimedia view template
** @author ChannelWeb srl
*}

{$html->css('ui.tabs')}
{$javascript->link("ui/ui.tabs")}
{$javascript->link("form")}
{$javascript->link("jquery.treeview")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.selectboxes.pack")}
{$javascript->link("jquery.metadata")}
{$javascript->link("jquery.validate")}
{$javascript->link("validate.tools")}
{$javascript->link("interface")}
<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	$('#title').show() ;
	$('#multimediaitem').show() ;
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