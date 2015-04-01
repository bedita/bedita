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

{include file = './inc/menuleft.tpl'}

<div class="head">
	<h1>{if !empty($object)}{$object.title|escape|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>
	{if !empty($object.uri)}
	{if (substr($object.uri,0,7) == 'http://') or (substr($object.uri,0,8) == 'https://')}
        {assign var="uri" value=$object.uri}
    {else}
        {assign_concat var="uri" 1=$conf->mediaUrl 2=$object.uri}
    {/if}
	<a class="BEbutton" href="{$uri}" target="_blank" style="vertical-align: bottom;">{t}view{/t}</a>
	<a class="BEbutton" href="{$uri}" target="_blank" download style="vertical-align: bottom;">{t}download{/t}</a>
	{/if}
</div>

{include file = './inc/menucommands.tpl' fixed = true}

<div class="main">

	{include file = './inc/form.tpl'}	

</div>

{$view->element('menuright')}