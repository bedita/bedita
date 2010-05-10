<div>
	<table class="bordered">
	<thead>
		<tr>
			<td colspan=5>
				{t}Version{/t} <b>{$version.revision}</b> / <b>{$totRevision}</b>, {$version.created|date_format:$conf->dateTimePattern}, {t}created by{/t} <b>{$user.realname|default:''} [ {$user.userid|default:''} ]</b>
				<input type="button" class="BEbutton" style="margin-left:10px" value="ripristina" /> 
				<input type="button" class="BEbutton" style="margin-left:10px" value="elimina" /> 
			</td>
		</tr>
	</thead>
	<tbody>
	{foreach from=$diff item=xdiff key=key}
		<tr>
			<td><b>{$key}</b></td>
			<td>{$revision[$key]|default:'<i>empty</i>'}</td>
			{*<td>{$diff}</td>*}
		</tr>
	{/foreach}
	</tbody>
	</table>



</div>