

	
	<table class="indexlist">

		<tr>
			<th>{t}name{/t}</th>
			<th>{t}publication{/t}</th>
			<th>{t}sender{/t}</th>
			<th>Id</th>
		</tr>

		{if !empty($objects)}		
		{foreach from=$objects item="template"}

			<tr rel="{$html->url('/newsletter/viewMailTemplate/')}{$template.id}">

				<td>
					{$template.title}
				</td>
				<td>
					{$template.Area.title}							
				</td>
				<td>
					{$template.sender|default:null}
				</td>
				<td>
					{$template.id}
				</td>
			</tr>
			
			
		{/foreach}
		{else}
			
			<tr><td colspan="100" style="padding: 30px;">{t}No templates found{/t}</td></tr>
			
		{/if}
		
		</table>
		


	