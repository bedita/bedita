{*
** multimedia view template
** @author ChannelWeb srl
*}

{$html->css('ui.tabs')}
{$javascript->link("jquery/ui/ui.tabs", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}
{$javascript->link("validate.tools", false)}
{$javascript->link("jquery/interface", false)}
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

{include file="../modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">
	
	<h1>{t}{$object.title|default:"New Item"}{/t}</h1>

</div>

{include file="inc/menucommands.tpl" method="view" fixed=true}

<div class="main">
	
	{include file="inc/form.tpl"}	

</div>


