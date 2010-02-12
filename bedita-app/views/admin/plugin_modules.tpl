<script type="text/javascript">
var unplugMessage = "{t}Unplugging the module will delete all related items. Do you want continue?{/t}";
{literal}
$(document).ready(function() {
	$(".modules *").unbind("click");
	
	$("#plugged input[type=button]").click(function() {
		var form = $(this).parents("form");
		form.attr("action", $(this).attr("rel"));
		if ($(this).attr("id") == "unplugButton") {
			if (confirm(unplugMessage)) {
				form.submit();
			}
		} else {
			form.submit();
		}
	});
})
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="mainfull">
	
	<h2>{t}Plugged modules{/t}</h2>
	
	<ul class="modules" id="plugged">
	{foreach from=$pluginModules.plugged item="mod"}
		<li class="{$mod.name}">
			<form action="{$html->url('/admin/plugModule')}" method="post">
			{t}{$mod.label}{/t}
			
			<br/><br/>
			<input type="hidden" value="{$mod.id}" name="data[id]"/>
			<input type="hidden" value="{$mod.name}" name="pluginName"/>
			<input type="hidden" value="{$mod.info.pluginPath}" name="pluginPath"/>
			{if $mod.status == "on"}
				<input type="hidden" value="off" name="data[status]"/>
				<input type="button" rel="{$html->url('/admin/toggleModule')}" value="{t}turn off{/t}"/>
			{elseif $mod.status == "off"}
				<input type="hidden" value="on" name="data[status]"/>
				<input type="button" rel="{$html->url('/admin/toggleModule')}" value="{t}turn on{/t}"/>
			{/if}
			<br/><br/>
			<input type="button" id="unplugButton" rel="{$html->url('/admin/unplugModule')}" value="{t}plug-out{/t}"/>
			</form>
		</li>
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
			<input type="submit" value="{t}plug-in{/t}"/>
			</form>
		</li>
	{/foreach}
	</ul>

</div>