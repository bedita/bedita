<div class="head">
	
	<div class="toolbar" style="white-space:nowrap">
		
		<h2>{if !empty($sectionSel)}{t}{$itemName|default:$moduleName}{/t} in “ 
		<span style="color:white" class="evidence">{$sectionSel.title}</span> ”
		{else} {t}all{/t} {t}{$itemName|default:$moduleName}{/t}{/if}</h2>
		
		<table>
		<tr>
			<td>
			<span class="evidence">{$beToolbar->size()}&nbsp;</span> {t}{$itemName|default:$moduleName}{/t}
			</td>
			
			<td>
				{$beToolbar->first('page','','page')}
				<span class="evidence"> {$beToolbar->current()} </span> 
				{t}of{/t} 
				<span class="evidence"> 
					{$beToolbar->last($beToolbar->pages(),'',$beToolbar->pages())}
				</span>
			</td>
			
			<td>{$beToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span></td>
			
			<td> {$beToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span></td>
			
			<td> 
			
				<form action="{$html->url('/')}{$moduleName}/index{if !empty($sectionSel)}/id:{$sectionSel.id}{/if}" method="post">				
				<span>{t}search{/t}</span> : <span class="evidence"> &nbsp;</span>
				<input type="text" name="searchstring" value="{$stringSearched|default:""}"/>
				
				<input type="submit" value="{t}go{/t}"/>
				</form>
				
			</td>

		</tr>
		</table>

	</div>

</div> 

