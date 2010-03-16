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

{assign_associative var="params" publication=false}
{$view->element('form_properties', $params)}

{$view->element('form_title_subtitle')}

{$view->element('form_tree')}

{include file="inc/form_mediatype.tpl"}

{$view->element('form_file')}

{if !empty($object)}
	{include file="inc/list_relationships.tpl"}
{/if}

{$view->element('form_tags')}

{$view->element('form_translations')}

{$view->element('form_advanced_properties')}

{$view->element('form_custom_properties')}

{$view->element('form_file_exif')}

</form>

	{$view->element('form_print')}