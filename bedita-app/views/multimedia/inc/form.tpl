{*
** multimedia form template
** @author ChannelWeb srl
*}

<form action="{$html->url('/multimedia/saveAjax')}" enctype="multipart/form-data" method="post" name="updateForm" id="updateForm" class="cmxform">
<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />
<input  type="hidden" name="data[object_type_id]" value="{$object.object_type_id|default:''}" />
<input  type="hidden" name="data[uri]" value="{$object.uri}" />
<input  type="hidden" name="data[name]" value="{$object.name}" />
<input  type="hidden" name="data[mime_type]" value="{$object.mime_type}" />

{assign_associative var="params" publication=false comments=true}
{$view->element('form_properties', $params)}

{$view->element('form_title_subtitle')}

{$view->element('form_tree')}

{include file="inc/form_mediatype.tpl"}

{if $object.Category == "spreadsheet" or $object.Category == "text" or $object.Category == "formula"}

	{$view->element('form_textbody')}

{elseif $object.Category == "application" or $object.Category == "video" or $object.Category == "audio"}

	{$view->element('form_external_audiovideo')}

{/if}

{$view->element('form_file')}

{if !empty($object)}
	{include file="inc/list_relationships.tpl"}
{/if}

{$view->element('form_tags')}

{$view->element('form_geotag')}
	
{$view->element('form_translations')}

{$view->element('form_advanced_properties')}

{$view->element('form_custom_properties')}

{$view->element('form_permissions',[
	'el'=>$object,
	'recursion'=>true
])}

{$view->element('form_file_exif')}

{$view->element('form_notes')}

</form>
	{$view->element('form_versions')}
	{$view->element('form_print')}