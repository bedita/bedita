{*
** document view template
*}
{$html->css("ui.datepicker", null, null, false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.selectboxes.pack", false)}
{$javascript->link("jquery/ui/jquery.ui.sortable", true)}
{$javascript->link("jquery/ui/jquery.ui.datepicker", false)}
{if $currLang != "eng"}
{$javascript->link("jquery/ui/i18n/ui.datepicker-$currLang.js", false)}
{/if}

{literal}
<script type="text/javascript">
    $(document).ready(function(){
		openAtStart("#title,#long_desc_langs_container");
    });
</script>
{/literal}

{assign_associative var="params" currObjectTypeId=$conf->objectTypes.document.id}
{$view->element('form_common_js', $params)}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

<div class="head">
    <h1>{if !empty($object)}{$object.title|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>
</div>

{assign var=objIndex value=0}

{include file="inc/menucommands.tpl" fixed = true}


<div class="main">
    {include file="inc/form.tpl"}
</div>

{$view->element('menuright')}

