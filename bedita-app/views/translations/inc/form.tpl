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
			$("*[rel='"+trigged+"']").toggle();		
			$("h2",this).css("background-position","right -25px");
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
	{t}Translation to{/t}
		{assign var=object_translated_lang value=$object_translation.lang|default:''}
		<select style="font-size:1.2em;" name="data[translation_lang]" id="main_lang">
			{foreach key=val item=label from=$conf->langOptions name=langfe}
			{if empty($object_translated_lang)}
				{if !in_array($val,$object_master_langs)}
				<option value="{$val}">{$label}</option>
				{/if}
			{else}
				{if $val==$object_translated_lang}<option value="{$val}" selected="selected">{$label}</option>{/if}
			{/if}
			{/foreach}
		</select>

		<hr />
		<label>{t}Status{/t}</label>:
		<input type="radio" name="data[LangText][0][text]" {if !empty($object_translation) && ($object_translation.status=='on')}checked="checked" {/if}value="on"/>ON
		<input type="radio" name="data[LangText][0][text]" {if !empty($object_translation) && ($object_translation.status=='off')}checked="checked" {/if}value="off"/>OFF
		<input type="radio" name="data[LangText][0][text]" {if !empty($object_translation) && ($object_translation.status=='draft')}checked="checked" {/if}value="draft"/>DRAFT
		<input type="radio" name="data[LangText][0][text]" {if empty($object_translation) || ($object_translation.status=='required')}checked="checked" {/if}value="required"/>TO DO
		<input type="hidden" name="data[LangText][0][name]" value="status"/>
		{if !empty($object_translation.id.status)}<input type="hidden" name="data[LangText][0][id]" value="{$object_translation.id.status}"/>{/if}

	</fieldset>



	<div class="tab2"><h2>{t}Title{/t}</h2></div>
	<fieldset rel="title">
		<label>{t}Title{/t}</label><br />
		<input type="text" id="title" name="data[LangText][1][text]" value="{if !empty($object_translation.title)}{$object_translation.title}{/if}"/><br />
		<input type="hidden" name="data[LangText][1][name]" value="title"/>
		{if !empty($object_translation.id.title)}<input type="hidden" name="data[LangText][1][id]" value="{$object_translation.id.title}"/>{/if}
		{if !empty($object_master.description)}
		<label>{t}Description{/t}</label><br />
		<textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name="data[LangText][2][text]">{if !empty($object_translation.description)}{$object_translation.description}{/if}</textarea>
		<input type="hidden" name="data[LangText][2][name]" value="description"/>
		{if !empty($object_translation.id.description)}<input type="hidden" name="data[LangText][2][id]" value="{$object_translation.id.description}"/>{/if}
		{/if}
	</fieldset>

	{if !empty($object_master.abstract) || !empty($object_master.body)}
	<div class="tab2"><h2>{t}Long Text{/t}</h2></div>

	<fieldset rel="long_desc_langs_container">
		{if !empty($object_master.abstract)}
		<label>{t}Short text{/t}:</label><br />
		<textarea name="data[LangText][3][text]" style="height:200px" class="mce">{if !empty($object_translation.abstract)}{$object_translation.abstract}{/if}</textarea>
		<input type="hidden" name="data[LangText][3][name]" value="abstract"/>
		{if !empty($object_translation.id.abstract)}<input type="hidden" name="data[LangText][3][id]" value="{$object_translation.id.abstract}"/>{/if}
		<br />
		{/if}
		{if !empty($object_master.body)}
		<label>{t}Long text{/t}:</label><br />
		<textarea name="data[LangText][4][text]" style="height:400px" class="mce">{if !empty($object_translation.body)}{$object_translation.body}{/if}</textarea>
		<input type="hidden" name="data[LangText][4][name]" value="body"/>
		{if !empty($object_translation.id.body)}<input type="hidden" name="data[LangText][4][id]" value="{$object_translation.id.body}"/>{/if}
		{/if}
	</fieldset>
	{/if}

TODO: commentati "multimedia descriptions" e "advanced properties" - decidere se implementare o no
{*
	<div class="tab2"><h2>{t}multimedia descriptions{/t}</h2></div>
    <fieldset rel="multimedia">
        <table style="margin-left:-10px; margin-bottom:20px;" border="0" cellpadding="0" cellspacing="2">
        <tr>
            <td>
                <a href="" target="_blank">
                    <img src="/img/thumb.jpg" style='width:100px; height:100px; border:5px solid white; margin-bottom:0px;'   />
                </a>
            </td>
            <td>
                <label>titolo</label>
                <input type="text" style="width:210px !important" name="" value="" />
                <label>dida</label>
                <textarea style="height:38px; width:210px !important" name=""></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <a href="" target="_blank">
                    <img src="/img/thumb2.jpg" style='width:100px; height:100px; border:5px solid white; margin-bottom:0px;'   />
                </a>
            </td>
            <td>
                <label>titolo</label>
                <input type="text" style="width:210px !important" name="" value="" />
                <label>dida</label>
                <textarea style="height:38px; width:210px !important" name=""></textarea>
            </td>
        </tr>
        </table>
    </fieldset>

    
    <div class="tab2"><h2>{t}advanced properties{/t}</h2></div>
    <fieldset rel="advancedproperties">
    <table class="bordered">
        <tr>
            <th>{t}created on{/t}:</th>
            <td>{$smarty.now|date_format:"%d-%m-%Y  | %H:%M:%S"}</td>
        </tr>
        <tr>
            <th>{t}last modified on{/t}:</th>
            <td>{$smarty.now|date_format:"%d-%m-%Y | %H:%M:%S"}</td>
        </tr>
        <tr>
            <th>{t}created by{/t}:</th>
            <td></td>
        </tr>
        <tr>
            <th>{t}last modified by{/t}:</th>
            <td></td>
        </tr>
    </table>
    </fieldset>

*}

</div>




<div class="mainhalf disabled">
	
<div class="tab2"><h2>{t}Properties{/t}</h2></div>
	<fieldset rel="properties">
	{t}Master language{/t}:
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
		<label>{t}Title{/t}</label><br />
		<input type="text" id="title" name="" value="{$object_master.title}" /><br />
		{if !empty($object_master.description)}
		<label>{t}Description{/t}</label><br />
		<textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name="">{$object_master.description}</textarea>
		{/if}
	</fieldset>

	{if !empty($object_master.abstract) || !empty($object_master.body)}
	<div class="tab2"><h2>{t}Long Text{/t}</h2></div>

	<fieldset rel="long_desc_langs_container">
		{if !empty($object_master.abstract)}
		<label>{t}Short text{/t}:</label><br />
		<textarea name="" style="height:200px" class="mce">{$object_master.abstract}</textarea>
		<br />
		{/if}
		{if !empty($object_master.body)}
		<label>{t}Long text{/t}:</label><br />
		<textarea name="" style="height:400px" class="mce">{$object_master.body}</textarea>
		{/if}
	</fieldset>
	{/if}


TODO: commentati "multimedia descriptions" e "advanced properties" - decidere se implementare o no
{*
	<div class="tab2"><h2>{t}multimedia descriptions{/t}</h2></div>
    <fieldset rel="multimedia">
        <table style="margin-left:-10px; margin-bottom:20px;" border="0" cellpadding="0" cellspacing="2">
        <tr>
            <td>
                <a href="" target="_blank">
                    <img src="/img/thumb.jpg" style='width:100px; height:100px; border:5px solid white; margin-bottom:0px;'   />
                </a>
            </td>
            <td>
                <label>titolo</label>
                <input type="text" style="width:210px !important" name="" value="" />
                <label>dida</label>
                <textarea style="height:38px; width:210px !important" name=""></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <a href="" target="_blank">
                    <img src="/img/thumb2.jpg" style='width:100px; height:100px; border:5px solid white; margin-bottom:0px;'   />
                </a>
            </td>
            <td>
                <label>titolo</label>
                <input type="text" style="width:210px !important" name="" value="wqwqwqwqwq qww" />
                <label>dida</label>
                <textarea style="height:38px; width:210px !important" name=""></textarea>
            </td>
        </tr>
        </table>
    </fieldset>
    
    <div class="tab2"><h2>{t}advanced properties{/t}</h2></div>
    <fieldset rel="advancedproperties">
    
    <table class="bordered">
        <tr>
            <th>{t}created on{/t}:</th>
            <td>{$smarty.now|date_format:"%d-%m-%Y  | %H:%M:%S"}</td>
        </tr>
        <tr>
            <th>{t}last modified on{/t}:</th>
            <td>{$smarty.now|date_format:"%d-%m-%Y | %H:%M:%S"}</td>
        </tr>
        <tr>
            <th>{t}created by{/t}:</th>
            <td></td>
        </tr>
        <tr>
            <th>{t}last modified by{/t}:</th>
            <td></td>
        </tr>
    </table>
        
    </fieldset>
    
</div>
*}

</div>

</form>