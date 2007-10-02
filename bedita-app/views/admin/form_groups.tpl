<div id="containerPage">
	
		<div class="FormPageHeader">
		<h1>{t}Groups admin{/t}</h1>
		</div>
		<div id="mainForm">
			<br />
			<form action="{$html->url('/admin/groups')}" method="post" name="groupForm" id="groupForm">
			<table border="0" cellspacing="8" cellpadding="0">
			<thead>
			<th>{t}Group{/t}</th>
			<th>{t}Name{/t}</th>
			</thead>
			{foreach from=$groups item=g}
			<tr>
				<td>{$g.Group.id}</td>
				<td>{$g.Group.name}</td>
			</tr>
   			{/foreach}
			</table>
</form>
	</div>
	
</div>
