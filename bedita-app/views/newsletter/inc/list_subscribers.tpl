{* DO NOT MOVE FROM HERE!! used in ajax call*}
{if !empty($subscribers)}
		
<table class="indexlist js-header-float">
	<thead>
		<tr id="orderSubscribers">
			<th></th>
			<th>{$beToolbar->order('newsletter_email', 'Email')}</th>
			<th>{$beToolbar->order('mail_html', 'Html')}</th>
			<th>{$beToolbar->order('mail_status', 'Status')}</th>
			<th>{$beToolbar->order('created', 'Inserted on')}</th>
			<th></th>
		</tr>
	</thead>
	
	{foreach from=$subscribers item="s"}
	<tr>
		<td><input name="objects_selected[]" type="checkbox" class="objectCheck" value="{$s.id}" /></td>
		<td>{$s.newsletter_email}</td>
		<td>{if $s.mail_html}{t}yes{/t}{else}{t}no{/t}{/if}</td>
		<td>{t}{$s.mail_status}{/t}</td>
		<td>{$s.created|date_format:$conf->datePattern}</td>
		<td><a href="{$html->url('/view/')}{$s.id}">› {t}details{/t}</a></td>
	</tr>
	{/foreach}
	
	</table>
	<hr />

	<table class="graced" id="paginateSubscribers">
	<tr>
		<td>
			{$beToolbar->first('page','','page')}
			<span class="evidence"> {$beToolbar->current()} </span> 
			{t}of{/t} 
			<span class="evidence"> {$beToolbar->last($beToolbar->pages(),'',$beToolbar->pages())} </span>
			&nbsp;
		</td>
		<td style="border:1px solid gray; border-top:0px; border-bottom:0px;">{$beToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span></td>
		<td>{$beToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span></td>
	</tr>
</table>
	
{else}
	{t}No subscribers{/t}
{/if}