{*
** bibliography view template
*}

{$html->css("ui.datepicker")}
{$html->css("jquery.autocomplete")}

{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/jquery.autogrow")}
{$javascript->link("jquery/jquery.selectboxes.pack")}

{$javascript->link("jquery/ui/ui.core.min", false)}
{$javascript->link("jquery/ui/ui.sortable.min", false)}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}
{if $currLang != "eng"}
	{$javascript->link("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}
{$javascript->link("jquery/jquery.autocomplete", false)}


<script language="JavaScript">
	{literal}
	$(document).ready( function ()
	{
		$('#personal').show() ;
		$('#address').show() ;
		$('textarea.autogrowarea').css("line-height", "1.2em").autogrow();
		
		// prendiamolo da remoto, facciamo n file php con tutti gli array helpers per gli autocomplete?
		var data = "Sig Sigra Satrap SoS sarallapappa Mr Mrs Dott Prof Ing SA srl Spa sagl etc".split(" ");
		$("#vtitle").autocomplete(data);


	});
	{/literal}
</script>


</head>
<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">
	
	<h1>{t}{$object.title|default:"New Item"}{/t}</h1>
	
</div>

{assign var=objIndex value=0}


{include file="inc/menucommands.tpl" method="view" fixed = true}


<div class="main">	
	
	{include file="inc/form.tpl"}
		
</div>


{include file="../common_inc/menuright.tpl"}





