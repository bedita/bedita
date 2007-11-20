<script type="text/javascript">
{literal}
$(document).ready(function(){
	validateFrm = $("#groupForm").validate({
		errorLabelContainer: $("#errorForm"),
		rules: {
			"data[Group][name]"		: "required"
		},
		messages: {
{/literal}
			"data[Group][name]"		: "{t}group name required{/t}"
{literal}
		}
	});
});

{/literal}
</script>

<div id="containerPage">
	<div class="FormPageHeader"><h1>{t}Groups admin{/t}</h1></div>
	<div id="mainForm">
		<form action="{$html->url('/admin/saveGroup')}" method="post" name="groupForm" id="groupForm">
		<table class="indexList">
		<thead><tr><th>{t}Id{/t}</th>
					<th>{t}Name{/t}</th>
					<th>{t}Created{/t}</th>
					<th>{t}Modified{/t}</th>
					<th>{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$groups item=g}
		<tr class="rowList">
				<td>{$g.Group.id}</td>
			{if $g.Group.immutable}
				<td>{$g.Group.name}</td>
				<td>-</td>
				<td>-</td>
				<td>-</td>
			{else}
				<td><a href="{$html->url('/admin/viewGroup/')}{$g.Group.id}">{$g.Group.name}</a></td>
				<td>{$g.Group.created}</td>
				<td>{$g.Group.modified}</td>
				<td>
					<a href="{$html->url('/admin/viewGroup/')}{$g.Group.id}">{t}Modify{/t}</a>
					<a href="{$html->url('/admin/removeGroup/')}{$g.Group.id}">{t}Remove{/t}</a>
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
					<p>{t}Name{/t} &nbsp; <input type="text" name="data[Group][name]" title="{t}Insert group name{/t}"
						value="{$group.Group.name}" class="{ldelim}required:true{rdelim}"/>&nbsp;
					</p>
					{if isset($group)}
					<p><b>{t}Users of this group{/t}:</b> {foreach from=$group.User item=u}{$u.userid}&nbsp;{/foreach}
					</p>
					{/if}
			</fieldset>
			<table class="indexList">
			<thead><tr>
						<th>{t}Module{/t}</th>
						<th>{t}No access{/t}</th>
						<th>{t}Read only{/t}</th>
						<th>{t}Read and modify{/t}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$modules item=mod}
					<tr class="rowList" id="{$mod.Module.id}">
						<td><input type="text" readonly="readonly" value="" maxlength="6" style="height:20px; background-color:{$mod.Module.color}; width:60px">
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
			</tbody>
			</table>
			<tr></tr>
			<tr>
			<td colspan="2">
				<input type="submit" name="save" class="submit" value="{if isset($group)}{t}Modify{/t}{else}{t}Create group{/t}{/if}" />
			</td> 
		</tr>
			</table>		
		</div>
		</form>
				
	</div>
</div>