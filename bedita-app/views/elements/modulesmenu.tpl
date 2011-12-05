<div class="modulesmenucaption">go to: &nbsp;<a>be</a></div>

<nav class="modulesmenu">
	
		
		
		<a title="{t}search{/t}" class="searchtrigger"></a>
		
		<!-- <a title="{t}help{/t}" class="helptrigger">?</a> -->
{strip}
{foreach from=$moduleListInv key=k item=mod}
{if ($mod.status == 'on')}
	{assign_concat var='link' 1=$html->url('/') 2=$mod.url}
	<a href="{$link}" title="{t}{$mod.label}{/t}" class="{$mod.name} {if ($mod.name == $moduleName)} on{/if}"></a>
{/if}
{/foreach}

    <a href="{$html->url('/')}" title="{t}Bedita3 main dashboard{/t}" class="bedita"></a>

{/strip}
</nav>

	<form class="searchobjects" {if !empty($stringSearched)}style="display:block"{/if} 	action="{$html->url('/')}{$moduleName}/index{if !empty($sectionSel)}/id:{$sectionSel.id}{/if}" method="post">					
	<input type="text" placeholder="{t}search{/t}" name="searchstring" value="{$stringSearched|default:""}"/>
	<input type="submit" value="{t}GO{/t}"/>
	</form>
	
	
	
	
	{$view->element('modulesmenu_dyn')}