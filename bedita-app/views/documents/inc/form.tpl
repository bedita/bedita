{*
** document form template
*}


{include file="../common_inc/form_common_js.tpl"}


<form action="{$html->url('/documents/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

	{include file="../common_inc/form_title_subtitle.tpl"}

	{include file="../common_inc/form_properties.tpl" doctype=false comments=true}
	
	{include file="../common_inc/form_tree.tpl"}
	
	{include file="../common_inc/form_long_desc_lang.tpl"}
	
	{include file="../common_inc/form_file_list.tpl" containerId='multimediaContainer' collection="true" relation='attach' title='Multimedia'}

	{include file="../common_inc/form_tags.tpl"}
	
	{include file="../common_inc/form_links.tpl"}
			
	{include file="../common_inc/form_geotag.tpl"}
	
	{include file="../common_inc/form_translations.tpl"}
	
	{include file="../common_inc/form_assoc_objects.tpl"}
	
	{include file="../common_inc/form_advanced_properties.tpl" el=$object}
	
	{include file="../common_inc/form_custom_properties.tpl" el=$object}
	
	{include file="../common_inc/form_permissions.tpl" el=$object recursion=true}
	


	

{*include file="../common_inc/form_lang_version.tpl"*}
{*include file="../common_inc/form_galleries.tpl"*}

