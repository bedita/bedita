{assign_associative var="optionsPagDisable" style="display: inline;"}
{assign var="pagParams" value=$this->Paginator->params()}

<h2>
	{if $view->action == 'index'}
		{t}System users{/t}
	{elseif $view->action == 'groups'}
		{t}User groups{/t}
	{/if}
	
	{if !empty($stringSearched)}
		&nbsp; {t}matching the query{/t}: “ <span style="color:white" class="evidence">{$stringSearched}</span> ”
	{/if}
</h2>

<table>
	<tr>
		<td style="padding-top:20px;">
			<a href="{$this->Html->url('/')}{$currentModule.url}/view{if $view->action == 'index'}User{elseif $view->action == 'groups'}Group{/if}">{t}Create new{/t} &nbsp;
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
		{assign var='label_page' value=$this->Tr->t('page',true)}
		<td>
			{if $this->Paginator->hasPrev()}
				{$this->Paginator->first($label_page)}
			{else}
				{t}page{/t}
			{/if} 
			<span class="evidence"> {$this->Paginator->current()}</span>
			{t}of{/t} 
			<span class="evidence"> 
			{if $this->Paginator->hasNext()}
				{$this->Paginator->last($pagParams.pageCount)}
			{else}
				{$this->Paginator->current()}
			{/if}
			</span>
		</td>
		{assign var='label_next' value=$this->Tr->t('next',true)}
		{assign var='label_prev' value=$this->Tr->t('prev',true)}
		<td>{$this->Paginator->next($label_next,null,$label_next,$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></td>
		<td>{$this->Paginator->prev($label_prev,null,$label_prev,$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></td>
	</tr>
</table>
