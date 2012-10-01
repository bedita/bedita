<div class="head">
	
	<div class="toolbar" style="white-space:nowrap">
		
		<h2>all {t}{$title}{/t}</h2>
		
		{assign_associative var="optionsPagDisable" style="display: inline;"}
		{assign var="pagParams" value=$this->Paginator->params()}
		
		{assign_associative var="par" url=$this->BeToolbar->getPassedArgs()}
		{$this->Paginator->options($par)}
		
		<ul>
	
			<li>
			<span class="evidence">{$pagParams.count}&nbsp;</span> {t}mail address{/t}
			</li>
			
			<li>
				{if $this->Paginator->hasPrev()}
					{$this->Paginator->first("page")}
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
			</li>
			
			<li>{$this->Paginator->next('next', null, 'next',$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></li>
			
			<li>{$this->Paginator->prev('prev',null,'prev',$optionsPagDisable)}  <span class="evidence"> &nbsp;</span></li>
			
			<li> 
			
				<form action="{$this->Html->url('/')}{$moduleName}/index{if !empty($sectionSel)}/id:{$sectionSel.id}{/if}" method="post">				
				<span>{t}search{/t}</span> : <span class="evidence"> &nbsp;</span>
				<input type="text" name="searchstring" value="{$stringSearched|default:""}"/>
				
				<input type="submit" value="{t}go{/t}"/>
				</form>
				
			</li>


		</ul>




	</div>

</div> 