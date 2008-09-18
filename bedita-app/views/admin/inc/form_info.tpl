<div class="tab"><h2>{t}System info {/t}</h2></div>
<fieldset id="system_info">
	
<div>	
	
		<ul class="bordered">
			<li><label>bedita:</label>  {$conf->Bedita.version}</li>
			<li><label>cake:</label>   {$conf->Cake.version}</li>
			<li><label>php:</label>    {$sys.phpVersion}</li>
			<li><label>mysql:</label>  server {$sys.mysqlServer} - client {$sys.mysqlClient}</li>
		</ul>

</div>
</fieldset>

<div class="tab"><h2>{t}System events{/t}</h2></div>

<fieldset id="system_events">
<div>

{include file="./inc/toolbar.tpl" label_items='system events'}

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