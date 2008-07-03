<div class="head">
	
	<div class="toolbar" style="white-space:nowrap">
		
		<h2>{if !empty($sectionSel)}{t}{$moduleName}{/t} in “ <span style="color:white" class="evidence">{$sectionSel.title}</span> ”
		{else} all {$moduleName}{/if}</h2>
		
		<ul>
	
			<li>
			<span class="evidence">{$beToolbar->size()}&nbsp;</span> {t}{$moduleName}{/t}
			</li>
			
			<li>
				{$beToolbar->first('page','','page')}
				<span class="evidence"> {$beToolbar->current()} </span> 
				{t}of{/t} 
				<span class="evidence"> 
					{$beToolbar->last($beToolbar->pages(),'',$beToolbar->pages())}
				</span>
			</li>
			
			<li>{$beToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span></li>
			
			<li> {$beToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span></li>
			
			<li>
			
				<form action="{$html->url('/')}{$moduleName}/index{if !empty($sectionSel)}/id:{$sectionSel.id}{/if}" method="post">				
				search : &nbsp;&nbsp;<input type="text" name="searchstring" value="{$stringSearched|default:""}"/>
				
				<input type="submit" value="{t}go{/t}"/>
				</form>
				
			</li>


		</ul>




	</div>

</div> 

