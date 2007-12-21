{assign var='method' value=$method|default:'index'}
<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onClick="window.location='{$html->url('/areas')}'" class="areas"><a href="{$html->url('/areas')}">{t}Areas{/t}</a></h1>
		<div class="inside">
			{include file="../pages/user_module_perms.tpl"}
			<ul class="simpleMenuList" style="margin:10px 0px 10px 0px">
				<li {if $method eq 'index'}class="on"{/if}>		<b>&#8250;</b> {$tr->link('Areas Tree', '/areas')}</li>
				{if $module_modify eq '1'}
				<li {if $method eq 'viewArea'}class="on"{/if}>	<b>&#8250;</b> {$tr->link('New Area', '/areas/viewArea')}</li>
				<li {if $method eq 'viewSection'}class="on"{/if}>	<b>&#8250;</b> {$tr->link('New Section', '/areas/viewSection')}</li>
				{/if}
			</ul>
			<hr/>
		</div>
	</div>
	<hr/>
	<div id="handlerChangeAlert"></div>
	<br/>
	<br/>
	<br/>
</div>