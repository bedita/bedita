<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onclick="window.location='{$html->url('/admin')}'" class="superadmin"><a href="{$html->url('/admin')}">{t}Admin{/t}</a></h1>
		<div class="inside">
			<ul class="simpleMenuList" style="margin: 15px 0;">
				<li {if $method eq 'index'}class="on"{/if}> <b>&#8250;</b> {$tr->link('Users', '/admin')}</li>
				<li {if $method eq 'groups'}class="on"{/if}> <b>&#8250;</b> {$tr->link('Groups', '/admin/groups')}</li>
				<li {if $method eq 'viewUser'}class="on"{/if}> <b>&#8250;</b> {$tr->link('New user', '/admin/viewUser')}</li>
				<li {if $method eq 'systemInfo'}class="on"{/if}> <b>&#8250;</b> {$tr->link('System Info', '/admin/systemInfo')}</li>
			</ul>
			<hr/>
			{include file="../pages/user_module_perms.tpl"}
			<hr/>
		</div>
	</div>
</div>
