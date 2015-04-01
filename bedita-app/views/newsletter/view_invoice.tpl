<script type="text/javascript">
$(document).ready(function() {
	
	openAtStart("#details");
	
	$("#selectMailStatus").change(function() {
		location.href = $(this).val();
	});
});
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="invoices"}

{include file="inc/menucommands.tpl" method="invoices" fixed=false}

<div class="head">

	<h1>{t}Invoice{/t} <i>{$object.title|escape}</i></h1>

</div>

<div class="main">
	
	<div class="tab"><h2>{t}Details{/t}</h2></div>
	<div id="details">
	<table class="bordered">
		<tr>
			<th>status:</th><td>{$object.mail_status}</td>
			<th>started at:</th><td>{$object.start_sending|date_format:$conf->dateTimePattern}
		</tr>
	{if $object.mail_status == "sent"}
		<tr>
			<th></th><td></td><th>ended at:</th><td>{$object.end_sending|date_format:$conf->dateTimePattern}</td>
		</tr>

		{assign var="totalTime" value=$beTime->dateDiff($object.start_sending, $object.end_sending)}

		<tr>
			<th>sent total time:</th><td>{$beTime->dateDiff($object.start_sending, $object.end_sending)} {t}minutes{/t}</td>
			<th>{t}mails / minute{/t}:</th><td>{math equation="round(y/x)" x=$totalTime y=$jobsOk}</td>
		</tr>
	{/if}
		<tr>
			<th>{t}total mails{/t}:</th><td colspan="3">{$totalJobs}</td>
		</tr>
		<tr>
			<th>{t}sent{/t}:</th><td>{$jobsOk}</td>
			<th>{t}pending{/t}:</th><td>{$jobsPending}</td>
		</tr>
		<tr>
			<th>{t}failed{/t}:</th><td>{$jobsFailed}</td>
			<th>{t}unsent{/t}:</th><td>{$jobsUnsent}</td>
		</tr>
	</table>
	</div>
	
	<div class="tab"><h2>{t}Jobs{/t}</h2></div>

	{assign_associative var=passedArgs url=$view->passedArgs}
	{$paginator->options($passedArgs)}

	{assign_associative var="optionsPagDisable" style="display: inline;"}
	{assign var="pagParams" value=$paginator->params()}
	<div id="jobs">

		<div style="white-space: nowrap">
		<table class="graced" style="border-bottom:1px solid gray">
			<tr>
				<td>
				&nbsp;<span class="evidence">{$pagParams.count}&nbsp;</span> {t}jobs{/t}
				</td>
				{assign var='label_page' value=$tr->t('page',true)}
				<td style="border:1px solid gray; border-top:0px; border-bottom:0px;">
					{if $paginator->hasPrev()}
						{$paginator->first($label_page)}
					{else}
						{t}page{/t}
					{/if}
					<span class="evidence"> {$paginator->current()}</span>
					{t}of{/t}
					<span class="evidence">
					{if $paginator->hasNext()}
						{$paginator->last($pagParams.pageCount)}
					{else}
						{$paginator->current()}
					{/if}
					</span>
				</td>
				{assign var='label_next' value=$tr->t('next',true)}
				{assign var='label_prev' value=$tr->t('prev',true)}
				<td>{$paginator->next($label_next,null,$label_next,$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></td>
				<td>{$paginator->prev($label_prev,null,$label_prev,$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></td>

				<td style="width:100%; padding-top:8px; border-left:1px solid gray;">
					{t}filter:{/t}{assign var="urlSelect" value=$view->passedArgs}
					{array_add var="urlSelect" page=1 sort="MailJob.sending_date" direction="asc"}
					<select id="selectMailStatus">
						<option value="{$paginator->url($urlSelect)}"> -- </option>
						{foreach from=$conf->checkConstraints.mail_jobs.status item="s"}
						{array_add var="urlSelect" status=$s}
						<option value="{$paginator->url($urlSelect)}"
							{if !empty($view->passedArgs.status) && $view->passedArgs.status == $s} selected{/if}>{t}{$s}{/t}</option>
						{/foreach}
					</select>
				</td>
			</tr>
		</table>
		</div>

		<table class="bordered" style="width:100%">

			<tr>
				{assign var="sendingDateLabel" value=$tr->t("sending date", true)}
				<th>{$paginator->sort($sendingDateLabel, "MailJob.sending_date")}:</th>
				{assign var="toRecipientLabel" value=$tr->t("to recipient", true)}
				<th>{$paginator->sort($toRecipientLabel, "Card.newsletter_email")}:</th>
				{assign var="statusLabel" value=$tr->t("status", true)}
				<th>{$paginator->sort($statusLabel, "MailJob.status")}:</th>
			</tr>

			{foreach from=$jobs item="j"}
			<tr>
				<td>{$j.MailJob.sending_date|date_format:$conf->dateTimePattern}</td>
				<td>{$j.Card.newsletter_email}</td>
				<td>{$j.MailJob.status}</td>
			</tr>
			{/foreach}

		</table>

	</div>



	{*dump var=$object*}
	{*dump var=$jobs*}
</div>
