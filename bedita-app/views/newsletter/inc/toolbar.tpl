<div class="head">
	
	<div class="toolbar" style="white-space:nowrap">
		
		<h2>all {t}{$title}{/t}</h2>
		
		{assign_associative var="optionsPagDisable" style="display: inline;"}
		{assign var="pagParams" value=$paginator->params()}
		
		{assign_associative var="par" url=$beToolbar->getPassedArgs()}
		{$paginator->options($par)}
		
		<ul>
	
			<li>
			<span class="evidence">{$pagParams.count}&nbsp;</span> {t}mail address{/t}
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
			
			<li> 
			
				<form action="{$html->url('/')}{$moduleName}/index{if !empty($sectionSel)}/id:{$sectionSel.id}{/if}" method="post">				
				<span>{t}search{/t}</span> : <span class="evidence"> &nbsp;</span>
				<input type="text" name="searchstring" value="{$stringSearched|default:""}"/>
				
				<input type="submit" value="{t}go{/t}"/>
				</form>
				
			</li>


		</ul>




	</div>

</div> 