<script type="text/javascript">
var unplugMessage = "{t}Disable object type will delete all related items. Do you want continue?{/t}";
{literal}
$(document).ready(function() {

	$("#addonsOn input[type=button]").click(function() {
		if (confirm(unplugMessage)) {
			$(this).closest('tr').find('form').submit();
		}
	});
	
})
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="mainfull">
	
	<div class="tab stayopen"><h2>{t}Addons{/t}</h2></div>
	
	<fieldset>
	
	<table class="indexlist" style="float:left; width:49%; margin-right:10px;">
		<tr><th colspan=2>{t}BEdita object type model enabled{/t}</th></tr>

		{if !empty($addons.models.objectTypes.on)}
			<tbody id="addonsOn">
			{foreach from=$addons.models.objectTypes.on item="ot"}
				<tr>
					<td>
					<form action="{$html->url('/admin/disableAddon')}" method="post">
					<input type="hidden" name="path" value="{$ot.path}">
					<input type="hidden" name="model" value="{$ot.model}">
					<input type="hidden" name="file" value="{$ot.file}">
					<input type="hidden" name="type" value="{$ot.type}">
					{$ot.model}
					</form>
					</td>
					<td>
					<input type="button" value="{t}set OFF{/t}"/> 
					</td>
				</tr>
			{/foreach}
			</tbody>
		{else}
			<tr><td>{t}no items{/t}</td></tr>
		{/if}
	</table>
	
	<table class="indexlist" style="float:left; width:49%">
		<tr><th colspan=2>{t}BEdita object type model disabled{/t}</th></tr>

		{if !empty($addons.models.objectTypes.off)}
			<tbody>
			{foreach from=$addons.models.objectTypes.off item="ot"}
				<tr>
					{if $ot.fileNameUsed}
						<td style="color: red;">{$ot.file}:</td><td style="color: red;">{t}file is already used, please change it to avoid malfunctioning{/t}</td>
					{else}
					<form action="{$html->url('/admin/enableAddon')}" method="post">
						<td>
						<input type="hidden" name="path" value="{$ot.path}">
						<input type="hidden" name="model" value="{$ot.model}">
						<input type="hidden" name="file" value="{$ot.file}">
						<input type="hidden" name="type" value="{$ot.type}">
						{$ot.model}
						</td>
						<td>
						<input type="submit" value="{t}set ON{/t}"/>
						</td>
					</form>
					{/if}
				</tr>
			{/foreach}
			</tbody>
		{else}
			<tr><td>{t}no items{/t}</td></tr>
		{/if}
	</table>
	</fieldset>

	
	<div class="tab stayopen"><h2>{t}Other Models{/t}</h2></div>
	<fieldset>
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
	</fieldset>


	<div class="tab stayopen"><h2>{t}Components{/t}</h2></div>
	<fieldset>
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
		<ul>{t}no items{/t}</ul>
	{/if}
	</fieldset>
		
	<div class="tab stayopen"><h2>{t}Helpers{/t}</h2></div>
	<fieldset>
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
	</fieldset>
	
</div>