{*
** nesletter view template
*}

{$html->css("ui.datepicker", null, null, false)}
{$html->css("jquery.timepicker.css", null, null, false)}

{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}

{$javascript->link("jquery/ui/jquery.ui.sortable", true)}
{$javascript->link("jquery/ui/jquery.ui.datepicker", false)}
{if $currLang != "eng"}
	{$javascript->link("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}

{$javascript->link("jquery/jquery.placement.below")}
{$javascript->link("jquery/jquery.timepicker-list")}

{$javascript->link("jquery/jquery.validate")}

<script type="text/javascript">
	{literal}
	$(document).ready( function ()
	{
		openAtStart("#contents, #invoice");
		$("#timeStart, #timeEnd").timePicker({startTime: "00:00", endTime: "23:30"});

		$("#updateForm").validate();
		
	});
	{/literal}
</script>

{$view->element('form_common_js')}


{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">
	
	<h1>{if !empty($object)}{$object.title|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>
	
</div>

{assign var=objIndex value=0}

{include file="inc/menucommands.tpl" method="view" fixed = true}

<div class="main">	
	
	{include file="inc/form.tpl"}
		
</div>

{$view->element('menuright')}