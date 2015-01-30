{*
** subscriber view template
*}

{$html->css("ui.datepicker", null, ['inline' => false])}

{$html->script("libs/jquery/plugins/jquery.form", false)}
{$html->script("libs/jquery/plugins/jquery.selectboxes.pack", false)}

{$html->script("libs/jquery/ui/jquery.ui.sortable.min", false)}
{$html->script("libs/jquery/ui/jquery.ui.datepicker.min", false)}
{if $currLang != "eng"}
	{$html->script("libs/jquery/ui/i18n/jquery.ui.datepicker-$currLang2.min.js", false)}
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
	
	<h1>{t}{$object.title|escape|default:"New Template"}{/t}</h1>
	
</div>

{include file="inc/menucommands.tpl" method="viewtemplate" fixed = true}

<div class="main">	
	
	{include file="inc/form_template.tpl"}
		
</div>

{$view->element('menuright')}