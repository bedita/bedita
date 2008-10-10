	
<table class="indexlist">

	<tr>
		<th>{t}sending date{/t}</th>
		<th>{t}status{/t}</th>
		<th>{t}newsletter title{/t}</th>
		<th>{t}template{/t}</th>
		<th>{t}recipients{/t}</th>
		<th>invoice id</th>
	</tr>

	{foreach from=$objects item="obj"}
		<tr rel="{$html->url('/newsletter/view/')}{$obj.id}">
			<td>{$obj.start_sending|date_format:$conf->datePattern}</td>
			<td>{$obj.mail_status}</td>
			<td>{$obj.title}</td>
			<td>
			{if !empty($obj.relations.template)}
				{$obj.relations.template.0.title}
			{/if}
			</td>
			<td></td>
			<td>{$obj.id}</td>
		</tr>
	{foreachelse}
		<tr><td colspan="100">{t}No invoices{/t}</td></tr>
	{/foreach}
			

		
</table>
		


	