<script type="text/javascript">
<!--
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

	validateFrm = $("#updateform").validate({
		debug:false,
		errorLabelContainer: $("#errorForm"),
		errorClass: "errorFieldForm",
		rules: { "data[title]": "required", },
		messages: { title: "Il titolo &egrave; obbligatorio",}
	});

	$("#updateform//input[@name=cancella]").bind("click", function() {
		if(!confirm("Attenzione!!! operazione potenzialmente dannosa.\nSicuro di voler continuare?")) {
			return false ;
		}
		document.location = "{/literal}{$html->url('deleteArea/')}{$area.id}{literal}" ;
	}) ;


	$("#updateform").bind("submit", function() {
		// se ci sono stati errori, stampa un messaggio
		if(validateFrm.errorList.length) {
			alert(validateFrm.errorList[0].message) ;
		}
	}) ;

	// Aggiunta traduzioni linguistiche dei campi
	$("#cmdTranslateTitle").addTranslateField('title', langs) ;
});

{/literal}
//-->
</script>

<div id="containerPage">

{formHelper fnc="create" args="'area', array('id' => 'updateform', 'action' => 'saveArea', 'type' => 'POST', 'enctype' => 'multipart/form-data')"}

<input  type="hidden" name="data[id]" value="{$area.id|default:''}"/>

<div class="FormPageHeader">
	<h1>{$area.title|default:"nuova area"}</h1>
	<table cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<a id="openAllBlockLabel" style="display:block;" href="javascript:showAllBlockPage(1)"><span style="font-weight:bold;">&gt;</span> {t}apri tutti i dettagli{/t}</a>
			<a id="closeAllBlockLabel" href="javascript:hideAllBlockPage()"><span style="font-weight:bold;">&gt;</span> {t}chiudi tutti i dettagli{/t}</a>
		</td>
		<td style="padding-left:40px;white-space:nowrap">
			{formHelper fnc="submit" args="'salva', array('name' => 'save', 'class' => 'submit', 'div' => false)"}
			<input type="button" name="cancella" class="submit" value="cancella" />
		</td>
		<td style="padding-left:40px">&nbsp;</td>
	</tr>
	</table>
</div>

<div class="blockForm" id="errorForm"></div>

<h2 class="showHideBlockButton">{t}Propriet&agrave;{/t}</h2>

<div class="blockForm" id="proprieta">
	<span style="font-weight:bold;">{t}status{/t}</span>:
	{html_radios name="status" options=$conf->statusOptions selected=$area.status|default:$conf->status separator=" "}
	<hr/>
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Lingua{/t}:</td>
		<td>
			<select name="lang">
			{html_options options=$conf->langOptions selected=$area.lang|default:$conf->lang}
			</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr id="Title_TR_{$area.lang|default:$conf->lang}">
		<td class="label">{t}Titolo{/t}:</td>
		<td>
			<input  class="{literal}{required:true}{/literal}" id="titleInput"  type="text" name="data[title]" value="{$area.title|default:''|escape:'html'|escape:'quotes'}" />&nbsp;
		</td>
		{if ($area)}
		<td><input class="cmdField" id="cmdTranslateTitle" type="button" value="lang ..."/></td>
		{/if}
	</tr>
	{if (isset($area.LangText.title))}
	{foreach name=i from=$area.LangText.title key=lang item=text}
	<tr>
		<td class="label">&#160;</td>
		<td>
			<input type='hidden' value='title' name="data[LangText][{$smarty.foreach.i.iteration}][name]"/>
			<input type="text" name="data[LangText][{$smarty.foreach.i.iteration}][txt]" value="{$text|escape:'html'|escape:'quotes'}"/>&nbsp;
		</td>
		<td>
			<select name="data[LangText][{$smarty.foreach.i.iteration}][lang]">
			{html_options options=$conf->langOptions selected=$lang}
			</select>
			&nbsp;&nbsp;
			<input type="button" name="delete" value=" x " onclick=" $('../..', this).remove();"/>
		</td>
	</tr>
	{/foreach}
	{/if}
	</table>
	{if ($area)}
	<hr/>
	<table class="tableForm" border="0">
	<tr><td class="label">{t}Alias{/t}:</td><td>{$area.nickname}</td></tr>
	<tr><td class="label">{t}Creata il{/t}:</td><td>{$area.created|date_format:$conf->date_format}</td></tr>
	<tr><td class="label">{t}Ultima modifica{/t}:</td><td>{$area.modified|date_format:$conf->date_format}</td></tr>
	<tr><td class="label">{t}IP{/t}:</td><td>{$area.IP_created}</td></tr>
	</table>
	{/if}
</div>

<h2 class="showHideBlockButton">{t}Proprietà Custom{/t}</h2>

<div class="blockForm" id="proprietaCustom">
{include file="../pages/form_custom_properties.tpl" el=$area}
</div>

<h2 class="showHideBlockButton">{t}Permessi{/t}</h2>

<div class="blockForm" id="permessi">
{include file="../pages/form_permissions.tpl" el=$area recursion=true}
</div>

</form>

</div>