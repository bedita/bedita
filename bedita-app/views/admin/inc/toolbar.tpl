
	{assign_associative var="optionsPagDisable" style="display: inline;"}
	{assign var="pagParams" value=$paginator->params()}
<table>
	<tr>
		<td>
		<span class="evidence">{$pagParams.count}&nbsp;</span> {t}{$label_items}{/t}
		</li>
		<td>
			{if $paginator->hasPrev()}
				{$paginator->first("page")}
			{else}
				{t}page{/t}
			{/if} 
			<span class="evidence"> {$paginator->current()}</span>
			{t}of{/t} 
			<span class="evidence"> 
			{if $paginator->hasNext()}
				{$paginator->last($pagParams.pageCount)}
			{else}
				{$paginator->current()}
			{/if}
			</span>
		</td>
		<td>{$paginator->next('next', null, 'next',$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></td>
		<td>{$paginator->prev('prev',null,'prev',$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></td>
	</tr>
</table>
