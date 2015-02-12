{if !empty($item)}

<div class="tab"><h2>Subscribers</h2></div>

<fieldset id="divSubscribers">
		
	<div id="loaderListSubscribers" class="loader"></div>
	<div id="subscribers">
	{include file="inc/list_subscribers.tpl"}
	</div>

</fieldset>


<div class="tab"><h2>{t}Operations on{/t} <span class="selecteditems evidence"></span> {t}selected subscribers{/t}</h2></div>
<fieldset id="bulk">
		<select name="operation" style="width:75px">
			<option value="copy"> {t}copy{/t} </option>
			<option value="move"> {t}move{/t} </option>
		</select>
		&nbsp;to:&nbsp;
		<select name="destination">
			{if !empty($groups)}
			{foreach from=$groups item="group"}
				{if $group.MailGroup.id != $item.id}
				<option value="{$group.MailGroup.id}">{$group.MailGroup.group_name|escape}</option>
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

{include file="inc/add_subscribers.tpl"}

{/if}