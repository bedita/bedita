<script type="text/javascript">
var unplugMessage = "{t}Unplugging the module will delete all related items. Do you want continue?{/t}";
{literal}
$(document).ready(function() {
	$(".mainfull .modules *").unbind("click");
	
	$("#plugged input[type=button]").click(function() {
		var form = $(this).parents("form");
		form.prop("action", $(this).attr("rel"));
		if ($(this).prop("id") == "unplugButton") {
			if (confirm(unplugMessage)) {
				form.submit();
			}
		} else {
			form.submit();
		}
	});
})
</script>

<style>
	.modules INPUT {
		width:105px;
		margin-bottom:5px;
	}
	.modules#plugged, .modules#unplugged {
		margin-bottom:20px;
		overflow:auto;
	}
		
	.modules#unplugged LI {
		background-color:#999 !important;
	}

	.modules.block LI {
		margin-right:10px;
		margin-bottom:10px;
		float:left;
	}

	.modules.block LI form {
		margin-top:5px;
	}

	.modules LI.off {
		opacity:0.3;
	}
						
</style>
{/literal}


{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="mainfull">

{if empty($pluginModules.plugged) && empty($pluginModules.unplugged)}

	<div class="tab stayopen"><h2>{t}No plugins found on filesystem{/t}</h2></div>
	<h3>{t}Please check{/t}: {$pluginDir}</h3>

{/if}

{if !empty($pluginModules.plugged)}
	
	<div class="tab stayopen"><h2>{t}Plugged modules{/t}</h2></div>

	<ul class="modules block" id="plugged">
	{foreach from=$pluginModules.plugged item="mod"}
		<li class="{$mod.name} {$mod.status}">
			{t}{$mod.label}{/t}
			<form action="{$html->url('/admin/plugModule')}" method="post">
			{$beForm->csrf()}
			<input type="hidden" value="{$mod.id}" name="data[id]"/>
			<input type="hidden" value="{$mod.name}" name="pluginName"/>
			{if $mod.status == "on"}
				<input type="hidden" value="off" name="data[status]"/>
				<input type="button" rel="{$html->url('/admin/toggleModule')}" value="{t}turn off{/t}"/>
			{elseif $mod.status == "off"}
				<input type="hidden" value="on" name="data[status]"/>
				<input type="button" rel="{$html->url('/admin/toggleModule')}" value="{t}turn on{/t}"/>
			{/if}
			<input type="button" id="unplugButton" rel="{$html->url('/admin/unplugModule')}" value="{t}plug-out{/t}"/>
			</form>
		</li>
	{/foreach}
	</ul>
{/if}	

{if !empty($pluginModules.unplugged)}
	
	<div class="tab stayopen"><h2>{t}Unplugged modules{/t}</h2></div>
	
	<ul class="modules block" id="unplugged">
	{foreach from=$pluginModules.unplugged item="mod"}
		<li>
			{t}{$mod.publicName}{/t}<div style="font-size:.9em">version {$mod.version}</div>
			<form action="{$html->url('/admin/plugModule')}" method="post">
			{$beForm->csrf()}
			<input type="hidden" value="{$mod.pluginName}" name="pluginName"/>
			<input type="submit" value="{t}plug-in{/t}"/>
			</form>
		</li>
	{/foreach}
	</ul>

{/if}

</div>