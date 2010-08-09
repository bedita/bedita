{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="invoices"}

{include file="inc/menucommands.tpl" method="invoices" fixed=false}

<div class="head">

	<h1>{t}Invoice{/t} <i>{$object.title}</i></h1>

</div>

<div class="main">

	status: {$object.mail_status}
	<br/>
	started sending: {$object.start_sending|date_format:$conf->dateTimePattern}
	<br/>

	{if $object.mail_status == "sent"}
	end sending: {$object.end_sending|date_format:$conf->dateTimePattern}
	<br/>
	{assign var="totalTime" value=$beTime->dateDiff($object.start_sending, $object.end_sending)}
	sent total time: {$beTime->dateDiff($object.start_sending, $object.end_sending)} {t}minutes{/t}
	<br/>
	{t}mails for minute{/t}: {math equation="round(y/x)" x=$totalTime y=$jobsOk}

	{/if}

	<br/>
	total mails: {$totalJobs}

	<br/>
	sent mails: {$jobsOk}

	<br/>
	failed mails: {$jobsFailed}

	<br/>
	pending mails: {$jobsPending}

	<br/>
	unsent mails: {$jobsUnsent}




	<div class="tab"><h2>{t}Jobs{/t}</h2></div>

	{assign_associative var=passedArgs url=$view->passedArgs}
	{$paginator->options($passedArgs)}

	{assign_associative var="optionsPagDisable" style="display: inline;"}
	{assign var="pagParams" value=$paginator->params()}
	<div>
		<div class="toolbar" style="white-space: nowrap">
		<table>
			<tr>
				<td>
				<span class="evidence">{$pagParams.count}&nbsp;</span> {t}jobs{/t}
				</td>
				{assign var='label_page' value=$tr->t('page',true)}
				<td>
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
