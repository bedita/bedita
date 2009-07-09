<div>
<table class="editorheader ultracondensed" style="width:100%">
<tr>
	<td class="author">{$note.UserCreated.realname|default:$note.UserCreated.userid|default:$note.creator|default:$note.user_created}</td>
	<td class="date">{$note.created|date_format:$conf->dateTimePattern}</td>
	<td>
	{if $note.user_created == $BEAuthUser.id}
		<input type="button" rel="{$note.id}" style="font-size:9px !important; padding:0px 0px 0px 0px" name="deletenote" value="x" />
	{/if}
	</td>
</tr>
</table>
<p class="editornotes">{$note.description}</p>
</div>
