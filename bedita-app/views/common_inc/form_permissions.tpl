<!-- disabled permissisons -->
{*

tutto disabilitato in atesa di tempi migliori
vedi: 
https://dev.channelweb.it/bedita/ticket/156
https://dev.channelweb.it/bedita/ticket/157

*}

<script type="text/javascript">
var urlLoad = "{$html->url('/pages/loadUsersGroupsAjax')}";
var permissionLoaded = false;
var permissions = new Array();
permissions[{$conf->OBJ_PERMS_READ_FRONT}] = "{t}frontend access{/t}";
permissions[{$conf->OBJ_PERMS_WRITE}] = "{t}write{/t}";

{literal}
$(document).ready(function(){
	
	$("#permissionsTab").click(function() {
		if (!permissionLoaded) {
			loadUserGroupAjax(urlLoad);
		}
	});

	if ($("#permissionsTab h2").attr("class") == "open documents") {
		loadUserGroupAjax(urlLoad);
	}
	
	$("#cmdAddGroupPerm").click(function() {
		var name = $("#inputAddPermGroup").val();
		var type = "group";
		var perm = $("#selectGroupPermission").val();
		var index = $("#frmCustomPermissions").find("tr[id^='permTR_']:last").attr("id");
		
		if (index == undefined) {
			index = 0;
		} else {
			indexArr = index.split("_");
			index = parseInt(indexArr[1]) + 1;
		}
		
		var htmlBlock = "<tr id=\"permTR_" + index + "\"><td>" + name + "</td>" +
						"<td>" + permissions[perm] + "</td>" + 
						"<td>" +
						"<input type=\"hidden\" name=\"data[Permissions]["+index+"][flag]\" value=\""+perm+"\"/>" + 
						"<input type=\"hidden\" name=\"data[Permissions]["+index+"][switch]\" value=\""+type+"\"/>" +
						"<input type=\"hidden\" name=\"data[Permissions]["+index+"][name]\" value=\""+name+"\"/>" +
						"<input type=\"button\" name=\"deletePerms\" value=\" x \"/>"+
						"</td></tr>";
		
		$("#frmCustomPermissions").find("tr:last").after(htmlBlock);
		refreshRemovePermButton();
	});

	refreshRemovePermButton();
});

function refreshRemovePermButton() {
	$("#frmCustomPermissions").find("input[type='button']").click(function() {
		$(this).parents("tr").remove();
	});
}

function loadUserGroupAjax(url) {
	$("#loaderug").show();
	$("#inputAddPermGroup").load(url, {itype:'group'}, function() {
		$("#loaderug").hide();
		permissionLoaded = true;
	});
}

{/literal}
</script>



<div class="tab" id="permissionsTab"><h2>{t}Permissions{/t}</h2></div>
<fieldset id="permissions">
<div class="loader" id="loaderug"></div>

<table class="indexlist" id="frmCustomPermissions">
<tr>
	<th>{t}name{/t}</th>
	<th>{t}permission{/t}</th>
	<th>&nbsp;</th>
</tr>
{if !empty($el.Permissions)}
	{section name=i loop=$el.Permissions}
	{assign var="perm" 	value=$el.Permissions[i]}
	{assign var="i" 	value=$smarty.section.i.index}
		
		<tr id="permTR_{$i}">
			<td>
				{$perm.name}
			</td>
			<td>
				{if $perm.flag == $conf->OBJ_PERMS_READ_FRONT}
					{t}frontend access{/t}
				{elseif $perm.flag == $conf->OBJ_PERMS_WRITE}
					{t}write{/t}
				{/if}
			</td>
			<td>
				<input type="hidden" name="data[Permissions][{$i}][flag]" value="{$perm.flag}"/>
				<input type="hidden" name="data[Permissions][{$i}][switch]" value="{$perm.switch|escape:'quotes'}"/>
				<input type="hidden" name="data[Permissions][{$i}][name]" value="{$perm.name|escape:'quotes'}"/>
				<input type="button" name="deletePerms" value=" x "/>
			</td>
		</tr>	
		
	{/section}

{/if}
</table>

<table class="indexlist" id="selCustomPermissions">
<tr>
	<th>{t}add group{/t}:</th>
	<th>{t}permission{/t}</th>
	<th>&nbsp;</th>
</tr>

<tr id="addPermGroupTR">
	<td style="white-space:nowrap">
		<select id="inputAddPermGroup" name="name"></select>
	</td>

	<td>
		<select id="selectGroupPermission" name="groupPermission">
			<option value="{$conf->OBJ_PERMS_READ_FRONT}">{t}frontend access{/t}</option>
			<option value="{$conf->OBJ_PERMS_WRITE}">{t}write{/t}</option>
		</select>
	</td>
	
	<td><input type="button" id="cmdAddGroupPerm" value=" {t}add{/t} "/></td>
</tr>
</table>
</fieldset>

