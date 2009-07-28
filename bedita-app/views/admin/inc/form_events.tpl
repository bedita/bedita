<div class="tab"><h2>{t}System events{/t}</h2></div>

<fieldset id="system_events">
<div>



<table class="indexlist">
	<tr>
		<th>{$paginator->sort('Date', 'created')}</th>
		<th>{$paginator->sort('Level', 'level')}</th>
		<th>{$paginator->sort('User', 'user')}</th>
		<th>{$paginator->sort('Msg', 'msg')}</th>
		<th>{$paginator->sort('Context', 'context')}</th>
	</tr>
	{foreach from=$events item=e}
	<tr>
		<td style="white-space:nowrap">{$e.EventLog.created}</td>
		<td class="{$e.EventLog.level}">{$e.EventLog.level}</td>
		<td>{$e.EventLog.user}</td>
		<td>{$e.EventLog.msg}</td>
		<td>{$e.EventLog.context}</td>
	</tr>
	{/foreach}
</table>

</div>
</fieldset>