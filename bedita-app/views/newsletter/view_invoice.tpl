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

	

	



	{dump var=$object}
</div>
