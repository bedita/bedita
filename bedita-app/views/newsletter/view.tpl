{*
** document view template
*}

{$html->css("ui.datepicker")}

{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/jquery.selectboxes.pack")}

{$javascript->link("jquery/ui/ui.sortable.min", true)}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}
{if $currLang != "eng"}
	{$javascript->link("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}


<script language="JavaScript">
	{literal}
	$(document).ready( function ()
	{
		var openAtStart ="#contents";
		$(openAtStart).prev(".tab").BEtabstoggle();
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





