<div class="tab"><h2>{t}System events{/t}</h2></div>

<fieldset id="system_events">
<div>
{assign var='label_date' value=$this->Tr->t('date',true)}
{assign var='label_level' value=$this->Tr->t('level',true)}
{assign var='label_user' value=$this->Tr->t('User',true)}
{assign var='label_msg' value=$this->Tr->t('msg',true)}
{assign var='label_context' value=$this->Tr->t('context',true)}
<table class="indexlist">
	<tr>
		<th>{$this->Paginator->sort($label_date,'created')}</th>
		<th>{$this->Paginator->sort($label_level,'level')}</th>
		<th>{$this->Paginator->sort($label_user,'user')}</th>
		<th>{$this->Paginator->sort($label_msg,'msg')}</th>
		<th>{$this->Paginator->sort($label_context,'context')}</th>
	</tr>
	{foreach from=$events item=e}
	<tr>
		<td style="white-space:nowrap">{$e.EventLog.created}</td>
		<td class="{$e.EventLog.log_level}">{$e.EventLog.log_level}</td>
		<td>{$e.EventLog.userid}</td>
		<td>{$e.EventLog.msg}</td>
		<td>{$e.EventLog.context}</td>
	</tr>
	{/foreach}
</table>

</div>
</fieldset>