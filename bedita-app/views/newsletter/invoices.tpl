
{$view->element('modulesmenu', ['substringSearch' => false])}

{include file="inc/menuleft.tpl" method="invoices"}

{include file="inc/menucommands.tpl" method="invoices" fixed=false}

<div class="head">
	
	<div class="toolbar" style="white-space:nowrap">
		<h2>{t}Invoices{/t}</h2>
		<table>
			<tr>
				<td><span class="evidence">{$sent}&nbsp;</span> {t}sent{/t}</td>
				<td><span class="evidence">{$inJob}&nbsp;</span> {t}in job{/t}</td>
				<td><span class="evidence"> {$scheduled} &nbsp;</span> {t}scheduled{/t}</td>
				<td>

					{t}next invoice{/t}:&nbsp;&nbsp;<span class="evidence">
						{if (($nextInvoiceDate|date_format:$conf->datePattern) == ($smarty.now|date_format:$conf->datePattern))}
							{t}today{/t}
						{else} 
						{$nextInvoiceDate|date_format:$conf->datePattern} {/if}</span>
					&nbsp;{t}at{/t}&nbsp;&nbsp;<span class="evidence">{$nextInvoiceDate|date_format:"%H:%M"}</span>
				</td>
			</tr>
		</table>

	</div>

</div>

<div class="mainfull">

	{include file="inc/list_invoices.tpl"}

</div>

