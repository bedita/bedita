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
	$("#groupform", false).validate(); 
});

{/literal}
</script>

<div id="containerPage">
	<div class="FormPageHeader"><h1>{t}Groups admin{/t}</h1></div>
	<div id="mainForm">
		<form action="{$html->url('/admin/saveGroup')}" method="post" name="groupForm" id="groupForm" class="cmxform">
		<table class="indexList">
		<thead>
		<tr>
			<th>{t}Name{/t}</th>
			<th>{t}Access to Backend{/t}</th>	
			<th>{t}Created{/t}</th>
			<th>{t}Modified{/t}</th>
			<th>{t}Actions{/t}</th>
		</tr>
		</thead>
		<tbody>
		{foreach from=$groups|default:'' item=g}
		<tr class="rowList">
			{if $g.Group.immutable}
				<td>{$g.Group.name}</td>
				<td>{if in_array($g.Group.name,$conf->authorizedGroups)}{t}Authorized{/t}{else}{t}Not Authorized{/t}{/if}</td>
				<td>-</td>
				<td>-</td>
				<td>-</td>
			{else}
				<td><a href="{$html->url('/admin/viewGroup/')}{$g.Group.id}">{$g.Group.name}</a></td>
				<td>{if in_array($g.Group.name,$conf->authorizedGroups)}{t}Authorized{/t}{else}{t}Not Authorized{/t}{/if}</td>
				<td>{$g.Group.created}</td>
				<td>{$g.Group.modified}</td>
				<td>
					{if $module_modify eq '1'}
					<input type="button" name="modifyGroup" value="{t}Modify{/t}" onclick="javascript:viewGroup({$g.Group.id});"/>
					<input type="button" name="deleteGroup" value="{t}Remove{/t}" onclick="javascript:delGroupDialog('{$g.Group.name}',{$g.Group.id});"/>
					{else}
					-
					{/if}
				</td>
			{/if}
		</tr>
  		{/foreach}
  		</tbody>
		</table>
				
		<h2 class="showHideBlockButton">{t}Group properties{/t}</h2>
			
		<div class="blockForm" id="errorForm"></div>
		
		<div id="groupForm">
			<fieldset>
				 	{if isset($group)}
					<input type="hidden" name="data[Group][id]" value="{$group.Group.id}"/>
					{/if}
					<span class="label"><label id="lgroupname" for="groupname">{t}Name{/t}</label></span>
					<span class="field">
						<input type="text" id="groupname" name="data[Group][name]" value="{$group.Group.name|default:''}" onkeyup="cutBlank(this);"
							class="{literal}{required:true,lettersnumbersonly:true,minLength:6}{/literal}" title="{t 1='6'}Group name is required (at least %1 chars, no white spaces and special chars){/t}"/>
					</span>
					<span class="status">&#160;</span>
					{if isset($group)}
					<p><b>{t}Users of this group{/t}:</b> {foreach from=$group.User item=u}{$u.userid}&nbsp;{/foreach}
					</p>
					{/if}
			</fieldset>
			<table class="indexList">
			<thead>
			<tr>
				<th>{t}Module{/t}</th>
				<th>{t}No access{/t}</th>
				<th>{t}Read only{/t}</th>
				<th>{t}Read and modify{/t}</th>
			</tr>
			</thead>
			<tbody>
			{foreach from=$modules|default:false item=mod}
			<tr class="rowList" id="tr_{$mod.Module.id}">
				<td><input type="text" readonly="readonly" value="" maxlength="6" style="height:20px; background-color:{$mod.Module.color}; width:60px"/>
						&nbsp;<b>{$mod.Module.label}</b>
				</td>				
				<td>
					<input type="radio" 
						name="data[ModuleFlags][{$mod.Module.label}]" value="" {if !isset($group)}checked="checked"{elseif ($mod.Module.flag == 0)}checked="checked"{/if}/>
				</td>
				<td>
					<input type="radio" name="data[ModuleFlags][{$mod.Module.label}]" value="{$conf->BEDITA_PERMS_READ}" 
							{if ($mod.Module.flag == $conf->BEDITA_PERMS_READ)}checked="checked"{/if}/>
				</td>
				<td>
					<input type="radio" name="data[ModuleFlags][{$mod.Module.label}]" value="{$conf->BEDITA_PERMS_READ_MODIFY}" 
							{if ($mod.Module.flag & $conf->BEDITA_PERMS_MODIFY)}checked="checked"{/if} />
				</td>
			</tr>
			{/foreach}
			{if $module_modify eq '1'}
			<tr>
				<td colspan="4">
					<input type="submit" name="save" class="submit" value="{if isset($group)}{t}Modify{/t}{else}{t}Create group{/t}{/if}" />
				</td> 
			</tr>
			{/if}
			</tbody>
			</table>		
		</div>
		</form>
	</div>
</div>