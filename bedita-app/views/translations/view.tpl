{*
** translations view template
*}

{$html->script("libs/jquery/ui/jquery.ui.datepicker.min", false)}
{if $currLang != "eng"}
	{$html->script("libs/jquery/ui/i18n/jquery.ui.datepicker-$currLang2.min.js", false)}
{/if}

{$view->element('texteditor')}

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

<script type="text/javascript">
$(document).ready(function() {
    $('.tab2').click(function () {
        var trigged = $(this).next().attr('rel');
        $('*[rel="' + trigged + '"]').prev('.tab2').BEtabstoggle();
    });
    openAtStart('#ttitle, #tlong_desc_langs_container');
});
</script>

{$view->element('form_common_js')}

{$view->element('modulesmenu')}

{include file= './inc/menuleft.tpl'}

<div class="head">
	{if !empty($object_translation.title)}
	<h2 style="margin:5px 0 5px 0">{$object_translation.title|escape|default:'<i>[no title]</i>'}</h2>{/if}
	{t}translation of{/t}
	<h2 style="margin:5px 0 0 0">{$object_master.title|escape|default:'<i>[no title]</i>'}</h2>

</div>

{assign var=objIndex value=0}


{include file="inc/menucommands.tpl" fixed=true}


<div class="mainfull" style="width:700px; padding:0px;">

	{include file="inc/form.tpl"}

</div>


{*$view->element('menuright')*}