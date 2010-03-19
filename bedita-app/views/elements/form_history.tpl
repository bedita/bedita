<div class="tab"><h2>{t}History{/t}</h2></div>
<fieldset id="history">

{if !empty($object.history)}
<table class="indexlist bordered">	
<tr>
	<th>{t}title{/t}</th>
	<th>{t}editor{/t}</th>
	<th>{t}date{/t}</th>
</tr>
{foreach from=$object.history item=h key=k}
	<tr>
		<td><a href="">{$h.title}</a></td>
		<td>{$h.userModified}</td>
		<tf>{$h.dateModified}</tf>
	</tr>
{/foreach}
</table>
{else}
{t}No history set{/t}
{/if}
</fieldset>
