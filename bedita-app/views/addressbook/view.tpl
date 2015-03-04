{*
** addressbook view template
*}

{assign_associative var="cssOptions" inline=false}
{$html->css("ui.datepicker", null, $cssOptions)}
{$html->css("jquery.autocomplete", null, $cssOptions)}

{$javascript->link("libs/jquery/plugins/jquery.form", false)}
{$javascript->link("libs/jquery/plugins/jquery.selectboxes.pack", false)}

{$html->script("libs/jquery/ui/jquery.ui.sortable.min", true)}
{$html->script("libs/jquery/ui/jquery.ui.datepicker.min", false)}
{if $currLang != "eng"}
	{$html->script("libs/jquery/ui/i18n/jquery.ui.datepicker-$currLang2.min.js", false)}
{/if}
{$html->script("libs/jquery/plugins/jquery.autocomplete", false)}


<script type="text/javascript">
	$(document).ready( function (){
		openAtStart("#card,#address,#properties");
	});
</script>

{$view->element('form_common_js')}

{$view->set('method', $view->action)}

{$view->element('modulesmenu')}

{include file='./inc/menuleft.tpl'}

<div class="head">
	
	<h1>{if !empty($object)}{$object.title|escape|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>
	
</div>

{$objIndex = 0}

{include file="./inc/menucommands.tpl" fixed = true}

<div class="main">	
	
	{include file="./inc/form.tpl"}
		
</div>

{$view->element('menuright')}

