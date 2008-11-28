{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

		<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>

	
		{include file="../common_inc/messages.tpl"}
	
		<ul class="menuleft insidecol">

		{if $module_modify eq '1'}

			<li><a href="{$html->url('/bibliographies/view')}">{t}New bibliography{/t}</a></li>

		{/if}

		</ul>

{include file="../common_inc/export.tpl"}

{if (!empty($method)) && $method eq "index"}

		<div class="insidecol publishingtree">
			
				{$beTree->view($tree)}
		
		</div>

{/if}

{include file="../common_inc/previews.tpl"}

{include file="../common_inc/user_module_perms.tpl"}
		
</div>