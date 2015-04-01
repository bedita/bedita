<div class="tab"><h2>{t}System events{/t}</h2></div>

<fieldset id="system_events">
<div>
{assign var='label_date' value=$tr->t('date',true)}
{assign var='label_level' value=$tr->t('level',true)}
{assign var='label_user' value=$tr->t('User',true)}
{assign var='label_msg' value=$tr->t('msg',true)}
{assign var='label_context' value=$tr->t('context',true)}
<table class="indexlist">
	<thead>
		<tr>
			<th>{$paginator->sort($label_date,'created')}</th>
			<th>{$paginator->sort($label_level,'level')}</th>
			<th>{$paginator->sort($label_user,'user')}</th>
			<th>{$paginator->sort($label_msg,'msg')}</th>
			<th>{$paginator->sort($label_context,'context')}</th>
		</tr>
	</thead>
	{foreach from=$events item=e}
	<tr>
		<td style="white-space:nowrap">{$e.EventLog.created}</td>
		<td class="{$e.EventLog.log_level}">{$e.EventLog.log_level}</td>
		<td>{$e.EventLog.userid|escape}</td>
		<td>{$e.EventLog.msg|escape}</td>
		<td>{$e.EventLog.context}</td>
	</tr>
	{/foreach}
</table>

</div>
</fieldset>