{*
** subscriber view template
*}

{assign_associative var="cssOptions" inline=false}
{$this->Html->css("ui.datepicker", null, $cssOptions)}

{$this->Html->script("jquery/jquery.form", false)}
{$this->Html->script("jquery/jquery.selectboxes.pack", false)}

{$this->Html->script("jquery/ui/ui.sortable.min", false)}
{$this->Html->script("jquery/ui/jquery.ui.datepicker", false)}
{if $currLang != "eng"}
	{$this->Html->script("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}

<script type="text/javascript">
	$(document).ready( function (){
		openAtStart("#details");
	});
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