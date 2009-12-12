{*
** bibliography form template
*}

<form action="{$html->url('/bibliographies/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

	{include file="../common_inc/form_title_subtitle.tpl"}

	{include file="../common_inc/form_properties.tpl" comments=true}
	
	{include file="../common_inc/form_textbody.tpl" height=200}

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
	
</form>


	{include file="../common_inc/form_print.tpl"}
