<div class="modulesmenucaption">go to: &nbsp;<a>be</a></div>

<ul class="modulesmenu">
		<li title="{t}help{/t}" class="helptrigger">?</li>
{strip}
{foreach from=$moduleListInv key=k item=mod}
{if ($mod.status == 'on')}
	{assign_concat var='link' 1=$html->url('/') 2=$mod.url}
	<li rel="{$link}" title="{t}{$mod.label}{/t}" class="{$mod.name} {if ($mod.name == $moduleName)} on{/if}"></li>
{/if}
{/foreach}

    <li rel="{$html->url('/')}" title="{t}Bedita3 main dashboard{/t}" class="bedita"></li>

{/strip}
</ul>