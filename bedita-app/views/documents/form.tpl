<script type="text/javascript">
var urlIcoCalendar 		= '{$html->url('../img/calendar.gif')}' ;
var formatDateCalendar	= '{$conf->df_jquery_calendar}' ;

{literal}

var langs = {
{/literal}
	{foreach name=i from=$conf->langOptions key=lang item=label}
	"{$lang}":	"{$label}" {if !($smarty.foreach.i.last)},{/if}
	{/foreach}
{literal}
} ;

var validate = null ;

$(document).ready(function(){

	// Visualizzazione campi con  calendario
	$('#start').calendar({autoPopUp: 'both', buttonImageOnly: true, buttonImage: urlIcoCalendar , buttonText: 'Calendar', dateFormat:formatDateCalendar});
	$('#end').calendar({autoPopUp: 'both', buttonImageOnly: true, buttonImage: urlIcoCalendar , buttonText: 'Calendar', dateFormat:formatDateCalendar});
	
	// Validazione al submit
	validateFrm = $("#updateForm").validate({
		debug:false,
		errorLabelContainer: $("#errorForm"),
		errorClass: "errorFieldForm",
		rules: {
			"data[title]"		: "required",
		},
		messages: {
			"data[title]"		: "Il titolo &egrave; obbligatorio",
		}
	});

	$("#updateForm").bind("submit", function() {
		// se ci sono stati errori, stampa un messaggio
		if(validateFrm.errorList.length) {
			alert(validateFrm.errorList[0].message) ;
		}
	}) ;

	// Conferma cancellazione
	$("#updateForm input[@name=cancella]").bind("click", function() {
		if(!confirm("Attenzione!!! operazione potenzialmente dannosa.\nSicuro di voler continuare?")) {
			return false ;
		}
		document.location = "{/literal}{$html->url('delete/')}{$object.id}{literal}" ;
	}) ;

	// Aggiunta traduzioni linguistiche dei campi
	$("#cmdTranslateTitle").addTranslateField('title', langs) ;
	$("#cmdTranslateSubTitle").addTranslateField('subtitle', langs) ;
	$("#cmdTranslateShortDesc").addTranslateField('shortdesc', langs) ;
	$("#cmdTranslateLongDesc").addTranslateField('longdesc', langs) ;
	
	// Dal tipo di documento selezionato, visualizza o no parti di form
	$("#updateForm//input[@name='data[object_type_id]']").bind("click", function() {
		activePortionsForm(this.value) ;	
	}) ;
	
	// Selezionano la tipologia di documento
	var type = {/literal}{$object.object_type_id|default:'22'}{literal} ;
	activePortionsForm(type) ;
	
	//$("#updateForm//input[@name='data[object_type_id]'][@value='"+type+"']").get(0).checked = true ;
});

objectTypeDiv = {
	"22" : "",
	"24" : "#divLinkExtern",
	"23" : "#divLinkIntern"
}

function activePortionsForm(objectType) {
	for(k in objectTypeDiv) {
		if(k != objectType && objectTypeDiv[k].length) {
			$(objectTypeDiv[k]).hide("fast") ;
		} else   {
			if(objectTypeDiv[k].length)
				$(objectTypeDiv[k]).show("fast") ;
		}
	}
}

{/literal}
</script>

<div id="containerPage">

<form action="{$html->url('/documents/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">

<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />

<div class="FormPageHeader">
{include file="../pages/form_header.tpl"}
</div>

<div class="blockForm" id="errorForm"></div>

<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
{include file="../pages/form_properties.tpl" doctype=false}
</div>

<h2 class="showHideBlockButton">{t}Subtitle, description{/t}</h2>
<div class="blockForm" style="display:none" id="subtitle" style="display: none">
{include file="../pages/form_subtitle_desc.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Where put the document into{/t}</h2>
<div class="blockForm" id="whereto" style="display: none">
{include file="../pages/form_tree.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Long Text{/t}</h2>
<div class="blockForm" id="extendedtext" style="display: none">
{include file="../pages/form_longdesc.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Images{/t}</h2>
<div class="blockForm" id="imgs" style="display:none">
{include file="../pages/form_multimedia.tpl" multimedia=$object.multimedia}
</div>

<h2 class="showHideBlockButton">{t}Attachments{/t}</h2>
<div class="blockForm" id="attachments" style="display:none">
{include file="../pages/form_attachments.tpl" attachments=$object.attachments}
</div>

<h2 class="showHideBlockButton">{t}Connect to multimedia gallery{/t}</h2>
<div class="blockForm" id="frmgallery" style="display:none">
<script type="text/javascript">
var sBasePathGallery  = "{$html->url('/galleries/view/id:')}" ;
{literal}
function commitSelectGalleryById(id, title) {
	if(id) {
		$("#gallery_id").attr("value", id) ;
		$("#current_gallery_title").html(title) ;
		
		// Indica l'avvenuto cambiamento dei dati
		try { $().alertSignal() ; } catch(e) {}
	}
		
	tb_remove() ;	
}

function rollbackSelectGallery() {
	tb_remove() ;
}

$(document).ready(function(){
	$("#remove_gallery").bind("click", function(index) {
		$("#gallery_id").attr("value", "") ;
		$("#current_gallery_title").html("") ;
		
		// Indica l'avvenuto cambiamento dei dati
		try { $().alertSignal() ; } catch(e) {}
	}) ;
});

{/literal}
</script>

<fieldset>
<input type="hidden" id="gallery_id" name="data[gallery_id]" value="{$object.gallery_id}" />
<p>
<a href="{$html->url('/galleries')}/select_from_list/?keepThis=true&TB_iframe=true&height=480&width=640&modal=true" title="{t}Select{/t}" class="thickbox">{t}Select{/t} &gt;&gt;</a>&nbsp;&nbsp; 
<a id="remove_gallery" href="javascript:void(0)">{t}Remove{/t}</a><br/>
{t}Current gallery{/t}: <a id="current_gallery_title" href="$html->url('/galleries/view/id:'){$object.gallery_id}">{$object.Gallery.title|default:''|escape:'html'}</a>
</p>
</fieldset>
</div>


<div id="divLinkExtern"  style="display: none">
<h2 class="showHideBlockButton">{t}External links{/t}</h2>
<div class="blockForm" id="linkEsterno" style="display: none">
	LINK ESTERNO
</div>
</div>

<div id="divLinkIntern"  style="display: none">
<h2 class="showHideBlockButton">{t}Objects{/t}</h2>
<div class="blockForm" id="linkInterno"  style="display: none">
	LINK INTERNO
</div>
</div>

<h2 class="showHideBlockButton">{t}Custom Properties{/t}</h2>
<div class="blockForm" id="customProperties">
{include file="../pages/form_custom_properties.tpl" el=$object}
</div>

<h2 class="showHideBlockButton">{t}Permissions{/t}</h2>
<div class="blockForm" id="permissions">
{include file="../pages/form_permissions.tpl" el=$object recursion=true}
</div>

</form>

</div>