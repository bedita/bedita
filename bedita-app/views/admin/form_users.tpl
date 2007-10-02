<div id="containerPage">
	
		<div class="FormPageHeader">
		<h1>{t}Users admin{/t}</h1>
		</div>
		<div id="mainForm">
			<br />
			<form action="{$html->url('/admin/users')}" method="post" name="userForm" id="userForm">
			<table border="0" cellspacing="8" cellpadding="0">
			<thead>
			<th>{t}User{/t}</th>
			<th>{t}Name{/t}</th>
			</thead>
			{foreach from=$users item=u}
			<tr>
				<td>{$u.User.userid}</td>
				<td>{$u.User.realname}</td>
			</tr>
   			{/foreach}
			</table>
</form>
	</div>
	
</div>
