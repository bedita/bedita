{*
** document form template
*}

<form action="{$html->url('/news/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

	{include file="../common_inc/form_title_subtitle.tpl"}

	{include file="../common_inc/form_properties.tpl" comments=true}
	
	{include file="../common_inc/form_tree.tpl"}
	
	{include file="../common_inc/form_categories.tpl"}
	
	{include file="../common_inc/form_long_desc_lang.tpl"}
	
	{include file="../common_inc/form_file_list.tpl" containerId='attachContainer' collection="true" relation='attach' title='Attachments'}

	{include file="../common_inc/form_tags.tpl"}
	
	{include file="../common_inc/form_links.tpl"}
			
	{*include file="../common_inc/form_geotag.tpl"*}
	
	{include file="../common_inc/form_translations.tpl"}
	
	{include file="../common_inc/form_assoc_objects.tpl" object_type_id=$conf->objectTypes.shortnews.id}
	
	{include file="../common_inc/form_advanced_properties.tpl" el=$object}
	
	{include file="../common_inc/form_custom_properties.tpl"}
	
	{include file="../common_inc/form_permissions.tpl" el=$object recursion=true}

</form>