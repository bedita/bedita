<script type="text/javascript">
var unplugMessage = "{t}Disable object type will delete all related items. Do you want continue?{/t}";
{literal}
$(document).ready(function() {
		
	$("#addonsOn input[type=button]").click(function() {
		if (confirm(unplugMessage)) {
			$(this).parents("form").submit();
		}
	});
	
})
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="mainfull">
	
	<h1>{t}Addons{/t}</h1>
	
	<h2 style="margin-top: 20px;">{t}BEdita object type model enabled{/t}</h2>
	{if !empty($addons.models.objectTypes.on)}
		<ul id="addonsOn">
		{foreach from=$addons.models.objectTypes.on item="ot"}
			<li>
				<form action="{$html->url('/admin/disableAddon')}" method="post">
				<input type="hidden" name="path" value="{$ot.path}">
				<input type="hidden" name="model" value="{$ot.model}">
				<input type="hidden" name="file" value="{$ot.file}">
				<input type="hidden" name="type" value="{$ot.type}">
				{$ot.model}
				<input type="button" value="{t}OFF{/t}"/> 
				</form>
			</li>
		{/foreach}
		</ul>
	{else}
		{t}no items{/t}
	{/if}
	
	<h2 style="margin-top: 20px;">{t}BEdita object type model disabled{/t}</h2>
	{if !empty($addons.models.objectTypes.off)}
		<ul>
		{foreach from=$addons.models.objectTypes.off item="ot"}
			<li>
				{if $ot.fileNameUsed}
					<span style="color: red;">{$ot.file}: {t}file is already used, please change it to avoid malfunctioning{/t}</span>
				{else}
					<form action="{$html->url('/admin/enableAddon')}" method="post">
					<input type="hidden" name="path" value="{$ot.path}">
					<input type="hidden" name="model" value="{$ot.model}">
					<input type="hidden" name="file" value="{$ot.file}">
					<input type="hidden" name="type" value="{$ot.type}">
					{$ot.model}
					<input type="submit" value="{t}ON{/t}"/>
				{/if}
			</li>
		{/foreach}
		</ul>
	{else}
		{t}no items{/t}
	{/if}
	
	<h2 style="margin-top: 20px;">{t}Other Models{/t}</h2>
	{if !empty($addons.models.others)}
		<ul>
		{foreach from=$addons.models.others item="m"}
			<li>
			file:
			{if $m.fileNameUsed}
				<span style="color: red;">{$m.file}: {t}file is already used, please change it to avoid malfunctioning{/t}</span>
			{else}
				{$m.file}
			{/if}
			<br/>
			path: {$m.path}<br/>
			</li>
		{/foreach}
		</ul>
	{else}
		{t}no items{/t}
	{/if}
	
	<h2 style="margin-top: 20px;">{t}Components{/t}</h2>
	{if !empty($addons.components)}
		<ul>
		{foreach from=$addons.components item="c"}
			<li>
			file:
			{if $c.fileNameUsed}
				<span style="color: red;">{$c.file}: {t}file is already used, please change it to avoid malfunctioning{/t}</span>
			{else}
				{$c.file}
			{/if}
			<br/>
			path: {$c.path}<br/>
			</li>
		{/foreach}
		</ul>
	{else}
		{t}no items{/t}
	{/if}
	
	<h2 style="margin-top: 20px;">{t}Helpers{/t}</h2>
	{if !empty($addons.helpers)}
		<ul>
		{foreach from=$addons.components item="h"}
			<li>
			file:
			{if $h.fileNameUsed}
				<span style="color: red;">{$h.file}: {t}file is already used, please change it to avoid malfunctioning{/t}</span>
			{else}
				{$h.file}
			{/if}
			<br/>
			path: {$h.path}<br/>
			</li>
		{/foreach}
		</ul>
	{else}
		{t}no items{/t}
	{/if}

</div>