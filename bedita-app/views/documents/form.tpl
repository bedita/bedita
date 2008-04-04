{include file="../pages/form_common_js.tpl"}
{assign var=objIndex value=0}
<div id="containerPage">
	<form action="{$html->url('/documents/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
	<fieldset><input  type="hidden" name="data[id]" value="{$object.id|default:''}"/></fieldset>
	{include file="../pages/form_header.tpl"}
	<div class="blockForm" id="errorForm"></div>
	{include file="../pages/form_properties.tpl" doctype=false comments=true}
	{include file="../pages/form_subtitle_desc.tpl"}
	{include file="../pages/form_tree.tpl"}
	{*include file="../pages/form_lang_version.tpl"*}
	{*include file="../pages/form_longdesc.tpl"*}
	{include file="../pages/form_long_desc_lang.tpl"}
	{include file="../pages/form_file_list.tpl" containerId='attachContainer' relation='attach' title='Attachments' items=$attach}
	{include file="../pages/form_galleries.tpl"}
	{include file="../pages/form_assoc_objects.tpl"}
	{foreach from=$moduleList item="mod"}
		{if $mod.label == "tags"}{include file="../pages/form_tags.tpl"}{/if}
	{/foreach}
	{include file="../pages/form_custom_properties.tpl" el=$object}
	{include file="../pages/form_permissions.tpl" el=$object recursion=true}
	</form>
</div>