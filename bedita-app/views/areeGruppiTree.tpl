{*
file include.
visualizza l'albero delle aree e dei gruppi per la selezione
di porzioni di contenuti.
*}

{section name=a loop=$Groups}
	{assign var="area" 		value=$Groups[a].Area}
	{assign var="subaree" 	value=$Groups[a].Groups}
	<h2 
	onClick="window.location='{$self}&amp;ida={$area.id}'" 
	style="{if ($area.id == $ida || $subaree)}background-color:#3399CC; {/if}; cursor:pointer;">
		{$area.name}
	</h2>	
	<div id="{$area.id}Div" 
	{if !($subaree)}
		style="display:none"
	{/if}>
	
	{if ($subaree)}
		<ul class="simpleMenuList" style="padding:0px; margin:0px;">
			{*<li {if !$smarty.get.currentArea}class="on"{/if}>	<b>&#8250;</b> <a href="{$self}&amp;ida={$IDarea}">tutte le sezioni</a></li>*}
			{section name="u" loop=$subaree}
				<li {if ($subaree[u].id == $idg)}class="on"{/if}>	<b>&#8250;</b> 
				<a href="{$self}&amp;ida={$area.id}&amp;idg={$subaree[u].id}">{$subaree[u].name}</a></li>
			{/section}
		</ul>	
	{/if}
	</div>
{/section}

