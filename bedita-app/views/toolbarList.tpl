{*
file include.
visualizza i comandi di navigazione negli elenchi.
*}
{php}
$vs = &$this->get_template_vars() ;
//pr($vs["toolbar"]);
//exit;
{/php}
{assign var="pagineTotali" value=$toolbar.pages}

{if $sez == "menuSX"}
<hr>
		<span style="white-space:nowrap">
                {if $toolbar.first eq 0}pagina {else} <a title="vai alla prima pagina"  href="{$selfPlus}&amp;page={$toolbar.first}">pagina</a>{/if}

				<select name="page" id="toolbarPageSX" onChange="document.location ='{$selfPlus}&amp;page='+this[this.selectedIndex].value">
                {section name="s" loop=$pagineTotali}
	                <option {if $smarty.section.s.iteration==$smarty.get.page}selected{/if} value="{$smarty.section.s.iteration}">{$smarty.section.s.iteration}</option>
                {/section}
                </select>
                 di <b><a title="vai all'ultima pagina"  href="{$selfPlus}&amp;page={$pagineTotali}">{$pagineTotali}</a></b>
                </span>
                <hr>
                {if $toolbar.next}<b>&#8250; </b><a title="pagina successiva" href="{$selfPlus}&amp;page={$toolbar.next}">avanti</a>{else}{/if}
                
                
                <br>
                {if $toolbar.prev}<b>&#8250;</b> <a title="pagina precedente" href="{$selfPlus}&amp;page={$toolbar.prev}">indietro</a>{else}{/if}
 
	<hr>

{elseif $sez == "menuCentro"}

<div class="gest_MenuHeader">
 <span style="white-space:nowrap">
	Totali:&nbsp; {$toolbar.size} &nbsp;&nbsp;| &nbsp;&nbsp;
	{if $toolbar.first eq 0}pagina 1 {else} <a title="vai alla prima pagina"  href="{$selfPlus}&amp;page={$toolbar.first}">{$toolbar.page} pagina</a>{/if} <b>{$page}</b> di 
	<b>{if $toolbar.last eq 0} {$toolbar.pages} {else} <a title="vai all'ultima pagina"  href="{$selfPlus}&amp;page={$toolbar.last}">{$toolbar.last}</a> {/if}</b>
	&nbsp;&nbsp;
	{if $toolbar.next}<b>&#8250; </b><a title="pagina successiva" href="{$selfPlus}&amp;page={$toolbar.next}">avanti</a>{else}{/if}
	&nbsp;&nbsp;
	{if $toolbar.prev}<b>&#8250;</b> <a title="pagina precednte" href="{$selfPlus}&amp;page={$toolbar.prev}">indietro</a>{else}{/if}
	&nbsp;&nbsp;
	Dimensioni: <select name="dim" onChange="document.location ='{$selfPlus}&amp;dim='+this[this.selectedIndex].value">
    <option {if $dim == 10}selected{/if} value="10">10</option>
    <option {if $dim == 20}selected{/if} value="20">20</option>
    <option {if $dim == 50}selected{/if} value="50">50</option>
    <option {if $dim == 100}selected{/if} value="100">100</option>
	</select>	
	&nbsp;&nbsp;
	<form style="display:inline; " action="{$selfPlus}&amp;page=1">	
		cerca:&nbsp;&nbsp;
		<input type="text" style=" font-size:10px;" name="ricerca" value="{$ricerca}" maxlength="100" style="width:110px;">
		&nbsp;&nbsp;<input type="submit" style="font-size:10px;" value="invia">
	</form>
</span>
</div>
{/if}
