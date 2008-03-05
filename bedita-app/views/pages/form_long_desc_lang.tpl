<h2 class="showHideBlockButton">{t}Long Text{/t}</h2>
<div class="blockForm" id="extendedtext" style="display: none">
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#long_desc_langs_container > ul').tabs();
	$('#long_desc_langs_container > ul > li > a').click( function() { localTriggerTabs('long_desc_langs_container'); } );
	{/literal}{foreach key=val item=label from=$conf->langOptions}{literal}
	var type_formatting_{/literal}{$val}{literal} = '{/literal}{$object.LangText.$val.type|default:'html'}{literal}' ;
	$(".formatting_{/literal}{$val}{literal}").each(function(i){		
	 	if(type_formatting_{/literal}{$val}{literal} == this.value) this.checked = true ; 
	}) ;
	$(".label_formatting_{/literal}{$val}{literal} ").bind("click", function() {
		if($(this).prev("input.formatting_{/literal}{$val}{literal}").get(0).checked) return ;
		$(this).prev("input.formatting_{/literal}{$val}{literal}").get(0).checked = true ;
	}) ;
	{/literal}{/foreach}{literal}
}) ;
{/literal}
{if ($conf->fckeditor|default:false)}
{literal}
var fck_config_file = "{/literal}{$html->webroot}{literal}js/descrizioni.fckeditor.config.js" ;
var fck_path = "{/literal}{$html->webroot}{literal}js/fckeditor/" ;
var fck_msg_confim = "{/literal}{t}If you continue, you loose the formatting type, new line.\nDo you want to continue?{/t}{literal}" ;
var fck_text = new Array();
var fck_text_l = new Array();
var formatting_html_doc = new Array();
var formatting_html_text_doc = new Array();
var fck_loaded = new Array();
var fck_showed = new Array();

function setupEditor(lang) {
	fck_text[lang] = new FCKeditor( 'text_fck_'+lang ) ;
	fck_text[lang].Config['CustomConfigurationsPath'] = fck_config_file ;
	fck_text[lang].BasePath	= fck_path ;
	fck_text[lang].ToolbarSet = 'PluginTest' ;
	fck_text[lang].Height = "175" ;	
	fck_text[lang].Width = "575" ;	
	fck_text[lang].ReplaceTextarea() ;
	fck_text_l[lang] = new FCKeditor( 'text_fck_l_'+lang ) ;
	fck_text_l[lang].Config['CustomConfigurationsPath'] = fck_config_file ;
	fck_text_l[lang].BasePath = fck_path ;
	fck_text_l[lang].ToolbarSet = 'PluginTest' ;
	fck_text_l[lang].Width = "575" ;	
	fck_text_l[lang].Height = "275" ;	
	fck_text_l[lang].ReplaceTextarea() ;
	formatting_html_doc[lang] = false ;
	fck_loaded[lang] = true ;
	fck_showed[lang] = false ;
}

function viewFCK(lang) {
	if(fck_showed[lang] || !fck_loaded[lang]) return ;
	$("#textarea_text_container_"+lang,"#textarea_text_l_container_"+lang).hide() ;
	var fck_editor_instance = FCKeditorAPI.GetInstance(fck_text[lang].InstanceName) ;
	fck_editor_instance.SetHTML($("#text_"+lang).get(0).value) ;
	fck_editor_instance = FCKeditorAPI.GetInstance(fck_text_l[lang].InstanceName) ;
	fck_editor_instance.SetHTML($("#text_l_"+lang).get(0).value) ;
	$("#fckeditor_text_container_"+lang,"#fckeditor_text_l_container_"+lang).show() ;
	formatting_html_doc[lang] = true ;	
	fck_showed[lang] = true ;
	return true ;
}

function unviewFCK(lang) {
	if(!fck_showed[lang] || !fck_loaded[lang]) return ;
	$("#fckeditor_text_container_"+lang,"#fckeditor_text_l_container_"+lang).hide() ;
	var fck_editor_instance = FCKeditorAPI.GetInstance(fck_text[lang].InstanceName) ;
	$("#text_"+lang).attr("value", fck_editor_instance.GetHTML()) ;
	fck_editor_instance = FCKeditorAPI.GetInstance(fck_text_l[lang].InstanceName) ;
	$("#text_l_"+lang).attr("value", fck_editor_instance.GetHTML()) ;
	$("#textarea_text_container_"+lang,"#textarea_text_l_container_"+lang).show() ;
	formatting_html_doc[lang] = false ;
	fck_showed[lang] = false ;
	return true ;
}

function changeFormattingText(sentHtml) {
	if(!fck_loaded[lang]) return ;
	if(sentHtml && !confirm(fck_msg_confim)) return ;
	if(sentHtml) {
		if(!fck_showed[lang]) viewFCK() ;
	} else {
		if(fck_showed[lang]) unviewFCK() ;
	}
}
$(document).ready(function(){
{/literal}{foreach key=val item=label from=$conf->langOptions}{literal}
	fck_text['{/literal}{$val}{literal}'] = false ;
	fck_text_l['{/literal}{$val}{literal}'] = false ;
	fck_loaded['{/literal}{$val}{literal}'] = false ;
	fck_showed['{/literal}{$val}{literal}'] = false ;
	formatting_html_text_doc['{/literal}{$val}{literal}'] = {/literal}{if ($object.LangText.type[$val]|default:'html' == "html")} true {else} false {/if}{literal} ;
	var type_formatting_{/literal}{$val}{literal} = '{/literal}{$object.LangText.type[$val]|default:'html'}{literal}' ;
	var app_{/literal}{$val}{literal} = formatting_html_text_doc['{/literal}{$val}{literal}'] ;
	setupEditor('{/literal}{$val}{literal}') ;
	formatting_html_text_doc['{/literal}{$val}{literal}'] = app_{/literal}{$val}{literal} ;
	if(formatting_html_text_doc['{/literal}{$val}{literal}']) {
		$("#textarea_text_container_{/literal}{$val}{literal}, #textarea_text_l_container_{/literal}{$val}{literal}").hide() ;
		$("#fckeditor_text_container_{/literal}{$val}{literal}, #fckeditor_text_l_container_{/literal}{$val}{literal}").show() ;
		fck_showed['{/literal}{$val}{literal}'] = true ;
	} else {
		$("#fckeditor_text_container_{/literal}{$val}{literal}, #fckeditor_text_l_container_{/literal}{$val}{literal}").hide() ;
		$("#textarea_text_container_{/literal}{$val}{literal}, #textarea_text_l_container_{/literal}{$val}{literal}").show() ;
		fck_showed['{/literal}{$val}{literal}'] = false ;
	}
	$(".formatting_{/literal}{$val}{literal}").each(function(i){ if(type_formatting_{/literal}{$val}{literal} == this.value) this.checked = true ; }) ;
	$(".formatting_{/literal}{$val}{literal} ").bind("click", function() { if(this.value == 'html') changeFormattingText(true) ; else changeFormattingText(false) ; }) ; 
	$(".label_formatting_{/literal}{$val}{literal} ").bind("click", function() {
		if($(this).prev("input.formatting_{/literal}{$val}{literal}").get(0).checked) return ;
		var value = $(this).prev("input.formatting_{/literal}{$val}{literal}").get(0).value ;
		if(value == 'html') changeFormattingText(true) ;
		else changeFormattingText(false) ;
		$(this).prev("input.formatting_{/literal}{$val}{literal}").get(0).checked = true ;
	}) ; 
	$($("#text_l_{/literal}{$val}{literal}")[0].form).bind("submit", function() {
		if(!formatting_html_text_doc[{/literal}{$val}{literal}]) return true ;
		var fck_editor_instance = FCKeditorAPI.GetInstance(fck_text['{/literal}{$val}{literal}'].InstanceName) ;
		$("#text_{/literal}{$val}{literal}").attr("value", fck_editor_instance.GetHTML()) ;
		fck_editor_instance = FCKeditorAPI.GetInstance(fck_text_l['{/literal}{$val}{literal}'].InstanceName) ;
		$("#text_l_{/literal}{$val}{literal}").attr("value", fck_editor_instance.GetHTML()) ;
		return true ;
	}) ;
{/literal}{/foreach}{literal}
}) ;
{/literal}
{/if}
</script>
{if ($conf->fckeditor|default:false)}{$javascript->link('fckeditor/fckeditor.js')}{/if}
<fieldset>
	<div id="long_desc_langs_container">
		<ul>
			{foreach key=val item=label from=$conf->langOptions}
			<li><a href="#long_desc_lang_{$val}"><span>{$label}</span></a></li>
			{/foreach}
		</ul>
		{foreach key=val item=label from=$conf->langOptions}
		<div id="long_desc_lang_{$val}">
			<h3><img src="{$html->webroot}img/flags/{$val}.png" border="0"/></h3>
			<b>{t}Short text{/t}:</b>
			<br/>
			{if ($conf->fckeditor|default:false)}
			<div id="textarea_text_container_{$val}" style="display:none;">
				<textarea name="data[LangText][{$val}][abstract]" id="text_{$val}" style="font-size:13px; width:510px; height:150px;">{$object.LangText.abstract[$val]|default:''}</textarea>
			</div>
			<div id="fckeditor_text_container_{$val}" style="display:none;">
				<textarea name="data[LangText][{$val}][abstract]" id="text_fck_{$val}" style="font-size:13px; width:510px; height:150px;">{$object.LangText.abstract[$val]|default:''}</textarea>
			</div>
			{else}
			<textarea name="data[LangText][{$val}][abstract]" id="text_{$val}" style="font-size:13px; width:510px; height:150px;">{$object.LangText.abstract[$val]|default:''}</textarea>
			{/if}
			<br/>
			<b>{t}Long text{/t}:</b>
			<br/>
			{if ($conf->fckeditor|default:false)}
			<div id="textarea_text_l_container_{$val}" style="display:none;">
				<textarea name="data[LangText][{$val}][body]" id="text_l_{$val}" style="font-size:13px; width:510px; height:150px;">{$object.LangText.body[$val]|default:''}</textarea>
			</div>
			<div id="fckeditor_text_l_container_{$val}" style="display:none;">
				<textarea name="data[LangText][{$val}][body]" id="text_fck_l_{$val}" style="font-size:13px; width:510px; height:150px;">{$object.LangText.body[$val]|default:''}</textarea>
			</div>
			{else}
			<textarea name="data[LangText][{$val}][body]" id="text_l_{$val}" style="font-size:13px; width:510px; height:150px;">{$object.LangText.body[$val]|default:''}</textarea>
			{/if}
			<br/>
			<b>{t}Text type{/t}:</b>
			&nbsp;&nbsp;
			<input type="radio" name="data[LangText][{$val}][type]" class="formatting_{$val}" value="html" /> <span class="label_formatting_{$val}">{t}html{/t}</span>
			<input type="radio" name="data[LangText][{$val}][type]" class="formatting_{$val}" value="txt"/> <span class="label_formatting_{$val}">{t}only text{/t}</span>
			<input type="radio" name="data[LangText][{$val}][type]" class="formatting_{$val}" value="txtParsed" /> <span class="label_formatting_{$val}">{t}text with conversion space and link{/t}</span>
		</div>
		{/foreach}
	</div>
</fieldset>
</div>