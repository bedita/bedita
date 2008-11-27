{*
** bibliography form template
*}


	{include file="../common_inc/form_title_subtitle.tpl"}

	{include file="../common_inc/form_properties.tpl" doctype=false comments=true}
	
	{include file="../common_inc/form_short_desc_lang.tpl"}

	{include file="../common_inc/form_biblioitems.tpl"}
	
	{include file="../common_inc/form_tree.tpl"}
	
	{include file="../common_inc/form_file_list.tpl" containerId='multimediaContainer' collection="true" relation='attach' title='Multimedia'}

	{include file="../common_inc/form_tags.tpl"}
	
	{include file="../common_inc/form_links.tpl"}
	
	{include file="../common_inc/form_translations.tpl"}
	
	{include file="../common_inc/form_assoc_objects.tpl" object_type_id=$conf->objectTypes.bibliography.id}
	
	{include file="../common_inc/form_advanced_properties.tpl" el=$object}
	
	{include file="../common_inc/form_custom_properties.tpl"}
	
	{include file="../common_inc/form_permissions.tpl" el=$object recursion=true}
	



