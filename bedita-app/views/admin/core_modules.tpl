<script type="text/javascript">
$(document).ready(function() {
	$(".mainfull .modules *").unbind("click");
})
</script>

<style>
	.modules INPUT {
		width:105px;
		margin-bottom:5px;
	}
	.modules#plugged {
		margin-bottom:20px;
	}
		
	.modules#unplugged LI {
		background-color:#999 !important;
	}

	.modules.block LI {
		margin-right:10px;
		margin-bottom:10px;
		float:left;
	}
			
	.modules LI.off {
		opacity:0.3;
	}
						
</style>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="mainfull">

{if !empty($moduleList)}
	
	<div class="tab stayopen"><h2>{t}Core modules{/t}</h2></div>

	<ul class="modules block" id="plugged">
	{foreach from=$moduleList key=k item=mod}
		<li class="{$mod.name} {$mod.status}">
			<form action="{$html->url('/admin/toggleModule')}" method="post">
			{$beForm->csrf()}
			{t}{$mod.label}{/t}
			
			{if $mod.name != "admin" && $mod.name != "users"}
				<br/><br/>
				<input type="hidden" value="{$mod.id}" name="data[id]"/>
				<input type="hidden" value="{$mod.name}" name="pluginName"/>
				{if $mod.status == "on"}
					<input type="hidden" value="off" name="data[status]"/>
					<input type="submit" value="{t}turn off{/t}"/>
				{elseif $mod.status == "off"}
					<input type="hidden" value="on" name="data[status]"/>
					<input type="submit" value="{t}turn on{/t}"/>
				{/if}
			{/if}
			</form>
		</li>
	{/foreach}
	</ul>
{/if}	


</div>