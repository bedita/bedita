{*
** translations form template
*}
{literal}
<style type="text/css">

	.mainhalf TEXTAREA, .mainhalf INPUT[type=text], .mainhalf TABLE.bordered {
		width:320px !important;
	}

	.disabled {
		opacity:0.6;	
	}
	.disabled TEXTAREA, .disabled INPUT[type=text] {
		background-color:transparent;
	}

</style>
{/literal}

{$javascript->link("tiny_mce/tiny_mce")}
{literal}
<script language="javascript" type="text/javascript">

tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	editor_selector : "mce",
	plugins : "safari,pagebreak,paste,fullscreen",

	// Theme options
	theme_advanced_buttons1 : "bold,italic, | ,formatselect,bullist, | ,link,unlink,pastetext,pasteword, | ,charmap,fullscreen",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "", 
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	//theme_advanced_statusbar_location : "bottom",
	//theme_advanced_resizing : true,
	theme_advanced_blockformats : "p,h1,h2,h3,h4,blockquote,address",
	width : "320",


	// Example content CSS (should be your site CSS)
	content_css : "/css/htmleditor.css",
	relative_urls : false,
	convert_urls : false,
	remove_script_host : false,
	document_base_url : "/"



});

	</script>
{/literal}



{literal}
<script type="text/javascript">
$(document).ready(function(){

	$(".tab2").click(function () {
		
			var trigged = $(this).next().attr("rel") ;
			//$(this).BEtabstoggle();
			$("*[rel='"+trigged+"']").prev(".tab2").BEtabstoggle();

	});

	$('textarea.autogrowarea').css("line-height","1.2em").autogrow();
});
</script>
{/literal}


{include file="../common_inc/form_common_js.tpl"}

<form action="{$html->url('/translations/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object_translation.id.status|default:''}"/>
<input type="hidden" name="data[master_id]" value="{$object_master.id|default:''}"/>

<div class="mainhalf">

	<div class="tab2"><h2>{t}Properties{/t}</h2></div>
	<fieldset rel="properties">
	<label>{t}translation to{/t}:</label>
		{assign var=object_translated_lang value=$object_translation.lang|default:''}
		{if empty($object_translated_lang)}
			<select style="font-size:1.2em;" name="data[translation_lang]" id="main_lang">
				{foreach key=val item=label from=$conf->langOptions name=langfe}
					{if !in_array($val,$object_master_langs)}
					<option value="{$val}">{$label}</option>
					{/if}
				{/foreach}
			</select>
		{else}
			<select style="font-size:1.2em;" name="data[translation_lang]" id="main_lang">
					<option value="{$object_translated_lang}" selected="selected">{$conf->langOptions[$object_translated_lang]}</option>
			</select>
		{/if}
		<hr />
		<label>{t}status{/t}:</label>
		<input type="radio" name="data[LangText][0][text]" {if !empty($object_translation) && ($object_translation.status=='on')}checked="checked" {/if}value="on"/>ON
		<input type="radio" name="data[LangText][0][text]" {if !empty($object_translation) && ($object_translation.status=='off')}checked="checked" {/if}value="off"/>OFF
		<input type="radio" name="data[LangText][0][text]" {if !empty($object_translation) && ($object_translation.status=='draft')}checked="checked" {/if}value="draft"/>DRAFT
		<input type="radio" name="data[LangText][0][text]" {if empty($object_translation) || ($object_translation.status=='required')}checked="checked" {/if}value="required"/>TO DO
		<input type="hidden" name="data[LangText][0][name]" value="status"/>
		{if !empty($object_translation) && !empty($object_translation.id) && !empty($object_translation.id.status)}<input type="hidden" name="data[LangText][0][id]" value="{$object_translation.id.status}"/>{/if}

	</fieldset>



	<div class="tab2"><h2>{t}Title{/t}</h2></div>
	<fieldset rel="title">
		<label>{t}title{/t}:</label><br />
		<input type="text" id="title" name="data[LangText][1][text]" value="{if !empty($object_translation.title)}{$object_translation.title}{/if}"/><br />
		<input type="hidden" name="data[LangText][1][name]" value="title"/>
		{if !empty($object_translation.id.title)}<input type="hidden" name="data[LangText][1][id]" value="{$object_translation.id.title}"/>{/if}
		{if !empty($object_master.description)}
		<label>{t}description{/t}:</label><br />
		<textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name="data[LangText][2][text]">{if !empty($object_translation.description)}{$object_translation.description}{/if}</textarea>
		<input type="hidden" name="data[LangText][2][name]" value="description"/>
		{if !empty($object_translation.id.description)}<input type="hidden" name="data[LangText][2][id]" value="{$object_translation.id.description}"/>{/if}
		{/if}
	</fieldset>

	{if !empty($object_master.abstract) || !empty($object_master.body)}
	<div class="tab2"><h2>{t}Long Text{/t}</h2></div>

	<fieldset rel="long_desc_langs_container">
		{if !empty($object_master.abstract)}
		<label>{t}short text{/t}:</label><br />
		<textarea name="data[LangText][3][text]" style="height:200px" class="mce">{if !empty($object_translation.abstract)}{$object_translation.abstract}{/if}</textarea>
		<input type="hidden" name="data[LangText][3][name]" value="abstract"/>
		{if !empty($object_translation.id.abstract)}<input type="hidden" name="data[LangText][3][id]" value="{$object_translation.id.abstract}"/>{/if}
		<br />
		{/if}
		{if !empty($object_master.body)}
		<label>{t}long text{/t}:</label><br />
		<textarea name="data[LangText][4][text]" style="height:400px" class="mce">{if !empty($object_translation.body)}{$object_translation.body}{/if}</textarea>
		<input type="hidden" name="data[LangText][4][name]" value="body"/>
		{if !empty($object_translation.id.body)}<input type="hidden" name="data[LangText][4][id]" value="{$object_translation.id.body}"/>{/if}
		{/if}
	</fieldset>
	{/if}


{if !empty($object_master.relations.attach) && $object_master.object_type_id != $conf->objectTypes.image.id
											&& $object_master.object_type_id != $conf->objectTypes.video.id
											&& $object_master.object_type_id != $conf->objectTypes.befile.id}

	<div class="tab2"><h2>{t}multimedia descriptions{/t}</h2></div>
	<fieldset rel="multimedia">
		<table style="margin-left:-10px; margin-bottom:20px;" border="0" cellpadding="0" cellspacing="2">
		{assign var='lang_text_index' value=10}
		{foreach from=$object_master.relations.attach item='image' name=attachfe}
		<tr>
			<td>
				<a href="{$conf->mediaUrl}{$image.path}">
					<img src="{$conf->mediaUrl}{$image.path}" style='width:100px; height:100px; border:5px solid white; margin-bottom:0px;'/>
				</a>
			</td>
			<td>
				{assign var='l1' value=$lang_text_index++}
				{assign var='image_status' value=$image.LangText.status[$object_translated_lang]|default:$image.status}
				<input type="hidden" name="data[LangText][{$l1}][name]" value="status"/>
				<input type="hidden" name="data[LangText][{$l1}][object_id]" value="{$image.id}"/>
				<input type="hidden" name="data[LangText][{$l1}][text]" value="{$image_status}"/>
				{if !empty($image.LangText[$image.id][$object_translated_lang].status)}<input type="hidden" name="data[LangText][{$l1}][id]" value="{$image.LangText[$image.id][$object_translated_lang].status}"/>{/if}

				{assign var='l1' value=$lang_text_index++}
				{assign var='image_title' value=$image.LangText.title[$object_translated_lang]|default:''}
				<label>{t}Title{/t}</label>
				<input type="hidden" name="data[LangText][{$l1}][name]" value="title"/>
				<input type="text" name="data[LangText][{$l1}][text]" style="width:210px !important" value="{$image_title}" />
				<input type="hidden" name="data[LangText][{$l1}][object_id]" value="{$image.id}"/>
				{if $image.LangText}<input type="hidden" name="data[LangText][{$l1}][id]" value="{$image.LangText[$image.id][$object_translated_lang].title|default:''}"/>{/if}
				
				{assign var='l1' value=$lang_text_index++}
				{assign var='image_description' value=$image.LangText.description[$object_translated_lang]|default:''}
				<label>{t}Description{/t}</label>
				<input type="hidden" name="data[LangText][{$l1}][name]" value="description"/>
				<textarea style="height:38px; width:210px !important" name="data[LangText][{$l1}][text]">{$image_description}</textarea>
				<input type="hidden" name="data[LangText][{$l1}][object_id]" value="{$image.id}"/>
				{if !empty($image.LangText)}<input type="hidden" name="data[LangText][{$l1}][id]" value="{$image.LangText[$image.id][$object_translated_lang].description|default:''}"/>{/if}
			
			</td>
		</tr>
		{/foreach}
		</table>
	</fieldset>

{/if}

	<div class="tab2"><h2>{t}advanced properties{/t}</h2></div>
	<fieldset rel="advancedproperties">
	<table class="bordered">
		<tr>
			<th>{t}created on{/t}:</th>
			<td>
				{if !empty($object_translation.created_on)}{$object_translation.created_on|date_format:$conf->dateTimePattern}{else}-{/if}
				<input type="hidden" name="data[LangText][5][text]" value="{if !empty($object_translation.created_on)}{$object_translation.created_on}{/if}"/>
				<input type="hidden" name="data[LangText][5][name]" value="created_on"/>
				{if !empty($object_translation.id.created_on)}<input type="hidden" name="data[LangText][5][id]" value="{$object_translation.id.created_on}"/>{/if}
			</td>
		</tr>
		<tr>
			<th>{t}last modified on{/t}:</th>
			<td>
				{if !empty($object_translation.modified_on)}{$object_translation.modified_on|date_format:$conf->dateTimePattern}{else}-{/if}
				<input type="hidden" name="data[LangText][6][text]" value="{if !empty($object_translation.modified_on)}{$object_translation.modified_on}{/if}"/>
				<input type="hidden" name="data[LangText][6][name]" value="modified_on"/>
				{if !empty($object_translation.id.modified_on)}<input type="hidden" name="data[LangText][6][id]" value="{$object_translation.id.modified_on}"/>{/if}
			</td>
		</tr>
		<tr>
			<th>{t}created by{/t}:</th>
			<td>
				{if !empty($object_translation.created_by)}{$object_translation.created_by}{else}-{/if}
				<input type="hidden" name="data[LangText][7][text]" value="{if !empty($object_translation.created_by)}{$object_translation.created_by}{/if}"/>
				<input type="hidden" name="data[LangText][7][name]" value="created_by"/>
				{if !empty($object_translation.id.created_by)}<input type="hidden" name="data[LangText][7][id]" value="{$object_translation.id.created_by}"/>{/if}
			</td>
		</tr>
		<tr>
			<th>{t}last modified by{/t}:</th>
			<td>
				{if !empty($object_translation.modified_by)}{$object_translation.modified_by}{else}-{/if}
				<input type="hidden" name="data[LangText][8][text]" value="{if !empty($object_translation.modified_by)}{$object_translation.modified_by}{/if}"/>
				<input type="hidden" name="data[LangText][8][name]" value="modified_by"/>
				{if !empty($object_translation.id.modified_by)}<input type="hidden" name="data[LangText][8][id]" value="{$object_translation.id.modified_by}"/>{/if}
			</td>
		</tr>
	</table>
	</fieldset>

</div>




<div class="mainhalf disabled">
	
<div class="tab2"><h2>{t}Properties{/t}</h2></div>
	<fieldset rel="properties">
	<label>{t}master language{/t}:</label>
		<select disabled style="font-size:1.2em;" id="eventoLang">
			<option label="{$conf->langOptions[$object_master.lang]}" value="{$object_master.lang}">{$conf->langOptions[$object_master.lang]}</option>
		</select>	
		<hr />
		<label>status</label>:
		<input disabled type="radio" {if ($object_master.status=='on')}checked="checked" {/if}value="on">ON
		<input disabled type="radio" {if ($object_master.status=='off')}checked="checked" {/if}value="off">OFF
		<input disabled type="radio" {if ($object_master.status=='draft')}checked="checked" {/if}value="draft">DRAFT
	</fieldset>


	<div class="tab2"><h2>{t}Original Title{/t}</h2></div>
	<fieldset rel="title">
		<label>{t}title{/t}:</label><br />
		<input type="text" id="title_master" name="" value="{$object_master.title}" readonly="readonly"/><br />
		{if !empty($object_master.description)}
		<label>{t}tescription{/t}:</label><br />
		<textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name="">{$object_master.description}</textarea>
		{/if}
	</fieldset>

	{if !empty($object_master.abstract) || !empty($object_master.body)}
	<div class="tab2"><h2>{t}Long Text{/t}</h2></div>

	<fieldset rel="long_desc_langs_container">
		{if !empty($object_master.abstract)}
		<label>{t}short text{/t}:</label><br />
		<textarea name="" style="height:200px" class="mce">{$object_master.abstract}</textarea>
		<br />
		{/if}
		{if !empty($object_master.body)}
		<label>{t}long text{/t}:</label><br />
		<textarea name="" style="height:400px" class="mce">{$object_master.body}</textarea>
		{/if}
	</fieldset>
	{/if}


{if !empty($object_master.relations.attach) && $object_master.object_type_id != $conf->objectTypes.image.id
											&& $object_master.object_type_id != $conf->objectTypes.video.id
											&& $object_master.object_type_id != $conf->objectTypes.befile.id}
	<div class="tab2"><h2>{t}multimedia descriptions{/t}</h2></div>
	<fieldset rel="multimedia">
		
		<table style="margin-left:-10px; margin-bottom:20px;" border="0" cellpadding="0" cellspacing="2">
		{foreach from=$object_master.relations.attach item='image'}
		<tr>
			<td>
				<a href="{$conf->mediaUrl}{$image.path}">
					<img src="{$conf->mediaUrl}{$image.path}" style='width:100px; height:100px; border:5px solid white; margin-bottom:0px;'/>
				</a>
			</td>
			<td>
				<label>{t}title{/t}</label>
				<input type="text" style="width:210px !important" name="" value="{$image.title}" />
				<label>{t}description{/t}</label>
				<textarea style="height:38px; width:210px !important" name="">{$image.description}</textarea>
			</td>
		</tr>
		{/foreach}
		</table>
	</fieldset>
{/if}

	<div class="tab2"><h2>{t}advanced properties{/t}</h2></div>
	<fieldset rel="advancedproperties">

	<table class="bordered">
		<tr>
			<th>{t}created on{/t}:</th>
			<td>{$object_master.created|date_format:$conf->dateTimePattern}</td>
		</tr>
		<tr>
			<th>{t}last modified on{/t}:</th>
			<td>{$object_master.modified|date_format:$conf->dateTimePattern}</td>
		</tr>
		<tr>
			<th>{t}created by{/t}:</th>
			<td>{$object_master.UserCreated.userid}</td>
		</tr>
		<tr>
			<th>{t}last modified by{/t}:</th>
			<td>{$object_master.UserModified.userid}</td>
		</tr>
	</table>

	</fieldset>

</div>

</form>