<div class="tab"><h2>{t}Versions{/t}</h2></div>
<fieldset id="history">

{if !empty($object.Version)}
<table class="indexlist">	
<tr>
	<th style="text-align:center; width:20px;">{t}version{/t}</th>
	<th>{t}date{/t}</th>
	<th>{t}editor{/t}</th>
	<th></th>
</tr>
{foreach from=$object.Version|@array_reverse item=h key=k}
	{*<tr class="idtrigger" rel="diff-{$h.revision}">*}
	<tr>
		<td style="text-align:center">
			{$h.revision}
		</td>
		<td>{$h.created|date_format:$conf->dateTimePattern}</td>
		<td>{$h.User.realname|default:''} [ {$h.User.userid|default:''} ]</td>
		<td><a class="modalbutton" rel="{$this->Html->url('/pages/revision')}/{$object.id}/{$h.revision}">  view  </a></td>
	</tr>
	{*
	<tr id="diff-{$h.revision}" style="display:none">
		<td></td>
		<td colspan=3 style="padding:0px;">
			<table class="diff">
			{foreach from=$h.diff|unserialize item=diff key=key}
				<tr><td><b>{$key}</b>:</td><td>{$diff|default:'<i>empty</i>'}</td></tr>
			{/foreach}
			</table>
		</td>
	</tr>
	*}
{/foreach}
</table>
{else}
{t}No versions set{/t}
{/if}


</fieldset>
