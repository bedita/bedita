<table class="editorheader ultracondensed" style="width:100%">
<tr>
	<td class="author">{$note.UserCreated.realname|default:$note.UserCreated.userid|default:$note.creator|default:$note.user_created}</td>
	<td class="date">{$note.created|date_format:$conf->dateTimePattern}</td>
</tr>
</table>
<p class="editornotes">{$note.description}</p>