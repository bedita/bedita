{*
** translations view template
*}

{$this->Html->script("jquery/ui/jquery.ui.datepicker", false)}

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