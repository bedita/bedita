{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	{include file="../common_inc/messages.tpl"}

	<ul class="menuleft insidecol">
	{if $module_modify eq '1'}
		<li {if $method eq 'index'}class="on"{/if}>{$tr->link('Events', '/events')}</li>
		<li {if $method eq 'categories'}class="on"{/if}>{$tr->link('Categories', '/events/categories')}</li>
		<li><a href="{$html->url('/')}{$currentModule.path}/view">{t}Create new event{/t}</a></li>

	{/if}
	</ul>

{include file="../common_inc/export.tpl"}

	{if (!empty($method)) && $method eq "index"}
	<div class="insidecol publishingtree">
		
		{include file="../common_inc/tree.tpl"}
	
	</div>
	{/if}

{include file="../common_inc/previews.tpl"}

{include file="../common_inc/user_module_perms.tpl"}

</div>