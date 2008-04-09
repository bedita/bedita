{*
** galleries view template
** @author ChannelWeb srl
*}

{$html->css('tree')}
{$html->css('ui.tabs')}
{$javascript->link("jquery/ui/jquery.dimensions")}
{$javascript->link("jquery/ui/ui.tabs")}
{$javascript->link("jquery/jquery.autogrow")}
{$javascript->link("form")}
{$javascript->link("jquery/jquery.treeview")}
{$javascript->link("jquery/jquery.changealert")}
{$javascript->link("jquery/jquery.form")}
{$javascript->link("jquery/jquery.selectboxes.pack")}
{$javascript->link("jquery/jquery.cmxforms")}
{$javascript->link("jquery/jquery.metadata")}
{$javascript->link("jquery/jquery.validate")}
{$javascript->link("validate.tools")}
{$javascript->link("jquery/interface")}

<script type="text/javascript">
<!--

{literal}

$(document).ready(function(){

	$('#multimedia').show() ;

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