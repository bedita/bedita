<form action="{$html->url('/admin/saveGroup')}" method="post" name="groupForm" id="groupForm" class="cmxform">

<script type="text/javascript">
{literal}
$(document).ready(function(){

	$(".indexlist TD").not(".checklist").not(".go").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	
	{/literal}{if !empty($group)}{literal}
	
		$('.tab').BEtabsopen();
	
	{/literal}{/if}{literal}
	
});

{/literal}
//-->
</script>	
{assign var='p_name' value=$tr->t('name',true)}
{assign var='p_modified' value=$tr->t('modified',true)}
<table class="indexlist">
	<tr>
		<th>{$paginator->sort($p_name,'name')}</th>
		<th>{t}Access to Backend{/t}</th>
		<th>{$paginator->sort($p_modified,'modified')}</th>
		<th></th>
	</tr>
	{foreach from=$groups|default:'' item=g}
	<tr class="rowList {if ($g.Group.id == $group.Group.id)}on{/if}">	
		<td><a href="{$html->url('/admin/viewGroup/')}{$g.Group.id}">{$g.Group.name}</a></td>
		<td>{if $g.Group.backend_auth}{t}Authorized{/t}{else}{t}Not Authorized{/t}{/if}</td>
		{if $g.Group.immutable}	
		<td>-</td>
		<td>-</td>
		<td>-</td>
		{else}
		<td>{$g.Group.modified}</td>
		<td class="go">{if ($module_modify eq '1')}
			<input type="button" name="deleteGroup" value="{t}Remove{/t}" 
			onclick="javascript:delGroupDialog('{$g.Group.name}',{$g.Group.id});"/>
			{else}-{/if}
		</td>
		{/if}
	</tr>
  	{/foreach}
</table>


<div class="tab"><h2>{$group.Group.name|default:'New'|upper} {t}group properties {/t}</h2></div>
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
				{/if}
					<td>
						<input type="checkbox" name="data[Group][backend_auth]" value="1"
							{if isset($group) && $group.Group.backend_auth == 1} checked="checked"{/if} /> {t}Access to Backend{/t}
					</td>
				</tr>
		</table>
</fieldset>				


<div class="tab"><h2>{$group.Group.name|default:'New'|upper} {t}group modules access{/t}</h2></div>

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

</form>
