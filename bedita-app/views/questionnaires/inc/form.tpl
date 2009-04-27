{*
** form form template
*}


	{include file="../common_inc/form_title_subtitle.tpl"}

	{include file="./inc/form_list_questions.tpl" object_type_id=$conf->objectTypes.questionnaire.id}
	

	{include file="../common_inc/form_properties.tpl" doctype=false comments=false}
	
	{include file="../common_inc/form_assoc_objects.tpl" object_type_id=$conf->objectTypes.document.id}	
	
	{include file="../common_inc/form_translations.tpl"}

	{include file="../common_inc/form_advanced_properties.tpl" el=$object}
	
	{include file="../common_inc/form_custom_properties.tpl"}
	
	{include file="../common_inc/form_permissions.tpl" el=$object recursion=true}
