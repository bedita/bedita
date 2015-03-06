{strip}
<div class="head">
	
	<div class="toolbar" style="white-space:nowrap">
		
		<h2>
			{if !empty($title)}
			
				{$title|escape}
			
			{elseif !empty($sectionSel)}
			
				{t}{$itemName|default:$moduleName}{/t} in “ 
				<span style="color:white" class="evidence">{$sectionSel.title|escape}</span> ”
			
			{elseif !empty($pubSel)}
				
				{t}{$itemName|default:$moduleName}{/t} in “ 
				<span style="color:white" class="evidence">{$pubSel.title|escape}</span> ”
			
			{else}
			
				{t}all {$itemName|default:$moduleName}{/t}
			
			{/if}
			
			
			{if $view->SessionFilter->check('query')}
			
				{if $view->SessionFilter->check('substring')}
					&nbsp; {t}matching the query containing{/t}
				{else}
					&nbsp; {t}matching the query{/t}
				{/if}

				: “ <span style="color:white" class="evidence">{$view->SessionFilter->read('query')}</span> ”
				
			{/if}
			
		</h2>
		
		<table>
		<tr>
			{if ($view->viewVars.module_modify eq '1') && empty($noitem)} 
			<td>
				<a href="{$html->url('/')}{$currentModule.url}/view">{t}Create new{/t} &nbsp;
				{if !empty($itemName)}
					{t}{$itemName}{/t}
				{else}
					{assign var=leafs value=$conf->objectTypes.leafs}
					{assign var=isFirst value=true}
					{foreach from=$conf->objectTypes item=type key=key}	
						{if ( in_array($type.id,$leafs.id) && is_numeric($key) && $type.module_name == $currentModule.name )}
							{if !$isFirst}
							&nbsp;/&nbsp;
							{/if}
							{t}{$type.model|lower}{/t}
							{$isFirst=false}
						{/if}
					{/foreach}
				{/if}
				</a>
			</td>
			{/if}
			<td>
			<span class="evidence">{$beToolbar->size()} &nbsp;</span> {t}{$itemName|default:$moduleName}{/t}
			</td>
			
			<td>
				{$beToolbar->first('page','','page')}
				<span class="evidence"> {$beToolbar->current()} </span> 
				{t}of{/t}  &nbsp;
				<span class="evidence">
					{if ($beToolbar->pages()) > 0}
					{$beToolbar->last($beToolbar->pages(),'',$beToolbar->pages())}
					{else}1{/if}
				</span>
			</td>
			
			<td> {$beToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span></td>

            <td>{$beToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span></td>
            
			
		</tr>
		</table>

	</div>

</div> 
{/strip}
