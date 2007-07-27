{*
Frammento di codice per inserire la gestione dei permessi
*}
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
var maxIDPerms = 0 ;

$(document).ready(function(){
{/literal}	
	{section name=i loop=$el.Permissions}
		_setupPermTR("{$el.Permissions[i].name}_{$el.Permissions[i].switch}"+postfix_customProp) ;
		maxIDPerms++ ;
	{/section}
{literal}
	setupFieldAutocomplete() ;
	
	$('input[@name=cmdAddUserPerm]', "#frmCustomPermissions").bind("click", function (e) {
		fncAddPermsTR('addPermUserTR') ;
	}) ;
	$('input[@name=cmdAddGroupPerm]', "#frmCustomPermissions").bind("click", function (e) {
		fncAddPermsTR('addPermGroupTR') ;
	}) ;
	
});

// Procedura per l'aggiunta di un permesso
var htmlTemplateCustomPerm = ' 													\
						<tr id=""> 												\
							<td> 												\
								<input type="hidden" name=""> 					\
							</td> 												\
							<td> 												\
								<input type="hidden" name=""> 					\
							</td> 												\
							<td> 												\
								<input type="checkbox" name="" value=""> 		\
							</td> 												\
							<td> 												\
								<input type="checkbox" name="" value=""> 		\
							</td> 												\
							<td> 												\
								<input type="checkbox" name="" value=""> 		\
							</td>												\
							<td> 												\
								<input type="button" name="delete" value=" x "> \
							</td> 												\
						</tr> 													\
' ;

function fncAddPermsTR(id) {
	var name 		= $.trim($("#"+id+" TD/input[@name=name]").fieldValue()[0].replace(/[^_a-z0-9]/g, ""));	
	var _switch 	= $("#"+id+" TD/input[@name=switch]").fieldValue()[0] ;	
	var read 		= $("#"+id+" TD/input[@name=read]").get(0).checked ;	
	var modify 		= $("#"+id+" TD/input[@name=modify]").get(0).checked ;	
	var _delete 	= $("#"+id+" TD/input[@name=delete]").get(0).checked ;	

	// Se non completa esce
	if(!name.length || (!(read || modify || _delete))) {
		alert("Dati non completi") ;
		return false ;
	}
	
	// Inserisce il nuovo elemento
	var newTR = $("#endLineCustomPermsTR").before(htmlTemplateCustomPerm).prev() ;

	// Setup nomi, id e comandi degli elementi
	newTR.attr("id", name+postfix_customProp) ;
	$("TD:nth-child(1)/input", newTR).attr("name", "Permissions["+maxIDPerms+"][name]") ;
	$("TD:nth-child(2)/input", newTR).attr("name", "Permissions["+maxIDPerms+"][switch]") ;
	$("TD:nth-child(3)/input", newTR).attr("name", "Permissions["+maxIDPerms+"][BEDITA_PERMS_READ]") ;
	$("TD:nth-child(4)/input", newTR).attr("name", "Permissions["+maxIDPerms+"][BEDITA_PERMS_MODIFY]") ;
	$("TD:nth-child(5)/input", newTR).attr("name", "Permissions["+maxIDPerms+"][BEDITA_PERMS_DELETE]") ;
	$('TD:nth-child(6)/input[@name=delete]', newTR).bind("click", function (e) { deleteTRPerm(this)}) ;

	// setup dei valori
	$("TD:nth-child(1)/input", newTR).attr("value", name) ;
	$("TD:nth-child(1)", newTR).append(name) ;
	$("TD:nth-child(2)/input", newTR).attr("value", _switch) ;
	$("TD:nth-child(2)", newTR).append(_switch) ;
	$("TD:nth-child(3)/input", newTR).get(0).checked = read ;
	$("TD:nth-child(4)/input", newTR).get(0).checked = modify ;
	$("TD:nth-child(5)/input", newTR).get(0).checked = _delete ;
	
	// Resetta i campi
	$("#"+id+" TD/input").not($("#"+id+" TD/input[@name=switch]")).clearFields() ;
	
	// Indica l'avvenuto cambiamento dei dati
	try {
		$().alertSignal() ;
	} catch(e) {}	
}

// Setta i comandi per la gestione delle righe della tabella dei permessi
function _setupPermTR(id) {
	// Definisce il comando per la cancellazione
	$('#'+id+' TD:last/input[@name=delete]').bind("click", function (e) {
		deleteTRPerm(this)
	}) ;
}

// cancella l'elemento
function deleteTRPerm(el) {
	if(!confirm("Confermi la cancellazione del permesso?")) return false ;
	$(el).parent().parent().remove() ;		
	
	// Indica l'avvenuto cambiamento dei dati
	try {
		$().alertSignal() ;
	} catch(e) {}
}




/**
Autocompletamento campi userid/group
*/
function formatItemListUserid(row) {
	return row[0] ;
}

function setupFieldAutocomplete() {
	$("#inputAddPermUser").autocomplete(
		"{/literal}{$html->url('/users/userids')}{literal}/",
		{
			delay:10,
			minChars:1,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:null,
			onFindValue:null,
			formatItem:formatItemListUserid,
			autoFill:false,
			maxItemsToShow:20
		}
	);
	
	$("#inputAddPermGroup").autocomplete(
		"{/literal}{$html->url('/groups/names')}{literal}/",
		{
			delay:10,
			minChars:1,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:null,
			onFindValue:null,
			formatItem:formatItemListUserid,
			autoFill:false,
			maxItemsToShow:20
		}
	);
}


{/literal}
</script>


				<table class="tableForm" border="0" id="frmCustomPermissions">
					<tr>
						<td class="label" style="text-align:left;">nome</td>
						<td class="label" style="text-align:left;">tipo</td>
						<td class="label" style="text-align:left;">lettura</td>
						<td class="label" style="text-align:left;">modifica</td>
						<td class="label" style="text-align:left;">cancella</td>
						<td class="label">&nbsp;</td>
					</tr>
					{section name=i loop=$el.Permissions}
						{assign var="perm" 	value=$el.Permissions[i]}
						{assign var="i" 	value=$smarty.section.i.index}
						
						<tr id="{$perm.name}_{$perm.switch}_permTR">
							<td>
								<input type="hidden" name="Permissions[{$i}][name]">
								{$perm.name}
							</td>		
							<td>
								<input type="hidden" name="Permissions[{$i}][switch]">
								{$perm.switch}
							</td>
							<td>
								<input type="checkbox" name="Permissions[{$i}][BEDITA_PERMS_READ]" value="{$conf->BEDITA_PERMS_READ}" {if ($perm.flag & $conf->BEDITA_PERMS_READ)}checked{/if}>
							</td>
							<td>
								<input type="checkbox" name="Permissions[{$i}][BEDITA_PERMS_MODIFY]" value="{$conf->BEDITA_PERMS_MODIFY}" {if ($perm.flag & $conf->BEDITA_PERMS_MODIFY)}checked{/if}>
							</td>
							<td>
								<input type="checkbox" name="Permissions[{$i}][BEDITA_PERMS_DELETE]" value="{$conf->BEDITA_PERMS_DELETE}" {if ($perm.flag & $conf->BEDITA_PERMS_DELETE)}checked{/if}>
							</td>			
							<td>
								{if !($perm.name == "administrator" && $perm.switch == 'group')}
								<input type="button" name="delete" value=" x ">
								{/if}
							</td>
						</tr>
					{/section}

					<tr id="endLineCustomPermsTR">
						<td colspan="4">
							<input type="checkbox"  name="recursiveApplyPermissions" id="recursiveApplyPermissions" value="1">&nbsp;
							<a href="javascript:void(0)" onclick="$('#recursiveApplyPermissions').toggleCheck() ;">
							Applica i permessi ricorsivamente
							</a>
						</td>
					</tr>
					<tr>
						<td colspan="8"><hr></td>
					</tr>
					<tr id="">
						<th colspan="8" style="text-align:left ;">
						utente:
						</th>
					</tr>
					<tr id="addPermUserTR">
						<td>
							<input type="text" name="name" style="width: 150px;" value="" id="inputAddPermUser" class="ac_input"/>							
						</td>		
						<td>
							<input type="hidden" name="switch" value="user">
						</td>
						<td>
							<input type="checkbox" name="read" value="{$conf->BEDITA_PERMS_READ}">
						</td>
						<td>
							<input type="checkbox" name="modify" value="{$conf->BEDITA_PERMS_MODIFY}">
						</td>
						<td>
							<input type="checkbox" name="delete" value="{$conf->BEDITA_PERMS_DELETE}">
						</td>			
						<td>
							<input type="button" name="cmdAddUserPerm" value=" add ">
						</td>
					</tr>
					<tr id="">
						<th colspan="8" style="text-align:left ;">
						gruppo:
						</th>
					</tr>
					<tr id="addPermGroupTR">
						<td>
							<input type="text" name="name" style="width: 150px;" value="" id="inputAddPermGroup" class="ac_input"/>							
						</td>		
						<td>
							<input type="hidden" name="switch" value="group">
						</td>
						<td>
							<input type="checkbox" name="read" value="{$conf->BEDITA_PERMS_READ}">
						</td>
						<td>
							<input type="checkbox" name="modify" value="{$conf->BEDITA_PERMS_MODIFY}">
						</td>
						<td>
							<input type="checkbox" name="delete" value="{$conf->BEDITA_PERMS_DELETE}">
						</td>			
						<td>
							<input type="button" name="cmdAddGroupPerm" value=" add ">
						</td>
					</tr>

				</table>
