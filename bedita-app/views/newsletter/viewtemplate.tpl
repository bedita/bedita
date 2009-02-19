{*
** subscriber view template
*}

{$html->css("ui.datepicker")}

{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.selectboxes.pack")}

{$javascript->link("jquery/ui/ui.sortable.min", false)}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}
{if $currLang != "eng"}
	{$javascript->link("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}


<script type="text/javascript">
	{literal}
	$(document).ready( function (){
		openAtStart("#details");
	});
	{/literal}
</script>

{include file="../common_inc/form_common_js.tpl" submiturl=""}

</head>
<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="templates"}

<div class="head">
	
	<h1>{t}{$object.title|default:"New Template"}{/t}</h1>
	
</div>

{include file="inc/menucommands.tpl" method="viewtemplate" fixed = true}

<form action="{$html->url('/newsletter/saveTemplate')}" method="post" name="updateForm" id="updateForm" class="">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

<div class="main">	
	
	{include file="inc/form_template.tpl"}
		
</div>

{include file="../common_inc/menuright.tpl"}

</form>



