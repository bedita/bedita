{*
** newsletter form template template :-)
*}

{if !empty($pub)}
	{assign_concat var="templateCSS" 0=$pub.public_url 1="/css/" 2=$conf->newsletterCss}
{else}
	{assign var="templateCSS" value=$html->url('/css/newsletter.css')}
{/if}


{if ($conf->mce|default:true)}
	{$javascript->link("tiny_mce/tiny_mce")}
{literal}
<script language="javascript" type="text/javascript">

function initializeTinyMCE(cssPath) {
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		editor_selector : "mce",
		plugins : "safari,pagebreak,paste,fullscreen,bedita",
	
		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough, | ,formatselect,bullist,numlist, hr, | ,link,unlink,pastetext,pasteword, | ,removeformat,charmap,code,fullscreen",
		theme_advanced_buttons2 : "be_content",
		theme_advanced_buttons3 : "", 
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		//theme_advanced_statusbar_location : "bottom",
		//theme_advanced_resizing : true,
		theme_advanced_blockformats : "p,h1,h2,h3,h4,blockquote,address",
		width : "450",
		// Example content CSS (should be your site CSS)
		//content_css : "http://beditafront.lcl:8081/css/htmleditor.css",
	    content_css : cssPath,
		relative_urls : false,
		convert_urls : false,
	    remove_script_host : false,
		document_base_url : "/"
		
	});
}

initializeTinyMCE("{/literal}{$templateCSS}{literal}");

$(document).ready(function() {
	$("#changeCss").change(function() {
		mce = tinyMCE.get("htmltextarea");
		mce.remove();
		cssBaseUrl = $(this).find("option:selected").attr("rel");
		if (cssBaseUrl === undefined)
			cssPath = "{/literal}{$html->url('/css/newsletter.css')}{literal}";
		else
			cssPath =  cssBaseUrl + "/css/{/literal}{$conf->newsletterCss}{literal}";

		initializeTinyMCE(cssPath);	
	});
});
	
</script>
{/literal}
{/if}

<form action="{$html->url('/newsletter/saveTemplate')}" method="post" name="updateForm" id="updateForm" class="">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

	<div class="tab"><h2>{t}Configuration{/t}</h2></div>
	
	<fieldset id="details">
		
	<table class="bordered" style="width:100%">
		<tr>
			<td>publication</td>
			<td>
				{if !empty($tree)}
				<select name="data[destination][]" id="changeCss">
				<option value="">--</option>
				{foreach from=$tree item="t"}
					<option rel="{$t.public_url|default:null}" value="{$t.id}"{if $t.id == $pub.id|default:null} selected{/if}>{$t.title}</option>
				{/foreach}
				</select>
				{/if}
			</td>
		</tr>
		<tr>
			<td>{t}title{/t}</td>
			<td>
				<input type="text" 	name="data[title]" value="{$object.title|default:null}" />
			</td>
		</tr>
		<tr>
			<td>{t}sender email{/t}</td>
			<td><input type="text" name="data[sender]" value="{$object.sender|default:null}"/></td>
		</tr>
		<tr>
			<td>{t}reply to{/t}</td>
			<td><input type="text" name="data[reply_to]" value="{$object.reply_to|default:null}"/></td>
		</tr>
		<tr>
			<td>{t}bounce to email{/t}</td>
			<td><input type="text" name="data[bounce_to]" value="{$object.bounce_to|default:null}" /></td>
		</tr>
		<tr>
			<td>{t}priority{/t}</td>
			<td><input type="text" value="{$object.priority|default:null}" /></td>
		</tr>
		<tr>
			<td>{t}signature{/t}:</td>
			<td>	
				<textarea name="data[signature]" style="width:340px" class="autogrowarea">{$object.signature|default:null}</textarea>
			</td>
		</tr>
		<tr>
			<td>{t}privacy disclaimer{/t}:</td>
			<td>	
				<textarea name="data[privacy_disclaimer]" style="width:340px" class="autogrowarea">{$object.privacy_disclaimer|default:null}</textarea>
			</td>
		</tr>
	</table>
	</fieldset>
	

	<div class="tab"><h2>{t}Message template{/t}</h2></div>
	
	<fieldset id="body">
	
	<table class="htab">
		<td rel="html">HTML version</td>
		<td rel="txt">PLAIN TEXT version</td>
	</table>
	
	<div class="htabcontainer" id="templatebody">
		
		<div class="htabcontent" id="html">
			<textarea id="htmltextarea" name="data[body]" style="height:300px" class="mce">
			{strip}
			{if !empty($object.body)}
				{$object.body}
			{else}
				<h1>[$newsletterTitle]</h1>
				<hr />
				<!--bedita content block-->
				<img src="/img/{$imagenotizia|default:'px.gif'}" 
				style="float:left; background-color: silver; width:96px; height:96px; margin:0px 20px 20px 0px;" />
				<h2>[$title]</h2>
				<h3>[$description]</h3>
				[$body|truncate:128]
				<!--bedita content block-->
				<hr style="clear:both" />
				[$signature]
				<hr />
				{t}To unsubscribe{/t} <a href="[$signoutlink]">[$signoutlink]</a>
				<p>
				[$privacydisclaimer]
				</p>
			{/if}
			{/strip}
			</textarea>
		</div>
		
		<div class="htabcontent" id="txt">
			<textarea name="data[abstract]" style="height:300px; border:1px solid silver; width:450px" class="autogrowarea">
{if !empty($object.abstract)}
{$object.abstract}
{else}
[$newsletterTitle]
________________________________
<!--bedita content block-->
[$title]
[$description]
[$body|truncate:128]
<!--bedita content block-->
---------------
[$signature]
________________________________
{t}To unsubscribe{/t} [$signoutlink]
________________________________
[$privacydisclaimer]
{/if}
			</textarea>

		</div>
		
	</div>
	

	
	</fieldset>

</form>

