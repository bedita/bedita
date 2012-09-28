
<script type="text/javascript">
	var message = "{t}Are you sure that you want to delete the item?{/t}";
	var delJobUrl = '{$this->Html->url('/admin/deleteMailJob')}';

	$(document).ready(function() { 
		$(".delJob").bind("click", function() { 
			if(!confirm(message))
				return false ;
			var jobId = $(this).attr("title");
			$("#form_job_"+jobId).attr("action", delJobUrl + '/' + jobId).submit();
		} );
	} );
</script>

<div class="tab"><h2>{t}Mail queues{/t}</h2></div>
<fieldset id="email_jobs">
<div>
{assign var='label_id' value=$this->Tr->t('id',true)}
{assign var='label_created' value=$this->Tr->t('created',true)}
{assign var='label_recipient' value=$this->Tr->t('recipient',true)}
{assign var='label_sending_date' value=$this->Tr->t('sent',true)}
{assign var='label_mail_body' value=$this->Tr->t('mail body',true)}
{assign var='label_status' value=$this->Tr->t('status',true)}
<table class="indexlist">
	<tr>
		<th>{$this->Paginator->sort($label_id,'id')}</th>
		<th>{$this->Paginator->sort($label_created,'created')}</th>
		<th>{$this->Paginator->sort($label_recipient,'recipient')}</th>
		<th>{$this->Paginator->sort($label_sending_date,'sending_date')}</th>
		<th>{t}Newsletter{/t}</th>
		<th>{$this->Paginator->sort($label_mail_body,'mail_body')}</th>
		<th>{$this->Paginator->sort($label_status,'status')}</th>
		<th></th>
	</tr>
	{foreach from=$jobs item=j}	
	<form id="form_job_{$j.MailJob.id}" method="post" action="">
	<tr>
		<td style="white-space:nowrap">{$j.MailJob.id}</td>
		<td style="white-space:nowrap">{$j.MailJob.created|date_format:$conf->dateTimePattern}</td>
		<td style="white-space:nowrap">{$j.MailJob.recipient}</td>
		<td style="text-align:center; white-space:nowrap">
			{$j.MailJob.sending_date|date_format:$conf->dateTimePattern|default:'no'}
		</td>
		<td style="text-align:center">
			{if !empty($j.MailJob.mail_message_id)}
			<a href="{$this->Html->url('/newsletter/view/')}{$j.MailJob.mail_message_id}">{$j.MailJob.mail_message_id}</a>
			{else}
				
			{/if}
		</td>
		<td>{$j.MailJob.mail_body|truncate:64}</td>
		<td>{$j.MailJob.status}</td>
		<td>{if $j.MailJob.status != 'pending'}<input type="button" class="delJob" value="{t}Delete{/t}" title="{$j.MailJob.id}" />{/if}</td>
	</tr>
	</form>
	{/foreach}
</table>

</div>
</fieldset>

<div class="tab"><h2>{t}Mail queue summary{/t}</h2></div>
<fieldset id="email_summary">
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
</fieldset>