{*
** multimedia view template
*}
{$html->script("libs/jquery/plugins/jquery.form", false)}
{$html->script("libs/jquery/ui/jquery.ui.datepicker.min", false)}
{if $currLang != "eng"}
    {$html->script("libs/jquery/ui/i18n/jquery.ui.datepicker-$currLang2.min.js", false)}
{/if}

<script type="text/javascript">
{if !empty($object.uri)}
    $(document).ready(function(){
		openAtStart("#multimediaitem");
    });
{else}
    $(document).ready(function(){
		openAtStart("#title,#mediatypes");
    });
{/if}
</script>

{$view->element('form_common_js')}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

<div class="head">
	<h1>{if !empty($object)}{$object.title|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>
</div>

{include file="inc/menucommands.tpl" fixed=true}

<div class="main">

	{include file="inc/form.tpl"}	

</div>

{$view->element('menuright')}