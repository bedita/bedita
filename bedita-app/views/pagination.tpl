{* set array for helper *}
{assign var="par" value=$html->params}
{assign var="pagParams" value=$paginator->params()}
{assign var="pagParamsFiltered" value=$beurl->filterPaginatorParams()}
{assign_associative var="optionsPag" class="pagEnable" url=$beurl->filterPaginatorParams()}
{assign_associative var="passFirst" page="1"}
{assign_associative var="passLast" page=$pagParams.pageCount}

{assign_concat var="page" 0="pagina " 1=$pagParams.page}

{if $sez == "menuSX"}
	<hr/>
	<span style="white-space:nowrap">
        
		{if !$paginator->hasPrev()}pagina {else} {$paginator->link('pagina', $passFirst, $optionsPag)}{/if}
        
		<select name="page" id="toolbarPageSX" onChange="document.location ='{$html->here}' + '/page:' + this.value">
		{section name="s" loop=$pagParams.pageCount}
		    <option {if $smarty.section.s.iteration==$pagParams.page}selected{/if} value="{$smarty.section.s.iteration}">{$smarty.section.s.iteration}</option>
		{/section}
		</select>
		di <b>{$paginator->link($pagParams.pageCount, $passLast, $optionsPag)}</b>
	</span>
    
    <hr/>
    {if $paginator->hasNext()}<b>&#8250;</b>{/if}
    {$paginator->next('avanti', $optionsPag, null)}
    
    {if $paginator->hasPrev()}<b>&#8250;</b>{/if}
	{$paginator->prev('indietro', $optionsPag, null)}
    
	<hr/>    

{elseif $sez == "menuCentro"}

	<div class="gest_MenuHeader">
		<div style="white-space:nowrap">
		Totali:&nbsp;
		{$pagParams.count}
		&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
		{if $paginator->hasPrev()}
			{$paginator->link($page, $passFirst, $optionsPag)}
		{else}
			{$page}
		{/if}
		di
		{if $paginator->hasNext()}
			<b>{$paginator->link($pagParams.pageCount, $passLast, $optionsPag)}</b>
		{else}
			<b>{$pagParams.pageCount}</b>
		{/if}
		
		&nbsp;&nbsp;
		{if $paginator->hasNext()}<b>&#8250;</b>{/if}
		
		{$paginator->next('avanti', $optionsPag, null)}
		
		&nbsp;&nbsp;
		{if $paginator->hasPrev()}<b>&#8250;</b>{/if}
		{$paginator->prev('indietro', $optionsPag, null)}
		
		&nbsp;&nbsp;Dimensioni:
		<select name="limit" onchange="javascript: location.href='{$html->here}{if !strstr($html->here,'/index')}/index{/if}' + '/page:1/limit:' + this.value;">
			<option value="10"{if $pagParams.options.limit == 10} selected{/if}>10</option>
			<option value="20"{if $pagParams.options.limit == 20} selected{/if}>20</option>
			<option value="50"{if $pagParams.options.limit == 50} selected{/if}>50</option>
			<option value="100"{if $pagParams.options.limit == 100} selected{/if}>100</option>
		</select>
		
		&nbsp;&nbsp;

		<form style="display: inline;" action="{$html->here}/page:1}">	
			cerca:&nbsp;&nbsp;
			<input type="text" style="width:110px; font-size:10px;" name="ricerca" value="{if !empty($ricerca)}{$ricerca}{/if}" maxlength="100"/>
			&nbsp;&nbsp;<input type="submit" style="font-size:10px;" value="invia"/>
		</form>	

		</div>
		
	</div>
{/if}