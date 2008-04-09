{assign var='method' value=$method|default:'index'}
<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onClick="window.location='{$html->url('/events')}'" class="eventi"><a href="{$html->url('/events')}">{t}Events{/t}</a></h1>
		<div class="inside">
			<ul class="simpleMenuList" style="margin: 15px 0;">
				<li {if $method eq 'index'}class="on"{/if}> <b>&#8250;</b> {$tr->link('Events', '/events')}</li>
				<li {if $method eq 'categories'}class="on"{/if}> <b>&#8250;</b> {$tr->link('Categories', '/events/categories')}</li>
				{if $module_modify eq '1'}
				<li {if $method eq 'view'}class="on"{/if}> <b>&#8250;</b> {$tr->link('New Event', '/events/view')}</li>
				{/if}
			</ul>	
			<hr/>
			{include file="../pages/user_module_perms.tpl"}
			<hr/>
		</div>
	</div>
	<br/>
	<div id="handlerChangeAlert"></div>
	<br/>
</div>
