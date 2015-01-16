{*
** document view template
*}
{$html->css("ui.datepicker", null, ['inline' => false])}
{$html->script("libs/jquery/plugins/jquery.form", false)}
{$html->script("libs/jquery/plugins/jquery.selectboxes.pack",false)}
{$html->script("libs/jquery/ui/jquery.ui.sortable.min", true)}
{$html->script("libs/jquery/ui/jquery.ui.datepicker.min", false)}
{if $currLang != "eng"}
    {$html->script("libs/jquery/ui/i18n/jquery.ui.datepicker-$currLang2.min.js", false)}
{/if}
<script type="text/javascript">
    $(document).ready(function(){
		openAtStart("#title");
    });
</script>

{$view->element('form_common_js')}

	{$view->element('modulesmenu')}
    
	{include file="inc/menuleft.tpl" method="view"}
    
	<div class="head">
        <h1>{if !empty($object)}{$object.title|default:"<i>[no title]</i>"|escape}{else}<i>[{t}New item{/t}]</i>{/if}</h1>
    </div>
    
	{assign var=objIndex value=0}
    
	{include file="inc/menucommands.tpl" method="view" fixed = true}

	<div class="main">
		
		{include file="inc/form.tpl"}
		
	</div>

{$view->element('menuright')}