<form action="{$html->url('/users/saveGroup')}" method="post" name="groupForm" id="groupForm" class="cmxform">
<style scoped>
	table.group_objects {
		margin-bottom:10px;
	}
	table.group_objects TR > TD:first-child {
		padding-left:30px;
		background: url("{$html->url('/')}img/iconLocked.png") center left no-repeat;
		background-size: 28px;
	}
</style>

<div class="tab"><h2>{t}group properties {/t}</h2></div>
<fieldset id="groupForm">
		{if !empty($group)}<input type="hidden" name="data[Group][id]" value="{$group.Group.id}"/>{/if}				
		<table>
			<tr>
				<th><label id="lgroupname" for="groupname">{t}Group Name{/t}</label></th>
				<td><input {if (!empty($group) && $group.Group.immutable == 1)}disabled=disabled{/if} style="width:300px;" type="text" id="groupname" name="data[Group][name]" value="{$group.Group.name|default:''}" onkeyup="cutBlank(this);"/>
				</td>
				<td>
					<input {if (!empty($group) && $group.Group.immutable == 1)}disabled=disabled{/if} type="checkbox" name="data[Group][backend_auth]" value="1"
						{if isset($group) && $group.Group.backend_auth == 1} checked="checked"{/if} /> {t}Access to Backend{/t}
				</td>
			</tr>
		</table>
</fieldset>				

<div class="tab"><h2>{t}group modules access{/t}</h2></div>

<fieldset id="modulesaccess">	

	<table class="bordered">		
		<tr>
			<th>{t}Module{/t}</th>
			<th>{t}No access{/t}</th>
			<th>{t}Read only{/t}</th>
			<th>{t}Read and modify{/t}</th>
		</tr>	
		{foreach from=$modules|default:false item=mod}
		<tr class="rowList" id="tr_{$mod.Module.id}">
			<td>
				<div style="float:left; vertical-align:middle; margin:0px 10px 0px -10px; width:20px;" class="{$mod.Module.url}">
				&nbsp;</div>
				{$mod.Module.label}
			</td>				
			<td class="center">
				<input type="radio" {if (!empty($group) && $group.Group.immutable == 1)}disabled=disabled{/if}
					name="data[ModuleFlags][{$mod.Module.name}]" value="" {if !isset($group)}checked="checked"{elseif ($mod.Module.flag == 0)}checked="checked"{/if}/>
			</td>
			<td class="center">
				<input type="radio" {if (!empty($group) && $group.Group.immutable == 1)}disabled=disabled{/if}
					name="data[ModuleFlags][{$mod.Module.name}]" value="{$conf->BEDITA_PERMS_READ}" 
						{if ($mod.Module.flag == $conf->BEDITA_PERMS_READ)}checked="checked"{/if}/>
			</td>
			<td class="center">
				<input type="radio" {if (!empty($group) && $group.Group.immutable == 1)}disabled=disabled{/if}
				name="data[ModuleFlags][{$mod.Module.name}]" value="{$conf->BEDITA_PERMS_READ_MODIFY}" 
						{if ($mod.Module.flag & $conf->BEDITA_PERMS_MODIFY)}checked="checked"{/if} />
			</td>
		</tr>
		{/foreach}
	</table>
</fieldset>
{if !empty($group)}

	{$objPermReverse = $conf->objectPermissions|@array_flip}
	<div class="tab"><h2>{$group.objects|@count|default:''} {t}objects for this group{/t}</h2></div>
	<fieldset>
	<table class="smallist group_objects bordered">
		<tr>
			<th>{t}title{/t}</th>
			<th nowrap>{t}type{/t}</th>
			<th>{t}status{/t}</th>
			<th colspan="2">{t}permission type{/t}</th>
		</tr>
		
		{$view->element('form_group_permissions', ['objsRelated' => $group.objects])}
		
	</table>

	<br />

	<input type="button" title="{t}Select objects for group '{$group.Group.name|default:''}'{/t}" rel="/pages/showObjects/group:{$group.Group.id}" class="modalbutton" 
	value="{t}{if !empty($group.objects)}protect more objects{else}add objects to group{/if}{/t}" />
	<!-- {* TODO adding preview objects or hidden or simple, uff *} -->
	</fieldset>


	<div class="tab"><h2>{$group.User|@count|default:''} {t}users in this group{/t}</h2></div>
	<table class="bordered">
	{foreach $group.User as $u}
		<tr>
			<td>
				<a href="{$html->url('/users/viewUser/')}{$u.id}">{$u.userid}</a>
			</td>
		</tr>
	{/foreach}
	</table>

{/if}		

</form>