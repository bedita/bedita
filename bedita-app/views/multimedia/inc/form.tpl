{*
** multimedia form template
** @author ChannelWeb srl
*}

<form action="{$html->url('/multimedia/save')}" enctype="multipart/form-data" method="post" name="updateForm" id="updateForm" class="cmxform">
<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />
<input  type="hidden" name="data[object_type_id]" value="{$object.object_type_id|default:''}" />
<input  type="hidden" name="data[path]" value="{$object.path}" />
<input  type="hidden" name="data[name]" value="{$object.name}" />
<input  type="hidden" name="data[mime_type]" value="{$object.mime_type}" />

{include file="../common_inc/form_properties.tpl" publication=false}

{include file="../common_inc/form_title_subtitle.tpl"}

{include file="inc/form_mediatype.tpl"}

{include file="../common_inc/form_file.tpl"}

{if !empty($object)}
	{include file="inc/list_relationships.tpl"}
{/if}

{include file="../common_inc/form_tags.tpl"}

{include file="../common_inc/form_advanced_properties.tpl"}

{include file="../common_inc/form_custom_properties.tpl"}

{include file="../common_inc/form_file_exif.tpl"}

</form>