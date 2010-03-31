{*
** multimedia view template
*}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}

<script type="text/javascript">
{if !empty($object.path)}
{literal}

    $(document).ready(function(){
		openAtStart("#multimediaitem");
    });
{/literal}
{else}
{literal}

    $(document).ready(function(){
		openAtStart("#title,#mediatypes");
    });
{/literal}
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
