{*
** subscriber view template
*}

{assign_associative var="cssOptions" inline=false}
{$html->css("ui.datepicker", null, $cssOptions)}

{$html->script("jquery/jquery.form", false)}
{$html->script("jquery/jquery.selectboxes.pack", false)}

{$html->script("jquery/ui/ui.sortable.min", false)}
{$html->script("jquery/ui/jquery.ui.datepicker", false)}
{if $currLang != "eng"}
	{$html->script("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
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

