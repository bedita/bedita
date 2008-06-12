<div class="head">
	
	<div class="toolbar">
		
		<h2>{if !empty($sectionSel)}{t}{$moduleName}{/t} in “ <span style="color:white" class="evidence">{$sectionSel.title}</span> ”
		{else} all {$moduleName}{/if}</h2>
		
		<ul>
	
			<li>
			<span class="evidence">14&nbsp;</span> <a href="">{t}{$moduleName}{/t}</a>
			</li>
			
			<li>
				{t}page{/t}
				<span class="evidence"> 1 </span> 
				{t}of{/t} 
				<span class="evidence"> 12 </span>
			</li>
			
			<li>next <span class="evidence"> &nbsp;</span></li>
			
			<li> prev <span class="evidence"> &nbsp;</span></li>
			
			<li>
			
				<form action="{$html->url('/')}{$moduleName}/index{if !empty($sectionSel)}/id:{$sectionSel.id}{/if}" method="post">				
				search : &nbsp;&nbsp;<input type="text" name="searchstring" value="{$stringSearched|default:""}"/>
				
				<input type="submit" value="{t}go{/t}"/>
				</form>
				
			</li>
			{*
			
			{$beToolbar->current()}
			{$beToolbar->size()}
			{$beToolbar->pages()}
			
			{$beToolbar->first()} 
			{$beToolbar->prev()}  
			{$beToolbar->next()} 
			{$beToolbar->last()}
			
			<li>{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}</li>
			
			<li>{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;</li>
			*}
		</ul>

	</div>

</div> 
