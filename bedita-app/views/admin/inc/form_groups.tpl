<script type="text/javascript">
{literal}

$.validator.setDefaults({ 
	//submitHandler: function() { alert("submitted!"); },
	success: function(label) {
		// set &nbsp; as text for IE
		label.html("&nbsp;").addClass("checked");
		}
	});
jQuery.validator.addMethod(
		"lettersnumbersonly",
		function(value, element) { return /^[a-z0-9]+$/i.test(value); },
		"{/literal}{t}Letters or numbers only please{/t}{literal}");
$(document).ready(function() {
	$("#groupform").validate(); 
});

{/literal}
</script>



<table class="indexlist">
	<tr>
		<th>{$paginator->sort('Name', 'name')}</th>
		<th>{t}Access to Backend{/t}</th>
		<th>{$paginator->sort('Created', 'created')}</th>
		<th>{$paginator->sort('Modified', 'modified')}</th>
		<th></th>
	</tr>
	{foreach from=$groups|default:'' item=g}
	<tr class="rowList" rel="{$html->url('/admin/viewGroup/')}{$g.Group.id}">	
		<td>{$g.Group.name}</td>
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
					<td><input type="text" id="groupname" name="data[Group][name]" value="{$group.Group.name|default:''}" onkeyup="cutBlank(this);"
							class="{literal}{required:true,lettersnumbersonly:true,minLength:6}{/literal}" title="{t 1='6'}Group name is required (at least %1 chars, no white spaces and special chars){/t}"/>
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


