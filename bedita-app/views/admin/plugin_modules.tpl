{literal}
<script type="text/javascript">
$(document).ready(function() {
	$(".modules *").unbind("click");
})
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="mainfull">
	
	<h2>{t}Plugged modules{/t}</h2>
	
	<ul class="modules">
	{foreach from=$pluginModules.plugged item="mod"}
		<li class="{$mod.name}">{t}{$mod.label}{/t}</li>
	{/foreach}
	</ul>
	
	<h2>{t}Unplugged modules{/t}</h2>
	
	<ul class="modules">
	{foreach from=$pluginModules.unplugged item="mod"}
		<li>
			<form action="{$html->url('/admin/plugModule')}" method="post">
			{t}{$mod.publicName}{/t}<br/>
			version {$mod.version}<br/><br/>
			<input type="hidden" value="{$mod.pluginPath}" name="pluginPath"/>
			<input type="hidden" value="{$mod.pluginName}" name="pluginName"/>
			<input type="submit" value="{t}attiva{/t}"/>
			</form>
		</li>
	{/foreach}
	</ul>

</div>