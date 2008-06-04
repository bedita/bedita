{*
** events view template
** @author ChannelWeb srl
*}

{$html->css('tree')}
{$html->css("ui.datepicker")}
{$html->css('ui.tabs')}
{$javascript->link("jquery/ui/jquery.dimensions", false)}
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

	$('#title').show() ;
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
	
{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">
	
	<h1>{t}{$object.title|default:"New Item"}{/t}</h1>

</div>

<form action="{$html->url('/events/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
{include file="../common_inc/form_common_js.tpl"}
{assign var=objIndex value=0}
<input  type="hidden" name="data[id]" value="{$object.id|default:''}"/>
	
	
{include file="inc/menucommands.tpl" method="view" fixed = true}


<div class="main">
		
{include file="inc/form.tpl"}	

</div>

</form>



{include file="menuright.tpl"}


