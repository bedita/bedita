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

$(document).ready(function(){
	
	$("#permissionsTab").click(function() {
		if (!permissionLoaded) {
			loadUserGroupAjax(urlLoad);
		}
	});

	if ($("#permissionsTab h2").hasClass("open")) {
		loadUserGroupAjax(urlLoad);
	}
	
	$("#cmdAddGroupPerm").click(function() {
		var name = $("#inputAddPermGroup").val();
		var type = "group";
		var perm = $("#selectGroupPermission").val();
		var index = $("#frmCustomPermissions").find("tr[id^='permTR_']:last").prop("id");
		
		if (index == undefined) {
			index = 0;
		} else {
			indexArr = index.split("_");
			index = parseInt(indexArr[1]) + 1;
		}
		
		var htmlBlock = "<tr id=\"permTR_" + index + "\"><td>" + name + "</td>" +
						"<td>" + permissions[perm] + "</td>" + 
						"<td style='text-align: right;'>" +
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
	$("#inputAddPermGroup").load(url, { itype:'group' }, function() {
		$("#loaderug").hide();
		permissionLoaded = true;
	});
}
</script>

{if empty($el)}
 {$el = $object|default:[]}
{/if}

{$relcount = $el.Permission|@count|default:0}
<div class="tab" id="permissionsTab">
	<h2 {if $relcount == 0}class="empty"{/if}>
		{t}Permissions{/t} &nbsp; {if $relcount > 0}<span class="relnumb">{$relcount}</span>{/if}
	</h2>
</div>

<fieldset id="permissions">
<div class="loader" id="loaderug"></div>

<table class="indexlist" border="0" id="frmCustomPermissions">
	<thead>
		<tr>
			<th>{t}name{/t}</th>
			<th>{t}permission{/t}</th>
			<th>&nbsp;</th>
		</tr>
	</thead>

{if !empty($el.Permission)}
	
	{section name=i loop=$el.Permission}
	{assign var="perm" 	value=$el.Permission[i]}
	{assign var="i" 	value=$smarty.section.i.index}
		
		<tr id="permTR_{$i}">
			<td>{$perm.name}</td>
			<td>
			{assign var="objPermReverse" value=$conf->objectPermissions|@array_flip}
			{t}{$objPermReverse[$perm.flag]}{/t}
			</td>
			<td style="text-align: right">
				<input type="hidden" name="data[Permission][{$i}][flag]" value="{$perm.flag}"/>
				<input type="hidden" name="data[Permission][{$i}][switch]" value="{$perm.switch|escape:'quotes'}"/>
				<input type="hidden" name="data[Permission][{$i}][name]" value="{$perm.name|escape:'quotes'}"/>
				<input type="button" name="deletePerms" value=" x "/>
			</td>
		</tr>	
		
	{/section}
{else}
	<tr class="trick">
		<td></td>
		<td></td>
		<td></td>
	</tr>
{/if}
</table>

<table class="" border="0" style="margin-top: 20px" id="selCustomPermissions">
	<tr id="addPermGroupTR" class="ignore">
		<td style="white-space:nowrap">
			<label>{t}add group{/t}</label>: <select data-placeholder="{t}select a group{/t}" id="inputAddPermGroup" name="name"></select>
		</td>

		<td>
			<label>{t}permission{/t}:</label> <select data-placeholder="{t}select a permission type{/t}" id="selectGroupPermission" name="groupPermission">
				<option></option>
				{foreach from=$conf->objectPermissions item="permVal" key="permLabel"}
				<option value="{$permVal}">{t}{$permLabel}{/t}</option>
				{/foreach}
			</select>
		</td>
		
		<td style="text-align: right"><input type="button" id="cmdAddGroupPerm" value=" {t}add{/t} "/></td>
	</tr>
</table>
</fieldset>

