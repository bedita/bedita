{*
<script type="text/javascript">
{literal}
var postfix_customProp = "_customPropTR" ;

$(document).ready(function(){
{/literal}	
	{foreach name="setCPForm" key="name" item="property" from=$el.CustomProperties}
		_setupCustomPropTR("{$name}"+postfix_customProp) ;
	{/foreach}
{literal}
	$('input[@name=cmdAdd]', "#frmCustomProperties").bind("click", function (e) {
		addCustomPropTR() ;
	}) ;
});

{/literal}

{literal}
var htmlTdInputHiddenEmpty = "<td><input type=\"hidden\" name=\"none\"\/><\/td>";
var htmlOptions="{/literal}{foreach key=key item=item from=$conf->customPropTypeOptions}<option label=\"{$key}\" value=\"{$item}\">{$item}<\/option>{/foreach}{literal}";
var htmlTdSelect="<td><select name=\"none\">"+htmlOptions+"<\/select><\/td>";
var htmlTdInputTextEmpty = "<td><input type=\"text\" readonly=\"readonly\" name=\"none\"\/><\/td>";
var htmlTdSubmit="<td><input type=\"button\" name=\"delete\" value=\" x \"\/><\/td>";
// Procedura per l'aggiunta di una proprieta'
var htmlTemplateCustomProp = '<tr>' 
+ htmlTdInputHiddenEmpty
+ htmlTdSelect
+ htmlTdInputTextEmpty
+ htmlTdSubmit
+ "<\/tr>";

function addCustomPropTR() {
	var name 	= $.trim($("#addCustomPropTR input[@name=name]").fieldValue()[0].replace(/[^_a-z0-9]/g, ""));	
	var value 	= $.trim($("#addCustomPropTR input[@name=value]").fieldValue()[0]) ;
	var type 	= $("#addCustomPropTR TD select[@name=type]").fieldValue()[0] ;
	
	// Se non completa esce
	if(!name.length || !value.length) {
		$("#addCustomPropTR TD:last").find("label").remove();
		$("#addCustomPropTR TD:last").append("<label class='error'>{/literal}{t}Incompleted data{/t}{literal}<\/label>")
		return false ;
	} else {
		$("#addCustomPropTR TD:last").find("label").remove();
	}
	
	// se gia' presente o vuota esce
	if($("#"+name+postfix_customProp).size()) {
		alert("{/literal}{t}Property already present{/t}{literal}") ;
		return false ;
	}

	// Inserisce il nuovo elemento
	var newTR = $("#endLineCustomPropTR").before(htmlTemplateCustomProp).prev() ;
	
	// Setup nomi, id e comandi degli elementi
	newTR.attr("id", name+postfix_customProp) ;
	$("TD:nth-child(1) input", newTR).attr("name", "data[CustomProperties]["+name+"][name]") ;
	$("TD:nth-child(2) select", newTR).attr("name", "data[CustomProperties]["+name+"][type]") ;
	$("TD:nth-child(3) input", newTR).attr("name", "data[CustomProperties]["+name+"][value]") ;
	$('TD:nth-child(4) input[@name=delete]', newTR).bind("click", function (e) { deleteTRCustomProp(this)}) ;
	
	// setup dei valori
	$("TD:nth-child(1) input", newTR).attr("value", name) ;
	$("TD:nth-child(1)", newTR).append(name) ;
	$("TD:nth-child(3) input", newTR).attr("value", value) ;

	var options = $("TD:nth-child(2) select", newTR).get(0).options ;
	for(var i = 0 ; i < options.length ; i++) {
		if(options[i].value == type) options[i].selected = true ; 
	}
	
	// resetta i campi per l'input di una nuova prop
	$("#addCustomPropTR input[@type=text]").attr("value", "") ;
	$("#addCustomPropTR select").get(0).options[0].selected = true ;
	
	// Indica l'avvenuto cambiamento dei dati
	try {
		$().alertSignal() ;
	} catch(e) {}
}


// Setta i comandi per la gestione delle righe della tabella delle custom properites
function _setupCustomPropTR(id) {
	// Definisce il comando per la cancellazione
	$('TD input[@name=delete]').bind("click", function (e) {
		deleteTRCustomProp(this)
	}) ;
}

// cancella l'elemento
function deleteTRCustomProp(el) {
	if(!confirm("{/literal}{t}Do you really want to delete the property{/t}{literal}'?")) return false ;
	$(el).parent().parent().remove() ;		
	
	// Indica l'avvenuto cambiamento dei dati
	try {
		$().alertSignal() ;
	} catch(e) {}
}

{/literal}
</script>

<div class="tab"><h2>{t}Custom Properties{/t}</h2></div>
<fieldset id="customProperties">
	
<table class="indexlist" id="frmCustomProperties">
	<tr>
		<th>{t}name{/t}:</th>
		<th>{t}type{/t}:</th>
		<th>{t}value{/t}:</th>
		<th>&nbsp;</th>
	</tr>
	
	{foreach key="name" item="property" from=$el.CustomProperties}
	<tr id="{$name}_customPropTR">
		<td><input type="hidden" name="data[CustomProperties][{$name}][name]"/>{$name}</td>
		<td>
			<select name="data[CustomProperties][{$name}][type]">
			{html_options options=$conf->customPropTypeOptions selected=$property|get_type}
			</select>
		</td>
		<td><input type="text" name="data[CustomProperties][{$name}][value]" value="{$property|escape:'html'}"/></td>
		<td><input type="button" name="delete" value=" x "/></td>
	</tr>
	{/foreach}
	
	
	<tr id="endLineCustomPropTR">
		<td colspan="4"><label>{t}add new property{/t}:</label></td>
	</tr>
	
	
	<tr id="addCustomPropTR">
		<td><input type="text" style="width:100px" name="name"/></td>
		<td><select name="type">{html_options options=$conf->customPropTypeOptions}</select></td>
		<td><input type="text" name="value" value=""/></td>
		<td><input type="button" name="cmdAdd" value="{t}add{/t}"/></td>
	</tr>
</table>

</fieldset>
*}