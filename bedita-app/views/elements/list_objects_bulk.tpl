{strip}
{if !empty($objects)}
<div style="white-space:nowrap">
	<input type="checkbox" class="selectAll" id="selectAll"/>
	&nbsp;<label for="selectAll">{t}(un)select all{/t}</label>
	&nbsp;&nbsp;&nbsp;
	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	&nbsp;{t}of{/t}&nbsp;
	{if ($beToolbar->pages()) > 0}
	{$beToolbar->last($beToolbar->pages(),'',$beToolbar->pages())}
	{else}1{/if}
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')}
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	{$beToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span>	
	|&nbsp;&nbsp;&nbsp;
	{$beToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span>
</div>

<br />

<div class="tab">
	<h2>{t}Operations on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2>
</div>
<div>
	{t}change status to:{/t}&nbsp;
	<select style="width:75px" id="newStatus" name="newStatus">
		<option value=""> -- </option>
		{html_options options=$conf->statusOptions}
	</select>
	&nbsp;
	<input id="changestatusSelected" type="button" value=" ok " />
	<hr />	
	<input id="deleteSelected" type="button" value="{t}Delete selected items{/t}"/>
</div>
{/if}
{/strip}