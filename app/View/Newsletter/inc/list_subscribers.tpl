{* DO NOT MOVE FROM HERE!! used in ajax call*}
{if !empty($subscribers)}
		
	<table class="indexlist">
	<tr id="orderSubscribers">
		<th></th>
		<th>{$this->BeToolbar->order('newsletter_email', 'Email')}</th>
		<th>{$this->BeToolbar->order('mail_html', 'Html')}</th>
		<th>{$this->BeToolbar->order('mail_status', 'Status')}</th>
		<th>{$this->BeToolbar->order('created', 'Inserted on')}</th>
		<th></th>
	</tr>
	
	{foreach from=$subscribers item="s"}
	<tr>
		<td><input name="objects_selected[]" type="checkbox" class="objectCheck" value="{$s.id}" /></td>
		<td>{$s.newsletter_email}</td>
		<td>{if $s.mail_html}{t}yes{/t}{else}{t}no{/t}{/if}</td>
		<td>{t}{$s.mail_status}{/t}</td>
		<td>{$s.created|date_format:$conf->datePattern}</td>
		<td><a href="{$this->Html->url('/addressbook/view/')}{$s.id}">› {t}details{/t}</a></td>
	</tr>
	{/foreach}
	
	</table>
	<hr />

	<table class="graced" id="paginateSubscribers">
	<tr>
		<td>
			{$this->BeToolbar->first('page','','page')}
			<span class="evidence"> {$this->BeToolbar->current()} </span> 
			{t}of{/t} 
			<span class="evidence"> {$this->BeToolbar->last($this->BeToolbar->pages(),'',$this->BeToolbar->pages())} </span>
			&nbsp;
		</td>
		<td style="border:1px solid gray; border-top:0px; border-bottom:0px;">{$this->BeToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span></td>
		<td>{$this->BeToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span></td>
	</tr>
	</table>
	
{else}
	{t}No subscribers{/t}
{/if}