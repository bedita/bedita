{*
file include.
visualizza l'albero delle aree e dei gruppi per la selezione
di porzioni di contenuti.
*}


{assign var="par" value=$html->params}
{assign_concat var="url_index" 0='/' 1 =$par.controller 2='/index/'}

{section name=a loop=$Groups}
	{assign var="area" 		value=$Groups[a].Area}
	{assign var="subaree" 	value=$Groups[a].Groups}
	
	<h2 
	onClick="window.location='{$html->url($url_index)}{$area.id}'" 
	style="{if $area.id == $ida}background-color:#3399CC; {/if}; cursor:pointer;">
		{$area.name}
	</h2>
	<div id="div{$area.id}" 
	{if $area.id != $ida || !empty($hideGroups)}
		style="display:none"
	{/if}>
	
	{if $subaree}
		<ul class="simpleMenuList" style="padding:0px; margin:0px;">
			{*<li {if !$smarty.get.currentArea}class="on"{/if}>	<b>&#8250;</b> <a href="{$self}&amp;ida={$IDarea}">tutte le sezioni</a></li>*}
			{section name="u" loop=$subaree}
				<li {if ($subaree[u].id == $idg)}class="on"{/if}>	<b>&#8250;</b> 
				<a href="{$html->url($url_index)}{$area.id}/{$subaree[u].id}">{$subaree[u].name}</a></li>
			{/section}
		</ul>	
	{/if}
	</div>
{/section}

