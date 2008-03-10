{assign var='method' value=$method|default:'index'}
<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onClick="window.location='{$html->url('/tags')}'" class="tags"><a href="{$html->url('/tags')}">{t}Tags{/t}</a></h1>
		<div class="inside">
			{include file="../pages/user_module_perms.tpl"}
			<ul class="simpleMenuList" style="margin:10px 0px 10px 0px">
				<li {if $method eq 'index'}class="on"{/if}>		<b>&#8250;</b> {$tr->link('Tags', '/tags')}</li>
				{if $module_modify eq '1'}
				<li {if $method eq 'view'}class="on"{/if}>	<b>&#8250;</b> {$tr->link('New Tag', '/tags/view')}</li>
				{/if}
			</ul>	
			<hr/>
		</div>
	</div>
	<hr/>
	<div id="handlerChangeAlert">
	</div>
	<br/>
	<br/>
	<br/>
</div>