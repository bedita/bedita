<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onclick="window.location='{$html->url('/galleries')}'" class="galleries"><a href="{$html->url('/galleries')}">{t}Galleries{/t}</a></h1>
		<div class="inside">
			{include file="../pages/user_module_perms.tpl"}
			<ul class="simpleMenuList" style="margin:10px 0px 10px 0px">
				<li {if $method eq 'index'}class="on"{/if}> <b>&#8250;</b> {$tr->link('Galleries', '/galleries')}</li>
				{if $module_modify eq '1'}
				<li {if $method eq 'view'}class="on"{/if}> <b>&#8250;</b> {$tr->link('New Gallery', '/galleries/view')}</li>
				{/if}
			</ul>
			<hr/>
		</div>
	</div>
	<hr/>
	<br/>
	<br/>
	<div id="handlerChangeAlert"></div>
	<br/>
</div>