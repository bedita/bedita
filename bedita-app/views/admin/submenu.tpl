<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onclick="window.location='{$html->url('/admin')}'" class="superadmin"><a href="{$html->url('/admin')}">{t}Admin{/t}</a></h1>
		<div class="inside">
			<ul class="simpleMenuList" style="margin:10px 0px 10px 0px">
				<li {if $method eq 'index'}class="on"{/if}> <b>&#8250;</b> {$tr->link('Users admin', '/admin')}</li>
				<li {if $method eq 'groups'}class="on"{/if}>	<b>&#8250;</b> {$tr->link('Groups admin', '/admin/groups')}</li>
				<li {if $method eq 'events'}class="on"{/if}>	<b>&#8250;</b> {$tr->link('System events', '/admin/events')}</li>
			</ul>
			<hr/>
		</div>
	</div>
	<hr/>
	<br/>
	<br/>
	<br/>
</div>