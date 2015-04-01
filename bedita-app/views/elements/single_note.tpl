<div>
<table class="editorheader ultracondensed" style="width:100%">
<tr>
	<td class="author">{$note.UserCreated.realname|default:$note.UserCreated.userid|default:$note.creator|default:$note.user_created|escape}</td>
	<td class="date">{$note.created|date_format:$conf->dateTimePattern}</td>
</tr>
</table>
<p class="editornotes">{$note.description|nl2br|escape}</p>
{if $note.user_created == $BEAuthUser.id}
	<input type="button" rel="{$note.id}" 
	style="font-size:9px !important; text-transform:lowercase; margin:0px 0px 0px 120px;" 
	name="deletenote" value="{t}delete{/t}" />
{/if}
</div>