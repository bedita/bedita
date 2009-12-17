{*
** document view template
*}
{$html->css("ui.datepicker", null, null, false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}
{$javascript->link("jquery/ui/ui.sortable.min", true)}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}
{if $currLang != "eng"}
{$javascript->link("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}
{literal}
<script type="text/javascript">
    $(document).ready(function(){
		
		openAtStart("#question");
		
    });
</script>
{/literal}

{$view->element('form_common_js')}

	
    {$view->element('modulesmenu')}
    
	{include file="inc/menuleft.tpl" method="viewQuestion"}
    
	<div class="head">
		
        <h1>{if !empty($object)}{$object.title|default:"<i>[no title]</i>"}{else}<i>[{t}New question{/t}]</i>{/if}</h1>
    </div>
    
	{assign var=objIndex value=0}
    
	{include file="inc/menucommands.tpl" method="viewQuestion" fixed = true}

	<div class="main">
		 {include file="inc/form_question.tpl"}
    </div>
    
	{$view->element('menuright')}