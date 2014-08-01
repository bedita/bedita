
<script type="text/javascript">
	var message = "{t}Are you sure that you want to delete the item?{/t}";

	$(document).ready(function() {
		$(".delJob").bind("click", function() {
			if (!confirm(message)) {
				return false;
			}
			var jobId = $(this).prop("title");
			$("#form_job_" + jobId).submit();
		} );
	} );
</script>

<div class="tab"><h2>{t}Mail queues{/t}</h2></div>
<fieldset id="email_jobs">
<div>
{assign var='label_id' value=$tr->t('id',true)}
{assign var='label_created' value=$tr->t('created',true)}
{assign var='label_recipient' value=$tr->t('recipient',true)}
{assign var='label_sending_date' value=$tr->t('sent',true)}
{assign var='label_mail_body' value=$tr->t('mail body',true)}
{assign var='label_status' value=$tr->t('status',true)}
<table class="indexlist">
	<tr>
		<th>{$paginator->sort($label_id,'id')}</th>
		<th>{$paginator->sort($label_created,'created')}</th>
		<th>{$paginator->sort($label_recipient,'recipient')}</th>
		<th>{$paginator->sort($label_sending_date,'sending_date')}</th>
		<th>{t}Newsletter{/t}</th>
		<th>{$paginator->sort($label_mail_body,'mail_body')}</th>
		<th>{$paginator->sort($label_status,'status')}</th>
		<th></th>
	</tr>
	{foreach from=$jobs item=j}
		{assign_concat var='formId' 1='form_job_' 2=$j.MailJob.id}
		{$view->Form->create(null, ['action' => 'deleteMailJob', 'id' => $formId])}
		<tr>
			<td style="white-space:nowrap">{$j.MailJob.id}</td>
			<td style="white-space:nowrap">{$j.MailJob.created|date_format:$conf->dateTimePattern}</td>
			<td style="white-space:nowrap">{$j.MailJob.recipient}</td>
			<td style="text-align:center; white-space:nowrap">
				{$j.MailJob.sending_date|date_format:$conf->dateTimePattern|default:'no'}
			</td>
			<td style="text-align:center">
				{if !empty($j.MailJob.mail_message_id)}
				<a href="{$html->url('/newsletter/view/')}{$j.MailJob.mail_message_id}">{$j.MailJob.mail_message_id}</a>
				{else}

				{/if}
			</td>
			<td>{$j.MailJob.mail_body|truncate:64}</td>
			<td>{$j.MailJob.status}</td>
			<td>
				{$view->Form->hidden('MailJob.id', ['value' => $j.MailJob.id])}
				{if $j.MailJob.status != 'pending'}
					<input type="button" class="delJob" value="{t}Delete{/t}" title="{$j.MailJob.id}" />
				{/if}
			</td>
		</tr>
		{$view->Form->end()}
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