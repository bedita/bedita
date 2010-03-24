<div class="tab"><h2>{t}History{/t}</h2></div>
<fieldset id="history">

{if !empty($object.Version)}
<table class="indexlist">	
<tr>
	<th style="text-align:center; width:20px;">{t}version{/t}</th>
	<th>{t}date{/t}</th>
	{*<th>diff</th>*}
	<th>{t}editor{/t}</th>
</tr>
{foreach from=$object.Version|@array_reverse item=h key=k}
	<tr>
		<td style="text-align:center"><a href="">{$h.revision}</a></td>
		<td>{$h.created|date_format:$conf->dateTimePattern}</td>
		{*<td>{$h.diff}</td>*}
		<td>[{$h.user_id}]</td>
	</tr>
{/foreach}
</table>
{else}
{t}No history set{/t}
{/if}

{*{dump var=$object.Version}*}

</fieldset>
