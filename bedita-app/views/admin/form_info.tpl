<div id="containerPage">
	<div class="FormPageHeader"><h1>{t}System events{/t}</h1></div>
	<div id="mainForm">
		<table class="indexList">
		<thead><tr><th>{t}Date{/t}</th>
				<th>{t}Level{/t}</th>
				<th>{t}User{/t}</th>
				<th>{t}Msg{/t}</th>
				</tr>
		</thead>
		<tbody>
		{foreach from=$events item=e}
		<tr class="rowList">
			<td>{$e.EventLog.created}</td>
			<td><em>{$e.EventLog.level}</em></td>
			<td>{$e.EventLog.user}</td>
			<td>{$e.EventLog.msg}</td>
		</tr>
		{/foreach}
		</table>
	</div>
</div>