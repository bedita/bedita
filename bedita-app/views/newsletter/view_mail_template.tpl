{*
** subscriber view template
*}

{$html->css("ui.datepicker", null, null, false)}

{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}

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

{assign_associative var="params"  submiturl=""}
{$view->element('form_common_js', $params)}


{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="templates"}

<div class="head">
	
	<h1>{t}{$object.title|default:"New Template"}{/t}</h1>
	
</div>

{include file="inc/menucommands.tpl" method="viewtemplate" fixed = true}

<div class="main">	
	
	{include file="inc/form_template.tpl"}
		
</div>

{$view->element('menuright')}

