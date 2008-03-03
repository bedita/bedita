<h2 class="showHideBlockButton">{t}Permissions{/t}</h2>
<div class="blockForm" id="permissions">

<script type="text/javascript">
{literal}
/*
Script per la gestione dei permessi
*/

// Utilizzato per la definizione dell'ID delle righe permessi
var postfix_customProp = "_permTR" ;

/**
* Indica l'incremento massimo raggiunto nell'elencazione dei permessi
*/

var maxIDPerms = {/literal}{if empty($maxIDPerms)}0{else}{$maxIDPerms}{/if}{literal} ;

$(document).ready(function(){
{/literal}
	{section name=i loop=$el.Permissions}
		_setupPermTR("{$el.Permissions[i].name}_{$el.Permissions[i].switch}"+postfix_customProp) ;
		maxIDPerms++ ;
		if ($("#inputAddPermUser").containsOption("{$el.Permissions[i].name}")) 
			$("#inputAddPermUser").removeOption("{$el.Permissions[i].name}");	
		if ($("#inputAddPermGroup").containsOption("{$el.Permissions[i].name}")) 
			$("#inputAddPermGroup").removeOption("{$el.Permissions[i].name}");		
	{/section}
{literal}

	$('input[@name=cmdAddUserPerm]', "#frmCustomPermissions").bind("click", function (e) {
		fncAddPermsTR('addPermUserTR') ;
	}) ;
	$('input[@name=cmdAddGroupPerm]', "#frmCustomPermissions").bind("click", function (e) {
		fncAddPermsTR('addPermGroupTR') ;
	}) ;
	$("#addPermUserTR :checkbox").bind("click", function() {
		if ($("#addPermUserTR :checkbox:checked").length > 0) {
			$("#addPermUserTR").find('label').remove();
		}
	});
	$("#addPermGroupTR :checkbox").bind("click", function() {
		if ($("#addPermGroupTR :checkbox:checked").length > 0) {
			$("#addPermGroupTR").find('label').remove();
		}
	});

});


var htmlTdInputHiddenEmpty = "<td><input type=\"hidden\" name=\"none\"\/><\/td>";
var htmlTdCheckboxRead="<td><input type=\"checkbox\" name=\"none\" value=\"{/literal}{$conf->BEDITA_PERMS_READ}{literal}\"\/><\/td>";
var htmlTdCheckboxModify="<td><input type=\"checkbox\" name=\"none\" value=\"{/literal}{$conf->BEDITA_PERMS_MODIFY}{literal}\"\/><\/td>";
var htmlTdCheckboxDelete="<td><input type=\"checkbox\" name=\"none\" value=\"{/literal}{$conf->BEDITA_PERMS_DELETE}{literal}\"\/><\/td>";
var htmlTdSubmit="<td><input type=\"button\" name=\"delete\" value=\" x \"\/><\/td>";
// Procedura per l'aggiunta di un permesso
var htmlTemplateCustomPerm = '<tr>' 
+ htmlTdInputHiddenEmpty
+ htmlTdInputHiddenEmpty 
+ htmlTdCheckboxRead
+ htmlTdCheckboxModify
+ htmlTdCheckboxDelete
+ htmlTdSubmit
+ "<\/tr>";

function fncAddPermsTR(id) {
	
	var name = $("#"+id+" TD select[@name=name]").val();
	var _switch 	= $("#"+id+" TD input[@name=switch]").fieldValue()[0] ;
	var read 		= $("#"+id+" TD input[@name=read]").get(0).checked ;
	var modify 		= $("#"+id+" TD input[@name=modify]").get(0).checked ;
	var _delete 	= $("#"+id+" TD input[@name=delete]").get(0).checked ;

	// Se non completa esce
	if(!name || (!(read || modify || _delete))) {
		$("#"+id+" TD:last").find("label").remove();
		$("#"+id+" TD:last").append("<label class='error'>{/literal}{t}Select at least one type of permission{/t}{literal}</label>")
		return false ;
	} else {
		$("#"+id+" TD:last").find("label").remove();
	}
	
	// Inserisce il nuovo elemento
	var newTR = $("#endLineCustomPermsTR").before(htmlTemplateCustomPerm).prev() ;

	// Setup nomi, id e comandi degli elementi
	newTR.attr("id", name+postfix_customProp) ;
	$("TD:nth-child(1) input", newTR).attr("name", "data[Permissions]["+maxIDPerms+"][name]") ;
	$("TD:nth-child(2) input", newTR).attr("name", "data[Permissions]["+maxIDPerms+"][switch]") ;
	$("TD:nth-child(3) input", newTR).attr("name", "data[Permissions]["+maxIDPerms+"][BEDITA_PERMS_READ]") ;
	$("TD:nth-child(4) input", newTR).attr("name", "data[Permissions]["+maxIDPerms+"][BEDITA_PERMS_MODIFY]") ;
	$("TD:nth-child(5) input", newTR).attr("name", "data[Permissions]["+maxIDPerms+"][BEDITA_PERMS_DELETE]") ;
	$('TD:nth-child(6) input[@name=delete]', newTR).bind("click", function (e) { deleteTRPerm(this)}) ;

	// setup dei valori
	$("TD:nth-child(1) input", newTR).attr("value", name) ;
	$("TD:nth-child(1)", newTR).append(name) ;
	$("TD:nth-child(2) input", newTR).attr("value", _switch) ;
	$("TD:nth-child(2)", newTR).append(_switch) ;
	$("TD:nth-child(3) input", newTR).get(0).checked = read ;
	$("TD:nth-child(4) input", newTR).get(0).checked = modify ;
	$("TD:nth-child(5) input", newTR).get(0).checked = _delete ;

	// remove selected element from select tag
	$("#"+id+" TD select[@name=name]").removeOption(name);
	if ($("#"+id+" TD select[@name=name]").selectedValues().length == 0) {
		$("#"+id+" TD input[@type=button]").attr("disabled","disabled");
		$("#"+id+" TD select[@name=name]").attr("disabled","disabled");
		$("#"+id+" :checkbox").attr("disabled","disabled");
	}

	// Resetta i campi
	$("#"+id+" TD input").not($("#"+id+" TD input[@name=switch]")).clearFields() ;

	// Indica l'avvenuto cambiamento dei dati
	try {
		$().alertSignal() ;
	} catch(e) {}

	incrementCounterPerms() ;
}

function incrementCounterPerms() {
	maxIDPerms++ ;
}

// Setta i comandi per la gestione delle righe della tabella dei permessi
function _setupPermTR(id) {
	// Definisce il comando per la cancellazione
	$('#'+id+' input[@name=delete]').bind("click", function (e) {
		deleteTRPerm(this)
	}) ;
}

// cancella l'elemento
function deleteTRPerm(el) {
	if(!confirm("{/literal}{t}Do you really want to delete the permission{/t}{literal}?")) return false ;
	var trToDelete = $(el).parents("tr");
	var valueToaddToSelect = $("TD:nth-child(1) input", trToDelete).val();
	var type = $("TD:nth-child(2) input", trToDelete).val();
	if (type == "user") {
		selectElement = $("#inputAddPermUser");
	} else if (type == "group") {
		selectElement = $("#inputAddPermGroup");
	}
	selectElement.addOption(valueToaddToSelect, valueToaddToSelect);
	selectElement.sortOptions();
	// enable add button, checkbox and select
	selectElement.parents("tr").find("input[@type=button]").attr("disabled","");
	selectElement.attr("disabled","");
	selectElement.parents("tr").find(":checkbox").attr("disabled","");
	trToDelete.remove();

	// Indica l'avvenuto cambiamento dei dati
	try {
		$().alertSignal() ;
	} catch(e) {}
}

{/literal}
</script>

<fieldset>
<table class="tableForm" border="0" id="frmCustomPermissions">
<tr>
	<td class="label" style="text-align:left;">{t}name{/t}</td>
	<td class="label" style="text-align:left;">{t}type{/t}</td>
	<td class="label" style="text-align:left;">{t}read{/t}</td>
	<td class="label" style="text-align:left;">{t}modify{/t}</td>
	<td class="label" style="text-align:left;">{t}delete{/t}</td>
	<td class="label">&nbsp;</td>
</tr>
{section name=i loop=$el.Permissions}
{assign var="perm" 	value=$el.Permissions[i]}
{assign var="i" 	value=$smarty.section.i.index}
	{if !($perm.name == "administrator" && $perm.switch == 'group')}
	<tr id="{$perm.name}_{$perm.switch}_permTR">
		<td><input type="hidden" name="data[Permissions][{$i}][name]" value="{$perm.name|escape:'quotes'}"/>{$perm.name}</td>
		<td><input type="hidden" name="data[Permissions][{$i}][switch]" value="{$perm.switch|escape:'quotes'}"/>{$perm.switch}</td>
		<td><input type="checkbox" name="data[Permissions][{$i}][BEDITA_PERMS_READ]" value="{$conf->BEDITA_PERMS_READ}" {if ($perm.flag & $conf->BEDITA_PERMS_READ)}{literal}checked="checked"{/literal}{/if}/></td>
		<td><input type="checkbox" name="data[Permissions][{$i}][BEDITA_PERMS_MODIFY]" value="{$conf->BEDITA_PERMS_MODIFY}" {if ($perm.flag & $conf->BEDITA_PERMS_MODIFY)}{literal}checked="checked"{/literal}{/if}/></td>
		<td><input type="checkbox" name="data[Permissions][{$i}][BEDITA_PERMS_DELETE]" value="{$conf->BEDITA_PERMS_DELETE}" {if ($perm.flag & $conf->BEDITA_PERMS_DELETE)}{literal}checked="checked"{/literal}{/if}/></td>
		<td><input type="button" name="delete" value=" x "/></td>
	</tr>
	{else}
	<tr id="{$perm.name}_{$perm.switch}_permTR">
		<td>
		<input type="hidden" name="data[Permissions][{$i}][name]" value="{$perm.name|escape:'quotes'}"/>{$perm.name}
		<input type="hidden" name="data[Permissions][{$i}][BEDITA_PERMS_READ]" value="{$conf->BEDITA_PERMS_DELETE}" />
		<input type="hidden" name="data[Permissions][{$i}][BEDITA_PERMS_MODIFY]" value="{$conf->BEDITA_PERMS_MODIFY}" />
		<input type="hidden" name="data[Permissions][{$i}][BEDITA_PERMS_DELETE]" value="{$conf->BEDITA_PERMS_DELETE}" />
		</td>
		<td><input type="hidden" name="data[Permissions][{$i}][switch]" value="{$perm.switch|escape:'quotes'}"/>{$perm.switch}</td>
		<td><input type="checkbox" disabled="1" name="permr" value="{$conf->BEDITA_PERMS_READ}" {if ($perm.flag & $conf->BEDITA_PERMS_READ)}{literal}checked="checked"{/literal}{/if}/></td>
		<td><input type="checkbox" disabled="1" name="permw" value="{$conf->BEDITA_PERMS_MODIFY}" {if ($perm.flag & $conf->BEDITA_PERMS_MODIFY)}{literal}checked="checked"{/literal}{/if}/></td>
		<td><input type="checkbox" disabled="1" name="permd" value="{$conf->BEDITA_PERMS_DELETE}" {if ($perm.flag & $conf->BEDITA_PERMS_DELETE)}{literal}checked="checked"{/literal}{/if}/></td>
		<td></td>
	</tr>	
	{/if}
{/section}
{if (isset($recursion) && !empty($recursion))}
<tr id="endLineCustomPermsTR">
	<td colspan="4">
		<input type="checkbox"  name="data[recursiveApplyPermissions]" id="recursiveApplyPermissions" value="1"/>&nbsp;
		<a href="javascript:void(0)" onclick="$('#recursiveApplyPermissions').toggleCheck() ;">
		{t}Apply permissions recursively{/t}
		</a>
	</td>
</tr>
{/if}
<tr><td colspan="8"><hr/></td></tr>
<tr><th colspan="8" style="text-align:left ;">{t}user{/t}</th></tr>
<tr id="addPermUserTR">
	<td>
		<select id="inputAddPermUser" name="name">
		{foreach from=$usersList key="id_user" item="username"}
			<option value="{$username}">{$username}</option>
		{/foreach}
		</select>
	</td>
	<td><input type="hidden" name="switch" value="{t}user{/t}"/></td>
	<td><input type="checkbox" name="read" value="{$conf->BEDITA_PERMS_READ}"/></td>
	<td><input type="checkbox" name="modify" value="{$conf->BEDITA_PERMS_MODIFY}"/></td>
	<td><input type="checkbox" name="delete" value="{$conf->BEDITA_PERMS_DELETE}"/></td>
	<td><input type="button" name="cmdAddUserPerm" value=" {t}add{/t} "/></td>
</tr>
<tr><th colspan="8" style="text-align:left ;">{t}group{/t}:</th></tr>
<tr id="addPermGroupTR">
	<td style="white-space:nowrap">
		<select id="inputAddPermGroup" name="name">
		{foreach from=$groupsList key="id_group" item="groupname"}
			<option value="{$groupname}">{$groupname}</option>
		{/foreach}
		</select>
	</td>
	<td><input type="hidden" name="switch" value="group"/></td>
	<td><input type="checkbox" name="read" value="{$conf->BEDITA_PERMS_READ}"/></td>
	<td><input type="checkbox" name="modify" value="{$conf->BEDITA_PERMS_MODIFY}"/></td>
	<td><input type="checkbox" name="delete" value="{$conf->BEDITA_PERMS_DELETE}"/></td>
	<td><input type="button" name="cmdAddGroupPerm" value=" {t}add{/t} "/></td>
</tr>
</table>
</fieldset>
</div>