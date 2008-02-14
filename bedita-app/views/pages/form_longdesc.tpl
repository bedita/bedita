<h2 class="showHideBlockButton">{t}Long Text{/t}</h2>
<div class="blockForm" id="extendedtext" style="display: none">

{if ($conf->fckeditor|default:false)}
	{*  BEGIN -- CON FCKEDITOR *}

{$javascript->link('fckeditor/fckeditor.js')}

<script type="text/javascript">
var typeFormatting		= '{$object.type|default:'html'}' ;
var formattingHTMLTextDoc 	= {if ($object.type|default:'html' == "html")} true {else} false {/if} ;
var sBasePath 		= "{$html->webroot}js/fckeditor/" ;
var sConfigurePath		= "{$html->webroot}js/descrizioni.fckeditor.config.js" ;
var sMsgConfirm		= "{t}If you continue, you loose the formatting type, new line.\nDo you want to continue?{/t}" ;

{literal}
var oFCKeditorTesto 	= false ;
var oFCKeditorTestoL 	= false ;

var bFCKLoaded		= false ;
var bFCKShowed		= false ;

$(document).ready(function(){
	var app = formattingHTMLTextDoc ;
	setupEditor() ;
	formattingHTMLTextDoc = app ;
	
	// Inizializza lo stato degli editor
	if(formattingHTMLTextDoc) {
		$("#containerTestoTextarea, #containerTestoLTextarea").hide() ;
		$("#containerTestoFCKEditor, #containerTestoLFCKEditor").show() ;
		
		bFCKShowed = true ;
	} else {
		$("#containerTestoFCKEditor, #containerTestoLFCKEditor").hide() ;
		$("#containerTestoTextarea, #containerTestoLTextarea").show() ;
		
		bFCKShowed = false ;
	}
	
	// Inizializza le label per cambiare le finestre di editor
	$(".formatting").each(function(i){}) ;

	$(".formatting").each(function(i){		
	 	if(typeFormatting == this.value) this.checked = true ; 
	}) ;

	$(".formatting ").bind("click", function() {
		if(this.value == 'html') changeFormattingText(true) ;
		else changeFormattingText(false) ;
	}) ; 
	$(".labelFormating ").bind("click", function() {
		if($(this).prev("input.formatting").get(0).checked) return ;
		
		var value = $(this).prev("input.formatting").get(0).value ;
		if(value == 'html') changeFormattingText(true) ;
		else changeFormattingText(false) ;
		
		$(this).prev("input.formatting").get(0).checked = true ;
	}) ; 
	
	// Per salvare il testo editato in fckeditor
	$($("#testoL")[0].form).bind("submit", function() {
		if(!formattingHTMLTextDoc) return true ;
		
		var oEditor = FCKeditorAPI.GetInstance(oFCKeditorTesto.InstanceName) ;
		$("#testo").attr("value", oEditor.GetHTML()) ;
	
		oEditor = FCKeditorAPI.GetInstance(oFCKeditorTestoL.InstanceName) ;
		$("#testoL").attr("value", oEditor.GetHTML()) ;
		
		return true ;
	}) ;
/*	
	// Setup stato iniziale editor
	var val = $(".formatting ").attr("value") ;
	if(val == 'html') {
		$("#containerTestoTextarea, #containerTestoLTextarea").hide() ;

		var oEditor = FCKeditorAPI.GetInstance(oFCKeditorTesto.InstanceName) ;
		oEditor.SetHTML($("#testo").get(0).value) ;
	
		oEditor = FCKeditorAPI.GetInstance(oFCKeditorTestoL.InstanceName) ;
		oEditor.SetHTML($("#testoL").get(0).value) ;
	
		$("#containerTestoFCKEditor, #containerTestoLFCKEditor").show() ;

		formattingHTMLTextDoc = true ;	
		bFCKShowed = true ;
	}
*/
}) ;

/**
* Setup degli editor utilizzati
*/
function setupEditor() {
	// Crea gli editor FCK
	oFCKeditorTesto = new FCKeditor( 'testoFCK' ) ;

	oFCKeditorTesto.Config['CustomConfigurationsPath'] = sConfigurePath ;
	oFCKeditorTesto.BasePath	= sBasePath ;
	oFCKeditorTesto.ToolbarSet	= 'PluginTest' ;
	oFCKeditorTesto.Height 		= "175" ;	
	oFCKeditorTesto.Width 		= "575" ;	
	oFCKeditorTesto.ReplaceTextarea() ;
		
	oFCKeditorTestoL = new FCKeditor( 'testoLFCK' ) ;

	oFCKeditorTestoL.Config['CustomConfigurationsPath'] = sConfigurePath ;
	oFCKeditorTestoL.BasePath		= sBasePath ;
	oFCKeditorTestoL.ToolbarSet		= 'PluginTest' ;
	oFCKeditorTestoL.Width 		= "575" ;	
	oFCKeditorTestoL.Height 		= "275" ;	
	oFCKeditorTestoL.ReplaceTextarea() ;
	
	formattingHTMLTextDoc = false ;
	bFCKLoaded = true ;
	bFCKShowed = false ;
}

function viewFCK() {
	if(bFCKShowed || !bFCKLoaded) return ;
	
	$("#containerTestoTextarea, #containerTestoLTextarea").hide() ;

	var oEditor = FCKeditorAPI.GetInstance(oFCKeditorTesto.InstanceName) ;
	oEditor.SetHTML($("#testo").get(0).value) ;
	
	oEditor = FCKeditorAPI.GetInstance(oFCKeditorTestoL.InstanceName) ;
	oEditor.SetHTML($("#testoL").get(0).value) ;
	
	$("#containerTestoFCKEditor, #containerTestoLFCKEditor").show() ;

	formattingHTMLTextDoc = true ;	
	bFCKShowed = true ;
	
	return true ;
}

function unviewFCK() {
	if(!bFCKShowed || !bFCKLoaded) return ;
	
	$("#containerTestoFCKEditor, #containerTestoLFCKEditor").hide() ;

	var oEditor = FCKeditorAPI.GetInstance(oFCKeditorTesto.InstanceName) ;
	$("#testo").attr("value", oEditor.GetHTML()) ;
	
	oEditor = FCKeditorAPI.GetInstance(oFCKeditorTestoL.InstanceName) ;
	$("#testoL").attr("value", oEditor.GetHTML()) ;

	$("#containerTestoTextarea, #containerTestoLTextarea").show() ;
	
	formattingHTMLTextDoc = false ;
	bFCKShowed = false ;
	
	return true ;
}

/* 
Richiede di cambiare la visualizzazione in base alla formattazione richiesta.
Se viene richiesta la visualizzazione html:
 - Se ancora da carciare, carica
 - Se gia' caricata avverte la possibilitˆ di perdita della formattazione txt
*/
function changeFormattingText(sentHtml) {
	// Se nn e' completo il caricamento, esce
	if(!bFCKLoaded) return ;
	
	if(sentHtml && !confirm(sMsgConfirm)) return ;
	
	if(sentHtml) {
		if(!bFCKShowed) viewFCK() ;
	} else {
		if(bFCKShowed) unviewFCK() ;
	}
}
{/literal}

</script>

<fieldset>
			<b>{t}Short text{/t}:</b>
			<br/>
			<div id="containerTestoTextarea" style="display:none;">
			<textarea name="data[short_desc]" id="testo" style="font-size:13px; width:510px; height:150px;">{$object.short_desc|default:''}</textarea>
			</div>
			<div id="containerTestoFCKEditor" style="display:none;">
			<textarea name="testoFCK" id="testoFCK" style="font-size:13px; width:510px; height:150px;">{$object.short_desc|default:''}</textarea>
			</div>
			<p>
			<b>{t}Long text{/t}:</b>
			<br/>
			<div id="containerTestoLTextarea" style="display:none;">
			<textarea name="data[long_desc]" id="testoL" style="font-size:13px; width:510px; height:150px;">{$object.long_desc|default:''}</textarea>
			</div>
			<div id="containerTestoLFCKEditor" style="display:none;">
			<textarea name="testoLFCK" id="testoLFCK" style="font-size:13px; width:510px; height:150px;">{$object.long_desc|default:''}</textarea>
			</div>
			<p>
			<b>{t}Text type{/t}:</b>
			&nbsp;&nbsp;
			<input type="radio" name="data[type]" class="formatting" value="html" /> <span class="labelFormating">{t}html{/t}</span>
			<input type="radio" name="data[type]" class="formatting" value="txt"/> <span class="labelFormating">{t}only text{/t}</span>
			<input type="radio" name="data[type]" class="formatting" value="txtParsed" /> <span class="labelFormating">{t}text with conversion space and link{/t}</span>
			</p>
</fieldset>

	{*  END -- CON FCKEDITOR *}
	
{else}
	{*  BEGIN -- SENZA FCKEDITOR *}

<script type="text/javascript">
var typeFormatting		= '{$object.type|default:'html'}' ;
var formattingHTMLTextDoc 	= {if ($object.type|default:'html' == "html")} true {else} false {/if} ;
var sBasePath 		= "{$html->url('/js/fckeditor/')}" ;
var sConfigurePath		= "{$html->url('/js/descrizioni.fckeditor.config.js')}" ;
var sMsgConfirm		= "{t}If you continue, you loose the formatting type, new line.\nDo you want to continue?{/t}" ;

{literal}
var oFCKeditorTesto 	= false ;
var oFCKeditorTestoL 	= false ;

var bFCKLoaded		= false ;
var bFCKShowed		= false ;

$(document).ready(function(){	

	$(".formatting").each(function(i){		
	 	if(typeFormatting == this.value) this.checked = true ; 
	}) ;
		
	$(".labelFormating ").bind("click", function() {
		if($(this).prev("input.formatting").get(0).checked) return ;
		
		$(this).prev("input.formatting").get(0).checked = true ;
	}) ; 

}) ;


{/literal}
</script>
	
<fieldset>
			<b>{t}Short text{/t}:</b>
			<br/>
			<textarea name="data[short_desc]" id="testo" style="font-size:13px; width:510px; height:150px;">{$object.short_desc|default:''}</textarea>
			<br/>
			<b>{t}Long text{/t}:</b>

			<br/>
			<textarea name="data[long_desc]" id="testoL" style="font-size:13px; width:510px; height:150px;">{$object.long_desc|default:''}</textarea>
			<br/>
			<b>{t}Text type{/t}:</b>
			&nbsp;&nbsp; 
			<input type="radio" name="data[type]" class="formatting" value="html" /> <span class="labelFormating">{t}html{/t}</span>
			<input type="radio" name="data[type]" class="formatting" value="txt"/> <span class="labelFormating">{t}only text{/t}</span>
			<input type="radio" name="data[type]" class="formatting" value="txtParsed" /> <span class="labelFormating">{t}text with conversion space and link{/t}</span>
			</p>

</fieldset>

	{*  END -- SENZA FCKEDITOR prova  *}
{/if}
</div>