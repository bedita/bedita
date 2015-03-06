<table class="indexlist js-header-float">
	<thead>
		<tr>
			<th>{t}name{/t}</th>
			<th>{t}publication{/t}</th>
			<th>{t}sender{/t}</th>
			<th>Id</th>
		</tr>
	</thead>

	{if !empty($objects)}		
	{foreach from=$objects item="template"}

	<tr rel="{$html->url('/newsletter/viewMailTemplate/')}{$template.id}">

		<td>
			{$template.title|escape}
		</td>
		<td>
			{$template.Area.title|escape}							
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
	
	<tr><td colspan="100">{t}No templates found{/t}</td></tr>
	
{/if}

</table>