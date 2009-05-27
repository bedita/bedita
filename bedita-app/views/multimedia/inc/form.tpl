{*
** multimedia form template
** @author ChannelWeb srl
*}


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
