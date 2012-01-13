{literal}
<script type="text/javascript">

	var message = "{/literal}{t}Are you sure that you want to delete the item?{/t}{literal}";
	var delJobUrl = '{/literal}{$html->url('/admin/deleteMailJob')}{literal}';
	var delLogUrl = '{/literal}{$html->url('/admin/deleteMailLog')}{literal}';

	$(document).ready(function(){
		$(".delJob").bind("click", function(){
			if(!confirm(message))
				return false ;
			var jobId = $(this).attr("title");
			$("#form_job_"+jobId).attr("action", delJobUrl + '/' + jobId).submit();
			return false;
		});
		$(".delLog").bind("click", function(){
			if(!confirm(message))
				return false ;
			var logId = $(this).attr("title");
			$("#form_log_"+logId).attr("action", delLogUrl + '/' + logId).submit();
			return false;
		});
	});
</script>
{/literal}



<div class="tab"><h2>{t}Job summary{/t}</h2></div>
<table class="bordered" style="width:100%">
<tr>
	<td>{t}Total{/t}</td>
	<td>{t}Pending{/t}</td>
	<td>{t}Sent{/t}</td>
	<td>{t}Unsent{/t}</td>
	<td>{t}Failed{/t}</td>
</tr>
<tr>
	<td>{$totalJobs}</td>
	<td>{$jobsPending}</td>
	<td>{$jobsSent}</td>
	<td>{$jobsUnsent}</td>
	<td>{$jobsFailed}</td>
</tr>
</table>

<div class="tab"><h2>{t}Job details{/t}</h2></div>

<fieldset id="email_jobs">
<div>
{assign var='label_id' value=$tr->t('id',true)}
{assign var='label_created' value=$tr->t('created',true)}
{assign var='label_modified' value=$tr->t('modified',true)}
{assign var='label_sending_date' value=$tr->t('sent',true)}
{assign var='label_mail_body' value=$tr->t('mail body',true)}
{assign var='label_status' value=$tr->t('status',true)}
<table class="indexlist">
	<tr>
		<th>{$paginator->sort($label_id,'id')}</th>
		<th>{$paginator->sort($label_created,'created')}</th>
		<th>{$paginator->sort($label_modified,'modified')}</th>
		<th>{$paginator->sort($label_sending_date,'sending_date')}</th>
		<th>{$paginator->sort($label_mail_body,'mail_body')}</th>
		<th>{$paginator->sort($label_status,'status')}</th>
		<td>-</td>
	</tr>
	{foreach from=$jobs item=j}
	<form id="form_job_{$j.MailJob.id}" method="post" action="">
	<tr>
		<td style="white-space:nowrap">{$j.MailJob.id}</td>
		<td style="white-space:nowrap">{$j.MailJob.created|date_format:$conf->dateTimePattern}</td>
		<td style="white-space:nowrap">{$j.MailJob.modified|date_format:$conf->dateTimePattern}</td>
		<td style="white-space:nowrap">{$j.MailJob.sending_date|date_format:$conf->dateTimePattern}</td>
		<td>{$j.MailJob.mail_body|truncate:64}</td>
		<td>{$j.MailJob.status}</td>
		<td>{if $j.MailJob.status != 'pending'}<input type="button" class="delJob" value="{t}Delete{/t}" title="{$j.MailJob.id}" />{/if}</td>
	</tr>
	</form>
	{/foreach}
</table>

</div>
</fieldset>

<div class="tab"><h2>{t}Emails{/t}</h2></div>
<fieldset id="single_emails">
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
	{foreach from=$logs item=j}
	<form id="form_log_{$j.MailLog.id}" method="post" action="">
	<tr>
		<td style="white-space:nowrap">{$j.MailLog.id}</td>
		<td style="white-space:nowrap">{$j.MailLog.created|date_format:$conf->dateTimePattern}</td>
		<td style="white-space:nowrap">{$j.MailLog.log_level}</td>
		<td style="white-space:nowrap">{$j.MailLog.recipient}</td>
		<td>{$j.MailLog.subject}</td>
		<td>{$j.MailLog.mail_body|truncate:64}</td>
		<td><input type="button" class="delLog" value="{t}Delete{/t}" title="{$j.MailLog.id}" /></td>
	</tr>
	{/foreach}
</table>

</div>
</fieldset>