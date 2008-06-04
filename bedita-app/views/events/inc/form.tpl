

{include file="../pages/form_properties.tpl" doctype=false comments=true}
{include file="../pages/form_subtitle_desc.tpl"}
{include file="../pages/form_calendar_dates.tpl"}
{include file="../pages/form_categories.tpl"}
{include file="../pages/form_tree.tpl"}
{include file="../pages/form_long_desc_lang.tpl"}
{include file="../pages/form_file_list.tpl" containerId='attachContainer' relation='attach' title='Attachments' items=$attach}
{include file="../pages/form_assoc_objects.tpl"}
{include file="../pages/form_custom_properties.tpl" el=$object}
{include file="../pages/form_permissions.tpl" el=$object recursion=true}<