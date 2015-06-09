<div class="modulesmenutop">
	<div class="modulesmenucaption">go to: &nbsp;<a>be</a></div>

	<nav class="modulesmenu">
		<a title="{t}search{/t}" class="searchtrigger"></a>
		<a title="{t}id or unique name{/t}" class="searchbyidtrigger"></a>
	{strip}
	{if !empty($moduleListInv)}
	{foreach from=$moduleListInv key=k item=mod}
	{if ($mod.status == 'on')}
		{assign_concat var='link' 1=$html->url('/') 2=$mod.url}
		<a href="{$link}" title="{t}{$mod.label}{/t}" class="{$mod.name|default:''} {if ($mod.name == $moduleName|default:'')} on{/if}"></a>
	{/if}
	{/foreach}
	{/if}
	    <a href="{$html->url('/')}" title="{t}Bedita3 main dashboard{/t}" class="bedita"></a>
	{/strip}
	</nav>

	<!-- {* searchDestination default is "index". Fot different defaults set $params.searchDestination in various modules views.tpl *} -->
	<form class="searchobjects" {if !empty($stringSearched)}style="display:block"{/if} 	action="{$html->url('/')}{$moduleName|default:''}/{$searchDestination|default:'index'}{if !empty($sectionSel)}/id:{$sectionSel.id}{/if}" method="post">
		{$beForm->csrf()}
	{if $substringSearch|default:true}
		<input type="checkbox" {if !$view->SessionFilter->check() || $view->SessionFilter->check('substring')}checked="checked"{/if} id="substring" name="filter[substring]" /> {t}substring{/t}
	{/if}
		<input type="text" placeholder="{t}search{/t} {$searchDestination|default:''}" name="filter[query]" value="{$view->SessionFilter->read('query')}"/>
		<input type="submit" value="{t}GO{/t}"/>
	</form>

	<!-- {* search by id or nickname (unique name) *} -->
	<form style="visibility: hidden" class="searchobjectsbyid" action="{$html->url('/')}pages/gotoObjectById" method="get">
		<input type="text" placeholder="{t}id or unique name{/t}" name="objectId" value=""/>
		<input type="submit" value="{t}GO{/t}"/>
	</form>

</div>
{$view->element('modulesmenu_dyn')}