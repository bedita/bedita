{$html->css("ui.datepicker")}

{$javascript->link("jquery/jquery.form")}
{$javascript->link("jquery/jquery.treeview")}

{$javascript->link("jquery/ui/datepicker/ui.datepicker")}
{if $currLang != "eng"}
	{$javascript->link("jquery/ui/datepicker/ui.datepicker-$currLang.js")}
{/if}


</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl"}

<div class="head">
	
	<h1>{t}{$object.title|default:"New Item"}{/t}</h1>

</div>

{assign var=objIndex value=0}


{include file="inc/menucommands.tpl" fixed=true method="view"}

<div class="main">	
	
	{include file="inc/form.tpl"}
		
</div>


{include file="../common_inc/menuright.tpl"}





