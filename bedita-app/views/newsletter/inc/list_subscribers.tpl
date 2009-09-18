{if !empty($object)}

<div class="tab"><h2>Subscribers</h2></div>

<fieldset id="divSubscribers">
		
	<div id="loaderListSubscribers" class="loader"></div>
	<div id="subscribers">
	{if !empty($subscribers)}
		
		<table class="indexlist">
		<tr id="orderSubscribers">
			<th></th>
			<th>{$beToolbar->order('newsletter_email', 'Email')}</th>
			<th>{$beToolbar->order('mail_html', 'Html')}</th>
			<th>{$beToolbar->order('mail_status', 'Status')}</th>
			<th>{$beToolbar->order('created', 'Inserted on')}</th>
			<th></th>
		</tr>
		
		{foreach from=$subscribers item="s"}
		<tr>
			<td><input name="objects_selected[]" type="checkbox" class="objectCheck" value="{$s.id}" /></td>
			<td>{$s.newsletter_email}</td>
			<td>{if $s.mail_html}{t}yes{/t}{else}{t}no{/t}{/if}</td>
			<td>{t}{$s.mail_status}{/t}</td>
			<td>{$s.created|date_format:$conf->datePattern}</td>
			<td><a href="{$html->url('/addressbook/view/')}{$s.id}">› {t}details{/t}</a></td>
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
	
	</div>

</fieldset>


<div class="tab"><h2>{t}Operations on{/t} <span class="selecteditems evidence"></span> {t}selected subscribers{/t}</h2></div>
<fieldset id="bulk">
		<select name="operation" style="width:75px">
			<option> {t}copy{/t} </option>
			<option> {t}move{/t} </option>
		</select>
		&nbsp;to:&nbsp;
		<select name="destination">
			{if !empty($groups)}
			{foreach from=$groups item="group"}
				{if $group.MailGroup.id != $object.id}
				<option value="{$group.MailGroup.id}">{$group.MailGroup.group_name}</option>
				{/if}
			{/foreach}
			{/if}
		</select>
		<input id="assocCard" type="button" value=" ok " />
	
	<hr />
	
		{t}change status to:{/t}&nbsp;&nbsp;
		<select style="width:75px" id="newStatus" name="newStatus">
			<option value="valid">{t}valid{/t}</option>
			<option value="blocked">{t}blocked{/t}</option>
		</select>
		<input id="changestatusSelected" type="button" value=" ok " />
	
	<hr />

	<input id="deleteSelected" type="button" value="X {t}Unsubscribe selected items{/t}"/>
</fieldset>


{/if}