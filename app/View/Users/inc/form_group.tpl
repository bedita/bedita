<form action="{$this->Html->url('/users/saveGroup')}" method="post" name="groupForm" id="groupForm" class="cmxform">

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
<div class="tab"><h2>{$group.User|@count|default:''} {t}users in this group{/t}</h2></div>

<table class="bordered">
{foreach from=$group.User item=u}
	<tr>
		<td>
			<a href="{$this->Html->url('/users/viewUser/')}{$u.id}">{$u.userid}</a>
		</td>
	</tr>
{/foreach}
</table>
{/if}		

</form>