{*
** addressbook view template
*}

{$html->css("ui.datepicker", null, null, false)}
{$html->css("jquery.autocomplete", null, null, false)}

{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}

{$javascript->link("jquery/ui/jquery.ui.sortable", true)}
{$javascript->link("jquery/ui/jquery.ui.datepicker", false)}
{if $currLang != "eng"}
	{$javascript->link("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}
{$javascript->link("jquery/jquery.autocomplete", false)}


<script type="text/javascript">
	{literal}
	$(document).ready( function (){
	
		openAtStart("#card,#address,#properties");
		
		$('textarea.autogrowarea').css("line-height", "1.2em").autogrow();
		
		// prendiamolo da remoto, facciamo n file php con tutti gli array helpers per gli autocomplete?
		var data = "Sig Sigra Satrap SoS sarallapappa Mr Mrs Dott Prof Ing SA srl Spa sagl etc".split(" ");
		$("#vtitle").autocomplete(data);

	});
	{/literal}
</script>

{$view->element('form_common_js')}

{$view->set('method', $view->action)}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

<div class="head">
	
	<h1>{if !empty($object)}{$object.title|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>
	
</div>

{assign var=objIndex value=0}

{include file="inc/menucommands.tpl" fixed = true}

<div class="main">	
	
	{include file="inc/form.tpl"}
		
</div>

{$view->element('menuright')}



