<div class="toolbar" style="white-space:nowrap">
	{assign_associative var="optionsPagDisable" style="display: inline;"}
	{assign var="pagParams" value=$paginator->params()}
	<ul>
		<li>
		<span class="evidence">{$pagParams.count}&nbsp;</span> {t}{$label_items}{/t}
		</li>
		<li>
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
		</li>
		<li>{$paginator->next('next', null, 'next',$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></li>
		<li>{$paginator->prev('prev',null,'prev',$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></li>
	</ul>
</div>