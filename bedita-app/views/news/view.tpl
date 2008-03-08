{$html->css('tree')}
{$html->css("ui.datepicker")}
{$html->css('ui.tabs')}
{$javascript->link("ui/jquery.dimensions")}
{$javascript->link("ui/ui.tabs")}
{$javascript->link("form")}
{$javascript->link("jquery.treeview")}
{$javascript->link("jquery.changealert")}
{$javascript->link("jquery.form")}
{$javascript->link("jquery.selectboxes.pack")}
{$javascript->link("jquery.cmxforms")}
{$javascript->link("jquery.metadata")}
{$javascript->link("jquery.validate")}
{$javascript->link("validate.tools")}
{$javascript->link("interface")}
{$javascript->link("ui/datepicker/ui.datepicker")}
{if $currLang != "eng"}
	{$javascript->link("ui/datepicker/ui.datepicker-$currLang.js")}
{/if}

<script type="text/javascript">
<!--

{* Avoid Javascript errors when a document have no 'parents' *}
{if is_array($parents) && count($parents) > 1}
	var parents = new Array({section name=i loop=$parents}{$parents[i]}{if !($smarty.section.i.last)},{/if}{/section}) ;

{elseif is_array($parents) && count($parents) == 1}
	var parents = new Array() ;
	parents[0] = {$parents[0]} ;
{else}
	var parents = new Array() ;
{/if}

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