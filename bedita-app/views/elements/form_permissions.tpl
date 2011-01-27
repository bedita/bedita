{*

https://dev.channelweb.it/bedita/ticket/156
https://dev.channelweb.it/bedita/ticket/157

*}

<script type="text/javascript">
var urlLoad = "{$html->url('/pages/loadUsersGroupsAjax')}";
var permissionLoaded = false;
var permissions = new Array();
{foreach from=$conf->objectPermissions key="permKey" item="permItem"}
	permissions[{$permItem}] = "{t}{$permKey}{/t}";
{/foreach}

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
						"<input type=\"hidden\" name=\"data[Permission]["+index+"][flag]\" value=\""+perm+"\"/>" + 
						"<input type=\"hidden\" name=\"data[Permission]["+index+"][switch]\" value=\""+type+"\"/>" +
						"<input type=\"hidden\" name=\"data[Permission]["+index+"][name]\" value=\""+name+"\"/>" +
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

<table class="indexlist" border=0 id="frmCustomPermissions">
{if !empty($el.Permission)}
<tr>
	<th style="width:190px">{t}name{/t}</th>
	<th style="width:190px">{t}permission{/t}</th>
	<th>&nbsp;</th>
</tr>

	{section name=i loop=$el.Permission}
	{assign var="perm" 	value=$el.Permission[i]}
	{assign var="i" 	value=$smarty.section.i.index}
		
		<tr id="permTR_{$i}">
			<td>{$perm.name}</td>
			<td>
			{assign var="objPermReverse" value=$conf->objectPermissions|@array_flip}
			{t}{$objPermReverse[$perm.flag]}{/t}
			</td>
			<td>
				<input type="hidden" name="data[Permission][{$i}][flag]" value="{$perm.flag}"/>
				<input type="hidden" name="data[Permission][{$i}][switch]" value="{$perm.switch|escape:'quotes'}"/>
				<input type="hidden" name="data[Permission][{$i}][name]" value="{$perm.name|escape:'quotes'}"/>
				<input type="button" name="deletePerms" value=" x "/>
			</td>
		</tr>	
		
	{/section}
{else}
<tr>
	<th style="width:190px"></th>
	<th style="width:190px"></th>
	<th>&nbsp;</th>
</tr>
{/if}
</table>

<table class="indexlist" border=0 id="selCustomPermissions">
<tr>
	<th style="width:190px">{t}add group{/t}:</th>
	<th style="width:190px">{t}permission{/t}</th>
	<th>&nbsp;</th>
</tr>

<tr id="addPermGroupTR">
	<td style="white-space:nowrap">
		<select id="inputAddPermGroup" name="name"></select>
	</td>

	<td>
		<select id="selectGroupPermission" name="groupPermission">
			{foreach from=$conf->objectPermissions item="permVal" key="permLabel"}
			<option value="{$permVal}">{t}{$permLabel}{/t}</option>
			{/foreach}
		</select>
	</td>
	
	<td><input type="button" id="cmdAddGroupPerm" value=" {t}add{/t} "/></td>
</tr>
</table>
</fieldset>

