<div id="containerPage">
	<form action="{$html->url('/admin/deleteEventLog')}" method="post">
	<div class="FormPageHeader"><h1>{t}System info{/t}</h1>
		<ul>
		<li><b>bedita: {$conf->Bedita.version}</b></li>
		<li><b>cake:  {$conf->Cake.version}</b></li>
		</ul>
	</div>
	<div class="FormPageHeader"><h1>{t}System events{/t}</h1><input type="submit" value="{t}Delete all events{/t}"/>
	</div>
	</form>
	<div id="mainForm">
		<table class="indexList">
		<thead><tr><th>{t}Date{/t}</th>
				<th>{t}Level{/t}</th>
				<th>{t}User{/t}</th>
				<th>{t}Msg{/t}</th>
				<th>{t}Context{/t}</th>
				</tr>
		</thead>
		<tbody>
		{foreach from=$events item=e}
		<tr class="rowList">
			<td>{$e.EventLog.created}</td>
			<td><em>{$e.EventLog.level}</em></td>
			<td>{$e.EventLog.user}</td>
			<td>{$e.EventLog.msg}</td>
			<td>{$e.EventLog.context}</td>
		</tr>
		{/foreach}
		</table>
	</div>
</div>