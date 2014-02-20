{assign_associative var="optionsPagDisable" style="display: inline;"}
{assign var="pagParams" value=$paginator->params()}

<h2>
	{if $view->action == 'index'}
		{t}System users{/t}
	{elseif $view->action == 'groups'}
		{t}User groups{/t}
	{/if}

	{if $view->SessionFilter->check('query')}

		&nbsp; {t}matching the query containing{/t}: “ <span style="color:white" class="evidence">{$view->SessionFilter->read('query')}</span> ”

	{/if}

</h2>

<table>
	<tr>
		<td style="padding-top:20px;">
			<a href="{$html->url('/')}{$currentModule.url}/view{if $view->action == 'index'}User{elseif $view->action == 'groups'}Group{/if}">{t}Create new{/t} &nbsp;
			{if $view->action == 'index'}
				{t}user{/t}
			{elseif $view->action == 'groups'}
				{t}group{/t}
			{/if}
			</a>
		</td>
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
	</tr>
</table>
