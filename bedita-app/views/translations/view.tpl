{*
** translations view template
*}

{$javascript->link("jquery/ui/ui.datepicker.min", false)}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

<div class="head">
	{if !empty($object_translation.title)}<h1>{$object_translation.title|default:'<i>[no title]</i>'}</h1>{/if}
	{t}translation of{/t}
	<h1 style="margin-top:0px">{$object_master.title|default:'<i>[no title]</i>'}</h1>

</div>

{assign var=objIndex value=0}


{include file="inc/menucommands.tpl" fixed = true}


<div class="mainfull">	

	{include file="inc/form.tpl"}

</div>


{*$view->element('menuright')*}