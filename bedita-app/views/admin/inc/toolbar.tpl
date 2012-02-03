<script type="text/javascript">
	var refreshUrl = "{$html->here}";
	$(document).ready(function() { 
		$("#pageDim").bind("change", function() {
			if(refreshUrl.match(/admin$/)) {
				refreshUrl += "/systemEvents";
			}
			document.location = refreshUrl + "/limit:" + this.value;
		} );
	} );
</script>

	{assign_associative var="optionsPagDisable" style="display: inline;"}
	{assign var="pagParams" value=$paginator->params()}
<table>
	<tr>
		<td>
		<span class="evidence">{$pagParams.count}&nbsp;</span> {t}{$label_items}{/t}
		</li>
		{assign var='label_page' value=$tr->t('page',true)}
		<td>
			{if $paginator->hasPrev()}
				{$paginator->first($label_page)}
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
		{assign var='label_next' value=$tr->t('next',true)}
		{assign var='label_prev' value=$tr->t('prev',true)}
		<td>{$paginator->next($label_next,null,$label_next,$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></td>
		<td>{$paginator->prev($label_prev,null,$label_prev,$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></td>
		<td>
			{t}Page size{/t}:
			<select name="dim" id="pageDim">
				<option value="5"{if $pagParams.options.limit == 5} selected="selected"{/if}>5</option>
				<option value="10"{if $pagParams.options.limit == 10} selected="selected"{/if}>10</option>
				<option value="20"{if $pagParams.options.limit == 20} selected="selected"{/if}>20</option>
				<option value="50"{if $pagParams.options.limit == 50} selected="selected"{/if}>50</option>
				<option value="100"{if $pagParams.options.limit == 100} selected="selected"{/if}>100</option>
			</select>
		</td>
	</tr>
</table>
