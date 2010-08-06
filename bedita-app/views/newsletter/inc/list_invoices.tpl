<table class="indexlist vtop">
	{capture name="theader}
	<tr>
		<th>{$beToolbar->order('start_sending', 'sending date')}</th>
		<th>{$beToolbar->order('mail_status', 'status')}</th>
		<th>{$beToolbar->order('title', 'newsletter title')}</th>
		<th>{t}to recipients{/t}</th>
		<th>{$beToolbar->order('id', 'invoice id')}</th>
	</tr>
	{/capture}

	{$smarty.capture.theader}

	{foreach from=$objects item="obj" name="i"}
		<tr>
			<td>{$obj.start_sending|date_format:$conf->dateTimePattern}</td>

			{if !empty($obj.mail_status) && $obj.mail_status == "injob"}
				<td style="color:red; text-decoration: blink;">{t}in job{/t}</td>
			{elseif  ($obj.mail_status == "pending")}
				<td class="info">{t}{$obj.mail_status|default:''}{/t}</td>
			{else}
				<td>{t}{$obj.mail_status|default:''}{/t}</td>
			{/if}
				
			<td><a title="details of '{$obj.title}'" href="{$html->url('/newsletter/viewInvoice/')}{$obj.id}">{$obj.title}</a></td>
			<td style="padding-left:20px">
				<ul style="list-style-type:disc">
				{foreach from=$obj.MailGroup item="recipient"}
					<li><a href="{$html->url('/newsletter/viewMailGroup/')}{$recipient.id}">{$recipient.group_name}</li>
				{/foreach}
				</ul>
			</td>

			<td style="text-align:center">{$obj.id}</td>
		</tr>

	{foreachelse}
		<tr><td colspan="100">{t}No invoices{/t}</td></tr>
	{/foreach}

	{if ($smarty.foreach.i.total) >= 10}

		{$smarty.capture.theader}

	{/if}
</table>

<br />

{if !empty($objects)}
<div style="white-space:nowrap">

	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')} 
	&nbsp;
	{t}of{/t}&nbsp;
	{if ($beToolbar->pages()) > 0}
	{$beToolbar->last($beToolbar->pages(),'',$beToolbar->pages())}
	{else}1{/if}
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
	
	&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;
	{$beToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span>	
	| &nbsp;&nbsp;
	{$beToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span>
</div>
{/if}
{*dump var=$objects*}