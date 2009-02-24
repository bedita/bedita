

<table class="indexlist">
	<tr>
		<th>{$paginator->sort('Name', 'name')}</th>
		<th>{t}Access to Backend{/t}</th>
		<th>{$paginator->sort('Created', 'created')}</th>
		<th>{$paginator->sort('Modified', 'modified')}</th>
		<th></th>
	</tr>
	{foreach from=$groups|default:'' item=g}
	<tr class="rowList">	
		<td><a href="{$html->url('/admin/viewGroup/')}{$g.Group.id}">{$g.Group.name}</a></td>
		<td>{if in_array($g.Group.name,$conf->authorizedGroups)}{t}Authorized{/t}{else}{t}Not Authorized{/t}{/if}</td>
		{if $g.Group.immutable}	
		<td>-</td>
		<td>-</td>
		<td>-</td>
		{else}
		<td>{$g.Group.created}</td>
		<td>{$g.Group.modified}</td>
		<td>{if ($module_modify eq '1')}
			<input type="button" name="deleteGroup" value="{t}Remove{/t}" 
			onclick="javascript:delGroupDialog('{$g.Group.name}',{$g.Group.id});"/>
			{else}-{/if}
		</td>
		{/if}
	</tr>
  	{/foreach}
</table>

<div class="tab"><h2>{t}Group properties {/t}</h2></div>

<fieldset id="groupForm">	

	{if isset($group)}
		<input type="hidden" name="data[Group][id]" value="{$group.Group.id}"/>
	{/if}
					
		<table class="bordered">
				<tr>
					<th><label id="lgroupname" for="groupname">{t}Group Name{/t}</label></th>
					<td><input type="text" id="groupname" name="data[Group][name]" value="{$group.Group.name|default:''}" onkeyup="cutBlank(this);"/>
					</td>
			{if isset($group)}
					<td>
						{if ($module_modify eq '1')}
							<input type="button" name="deleteGroup" value="{t}Remove{/t}" 
							onclick="javascript:delGroupDialog('{$group.Group.name}',{$group.Group.id});"/>
						{/if}
					</td>

				</tr>
				<tr>
					<th>{t}Users of this group{/t}</th>
					<td>
							
						{foreach from=$group.User item=u}
							<a href="{$html->url('/admin/viewUser/')}{$u.id}">
								{$u.userid} / 	
							</a>
						{/foreach}
					</td>
				</tr>
			{/if}
		</table>
		
</fieldset>				


<div class="tab"><h2>{t}Group modules access{/t}</h2></div>

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
					<div style="float:left; vertical-align:middle; margin:0px 10px 0px -10px; width:20px;" class="{$mod.Module.path}">
					&nbsp;</div>
					
					{$mod.Module.label}
					

				</td>				
				<td class="center">
					<input type="radio" 
						name="data[ModuleFlags][{$mod.Module.name}]" value="" {if !isset($group)}checked="checked"{elseif ($mod.Module.flag == 0)}checked="checked"{/if}/>
				</td>
				<td class="center">
					<input type="radio" name="data[ModuleFlags][{$mod.Module.name}]" value="{$conf->BEDITA_PERMS_READ}" 
							{if ($mod.Module.flag == $conf->BEDITA_PERMS_READ)}checked="checked"{/if}/>
				</td>
				<td class="center">
					<input type="radio" name="data[ModuleFlags][{$mod.Module.name}]" value="{$conf->BEDITA_PERMS_READ_MODIFY}" 
							{if ($mod.Module.flag & $conf->BEDITA_PERMS_MODIFY)}checked="checked"{/if} />
				</td>
			</tr>
			{/foreach}

			</table>
		
</fieldset>


