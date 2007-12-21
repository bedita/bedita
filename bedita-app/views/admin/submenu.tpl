<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onclick="window.location='{$html->url('/admin')}'" class="superadmin"><a href="{$html->url('/admin')}">{t}Admin{/t}</a></h1>
		<div class="inside">
			{include file="../pages/user_module_perms.tpl"}
			<hr/>
			<ul class="simpleMenuList" style="margin:10px 0px 10px 0px">
				<li {if $method eq 'index'}class="on"{/if}> <b>&#8250;</b> {$tr->link('Users list', '/admin')}</li>
				<li {if $method eq 'viewUser'}class="on"{/if}> <b>&#8250;</b> {$tr->link('Manage user', '/admin/viewUser')}</li>
				<li {if $method eq 'groups'}class="on"{/if}> <b>&#8250;</b> {$tr->link('Groups', '/admin/groups')}</li>
				<li {if $method eq 'systemInfo'}class="on"{/if}> <b>&#8250;</b> {$tr->link('System Info', '/admin/systemInfo')}</li>
			</ul>
			<hr/>
		</div>
	</div>
</div>