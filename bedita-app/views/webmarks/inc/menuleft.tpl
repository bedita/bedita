{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>

	{include file="../common_inc/messages.tpl"}

	<ul class="menuleft insidecol">
	{if $module_modify eq '1'}

			<li><a href="{$html->url('/')}{$currentModule.path}/view">{t}New link{/t}</a></li>
			<li><a href="{$html->url('/')}{$currentModule.path}/categories">{t}Categories{/t}</a></li>
	{/if}
	</ul>

{include file="../common_inc/export.tpl"}


{include file="../common_inc/user_module_perms.tpl"}

</div>