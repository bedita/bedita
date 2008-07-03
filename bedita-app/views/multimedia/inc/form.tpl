{*
** multimedia form template
** @author ChannelWeb srl
*}


{$javascript->link("jquery/jquery.form")}
{$javascript->link("jquery/jquery.autogrow")}


<form action="{$html->url('/multimedia/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />
<input  type="hidden" name="data[object_type_id]" value="{$object.object_type_id|default:''}" />


{include file="../common_inc/form_properties.tpl" doctype=false publication=false}
{include file="../common_inc/form_title_subtitle.tpl"}
{include file="../common_inc/form_file.tpl"}
{include file="../common_inc/form_tags.tpl"}
{include file="../common_inc/form_custom_properties.tpl" el=$object}
{include file="../common_inc/form_permissions.tpl" el=$object recursion=true}

</form>
