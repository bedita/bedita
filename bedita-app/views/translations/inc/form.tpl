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
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

<div class="mainhalf">
		
	<div class="tab2"><h2>{t}Properties{/t}</h2></div>
	<fieldset rel="properties">
	Traduzione in
		<select style="font-size:1.2em;" name="eventoLang" id="eventoLang">
			<option value="">--</option>
			<option label="English" value="EN" selected="selected">English</option>
			<option label="Espa&ntilde;ol" value="ES">Espa&ntilde;ol</option>
			<option label="Deutsch" value="DE">Deutsch</option>
			<option label="Fran&ccedil;ais" value="FR">Fran&ccedil;ais</option>
			<option label="" value=""></option>
			
			<option label="Abkhazian" value="AB">Abkhazian</option>
			<option label="Afar" value="AA">Afar</option>
			<option label="Afrikaans" value="AF">Afrikaans</option>
			<option label="Albanian" value="SQ">Albanian</option>
			
			<option label="Yoruba" value="YO">Yoruba</option>
			<option label="Zhuang; Chuang" value="ZA">Zhuang; Chuang</option>
			<option label="Zulu" value="ZU">Zulu</option>
		</select>	
	
		<hr />
		<label>status</label>:
		<input type="radio" name="status" checked value="on">ON
		<input type="radio" name="status"  value="off">OFF
		<input type="radio" name="status"  value="draft">DRAFT
	    <input type="radio" name="status"  value="required">TO DO
	</fieldset>
	        
	        
	
	<div class="tab2"><h2>{t}Title{/t}</h2></div>
	<fieldset rel="title">
		<label>{t}Title{/t}</label><br />
		<input type="text" id="title" name="data[title]" value="" /><br />
		<label>{t}Description{/t}</label><br />
		<textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name="data[description]"></textarea>
	</fieldset>
	
	
	<div class="tab2"><h2>{t}Long Text{/t}</h2></div>
	
	<fieldset rel="long_desc_langs_container">
		
		<label>{t}Short text{/t}:</label><br />
		<textarea name="data[abstract]" style="height:200px" class="mce"></textarea>
		<br />
		<label>{t}Long text{/t}:</label><br />
		<textarea name="data[body]" style="height:400px" class="mce">wqqw</textarea>
			
	</fieldset>
	

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

</div>




<div class="mainhalf disabled">
	
<div class="tab2"><h2>{t}Properties{/t}</h2></div>
	<fieldset rel="properties">
	Lingua originale:
		<select disabled style="font-size:1.2em;" id="eventoLang">
			<option label="Afrikaans" value="AF">Afrikaans</option>
		</select>	
		<hr />
		<label>status</label>:
		<input disabled type="radio" checked value="on">ON
		<input disabled type="radio" value="off">OFF
		<input disabled type="radio" value="draft">DRAFT
	</fieldset>
	        
	        
	
	<div class="tab2"><h2>{t}Original Title{/t}</h2></div>
	<fieldset rel="title">
		<label>{t}Title{/t}</label><br />
		<input type="text" id="title" name="" value="" /><br />
		<label>{t}Description{/t}</label><br />
		<textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name=""></textarea>
	</fieldset>
	
	
	<div class="tab2"><h2>{t}Long Text{/t}</h2></div>
	
	<fieldset rel="long_desc_langs_container">
		
		<label>{t}Short text{/t}:</label><br />
		<textarea name="" style="height:200px" class="mce"></textarea>
		<br />
		<label>{t}Long text{/t}:</label><br />
		<textarea name="" style="height:400px" class="mce"></textarea>
			
	</fieldset>
	
	
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


</form>