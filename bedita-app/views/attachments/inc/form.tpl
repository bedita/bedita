<script type="text/javascript">
<!--
{literal}
var langs = {{/literal}{foreach name=i from=$conf->langOptions key=lang item=label}"{$lang}":   "{$label}" {if !($smarty.foreach.i.last)},{/if}{/foreach}{literal}} ;
var validate = null ;
$(document).ready(function(){
	$("#updateForm").validate();
    $('div.tabsContainer > ul').tabs();
	$('div.tabsContainer > ul > li > a').changeActiveTabs();
});
{/literal}
//-->
</script>

{$javascript->link("jquery/jquery.form")}

{include file="../common_inc/form_common_js.tpl"}

<form action="{$html->url('/attachments/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">

<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />
<input  type="hidden" name="data[object_type_id]" value="{$object.object_type_id|default:''}" />


{include file="../common_inc/form_properties.tpl" doctype=false publication=false}

{include file="../common_inc/form_file.tpl"}

{include file="../common_inc/form_title_subtitle.tpl"}


{include file="../common_inc/form_custom_properties.tpl" el=$object}

{include file="../common_inc/form_permissions.tpl" el=$object recursion=true}


</form>

