{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">
	
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>
	
		{include file="../common_inc/messages.tpl"}
		
	<ul class="menuleft insidecol">
		<li {if $method eq 'index'}class="on"{/if}>{$tr->link('Events', '/events')}</li>
		<li {if $method eq 'categories'}class="on"{/if}>{$tr->link('Categories', '/events/categories')}</li>
	{if $module_modify eq '1'}
		<li {if $method eq 'view'}class="on"{/if}>{$tr->link('New Event', '/events/view')}</li>
	{/if}
									
	</ul>

{if (!empty($method)) && $method eq "index"}

		<div class="insidecol publishingtree">
			
				{$beTree->view($tree)}
		
		</div>

{/if}

<div id="handlerChangeAlert"></div>

</div>


{*include file="../pages/user_module_perms.tpl"*}