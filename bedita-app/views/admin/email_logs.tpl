{$html->script("jquery/jquery.treeview", false)}
{$html->script("form", false)}

<script type="text/javascript">
	$(document).ready(function() { 
		$("#email_logs").prev(".tab").BEtabstoggle();
	} );
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="emailInfo"}

{include file="inc/menucommands.tpl" method="emailInfo" fixed=true}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
		<h2>{t}Mail Logs{/t}</h2>
		{include file="./inc/toolbar.tpl" label_items='logs'}
	</div>
</div>

<div class="mainfull">

<div class="tab"><h2>{t}Email log{/t}</h2></div>

	<fieldset id="email_logs">

	<div>
	{assign var='label_id' value=$tr->t('id',true)}
	{assign var='label_created' value=$tr->t('created',true)}
	{assign var='label_log_level' value=$tr->t('log level',true)}
	{assign var='label_recipient' value=$tr->t('to',true)}
	{assign var='label_subject' value=$tr->t('subject',true)}
	{assign var='label_msg' value=$tr->t('mail body',true)}
	<table class="indexlist">
		<tr>
			<th>{$paginator->sort($label_id,'id')}</th>
			<th>{$paginator->sort($label_created,'created')}</th>
			<th>{$paginator->sort($label_log_level,'log_level')}</th>
			<th>{$paginator->sort($label_recipient,'recipient')}</th>
			<th>{$paginator->sort($label_subject,'subject')}</th>
			<th>{$paginator->sort($label_msg,'mail_body')}</th>
			<td>-</td>
		</tr>
		{if !empty($logs)}
		{foreach from=$logs item=j}
		<form id="form_log_{$j.MailLog.id}" method="post" action="">
		<tr>
			<td style="white-space:nowrap">{$j.MailLog.id}</td>
			<td style="white-space:nowrap">{$j.MailLog.created|date_format:$conf->dateTimePattern}</td>
			<td style="white-space:nowrap">{$j.MailLog.log_level}</td>
			<td style="white-space:nowrap">{$j.MailLog.recipient|default:''}</td>
			<td>{$j.MailLog.subject|default:''}</td>
			<td>{$j.MailLog.mail_body|default:''|truncate:64}</td>
			<td><input type="button" class="delLog" value="{t}Delete{/t}" title="{$j.MailLog.id}" /></td>
		</tr>
		{/foreach}
		{/if}
	</table>

	</div>
	</fieldset>

</div>