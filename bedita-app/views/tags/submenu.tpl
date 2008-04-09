{assign var='method' value=$method|default:'index'}
<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onClick="window.location='{$html->url('/tags')}'" class="tags"><a href="{$html->url('/tags')}">{t}Tags{/t}</a></h1>
		<div class="inside">
			<ul class="simpleMenuList" style="margin: 15px 0;">
				<li {if $method eq 'index'}class="on"{/if}>		<b>&#8250;</b> {$tr->link('Tags', '/tags')}</li>
				{if $module_modify eq '1'}
				<li {if $method eq 'view'}class="on"{/if}>	<b>&#8250;</b> {$tr->link('New Tag', '/tags/view')}</li>
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