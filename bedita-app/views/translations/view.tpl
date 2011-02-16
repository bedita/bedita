{*
** translations view template
*}

{$html->script("jquery/ui/jquery.ui.datepicker", false)}

{if ($conf->mce|default:true)}
	
	{$html->script("tiny_mce/tiny_mce", false)}
	{$html->script("tiny_mce/tiny_mce_default_init", false)}


{elseif ($conf->wymeditor|default:true)}

	{$html->script("wymeditor/jquery.wymeditor.pack", false)}
	{$html->script("wymeditor/wymeditor_default_init", false)}

{/if}


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


{literal}
<script type="text/javascript">
$(document).ready(function(){
	openAtStart("#ttitle,#tlong_desc_langs_container");
	
	$(".tab2").click(function () {	
			var trigged = $(this).next().attr("rel") ;
			//$(this).BEtabstoggle();
			$("*[rel='"+trigged+"']").prev(".tab2").BEtabstoggle();
	});
	//$('textarea.autogrowarea').css("line-height","1.2em").autogrow();
});
</script>
{/literal}

{literal}
<script language="javascript" type="text/javascript">

tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	editor_selector : "mcet",
	plugins : "safari,pagebreak,paste,fullscreen",

	// Theme options
	//theme_advanced_buttons1 : "bold,italic, | ,formatselect,bullist, | ,link,unlink,pastetext,pasteword, | ,charmap,fullscreen",
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough, | ,formatselect,bullist,numlist, hr, link,unlink",
	theme_advanced_buttons2 : "pastetext,pasteword, | ,removeformat,charmap,code,fullscreen",
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

{$view->element('form_common_js')}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

<div class="head">
	{if !empty($object_translation.title)}<h1>{$object_translation.title|default:'<i>[no title]</i>'}</h1>{/if}
	{t}translation of{/t}
	<h1 style="margin-top:0px">{$object_master.title|default:'<i>[no title]</i>'}</h1>

</div>

{assign var=objIndex value=0}


{include file="inc/menucommands.tpl" fixed = true}


<div class="mainfull" style="width:700px; padding:0px;">	

	{include file="inc/form.tpl"}

</div>


{*$view->element('menuright')*}