{include file="../pages/form_common_js.tpl"}

<div id="containerPage">
<form action="{$html->url('/events/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<fieldset><input  type="hidden" name="data[id]" value="{$object.id|default:''}"/></fieldset>
{include file="../pages/form_header.tpl"}
<div class="blockForm" id="errorForm"></div>
{include file="../pages/form_properties.tpl" doctype=false comments=true}
{include file="../pages/form_subtitle_desc.tpl"}
{include file="event_dates.tpl"}
{include file="../pages/form_categories.tpl"}
{include file="../pages/form_tree.tpl"}
{include file="../pages/form_long_desc_lang.tpl"}
{include file="../pages/form_custom_properties.tpl" el=$object}
{include file="../pages/form_permissions.tpl" el=$object recursion=true}
</form>
</div>