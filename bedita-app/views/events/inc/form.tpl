

{include file="../common_inc/form_properties.tpl" doctype=false comments=true}
{include file="../common_inc/form_subtitle_desc.tpl"}
{include file="../common_inc/form_calendar_dates.tpl"}
{include file="../common_inc/form_categories.tpl"}
{include file="../common_inc/form_tree.tpl"}
{include file="../common_inc/form_long_desc_lang.tpl"}
{include file="../common_inc/form_file_list.tpl" containerId='attachContainer' relation='attach' title='Attachments' items=$attach}
{include file="../common_inc/form_assoc_objects.tpl"}
{include file="../common_inc/form_custom_properties.tpl" el=$object}
{include file="../common_inc/form_permissions.tpl" el=$object recursion=true}<