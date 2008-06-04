{*
** galleries form template
** @author ChannelWeb srl
*}


{include file="../common_inc/form_title_subtitle.tpl"}
{include file="../common_inc/form_properties.tpl" publication=false}

{*include file="../pages/form_file_list.tpl" 
containerId='multimediaContainer' 
collection="true" relation='attach' title='Multimedia' items=$multimedia*}

{include file="../common_inc/form_file_listNEW.tpl" 
containerId='multimediaContainer' 
collection="true" relation='attach' title='Multimedia' items=$multimedia}


{include file="../common_inc/form_advanced_properties.tpl" el=$object}
{include file="../common_inc/form_custom_properties.tpl" el=$object}
{include file="../common_inc/form_permissions.tpl" el=$object recursion=true}

</form>
