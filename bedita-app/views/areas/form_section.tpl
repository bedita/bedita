<script type="text/javascript">
<!--
var urlDelete = "{$html->url('deleteSection/')}" ;
var messageDel = "{t}Are you sure that you want to delete the section?{/t}" ;

{literal}
var langs = {
{/literal}
	{foreach name=i from=$conf->langOptions key=lang item=label}
	"{$lang}":	"{$label}" {if !($smarty.foreach.i.last)},{/if}
	{/foreach}
{literal}
} ;

var validate = null ;

$.validator.setDefaults({ 
	success: function(label) {
		// set &nbsp; as text for IE
		label.html("&nbsp;").addClass("checked");
	}
});

$(document).ready(function(){
	$("#updateForm").validate(); 
	$("#delBEObject").submitConfirm({
		action: urlDelete,
		message: messageDel
	});
});

{/literal}
//-->
</script>
<div id="containerPage">
<form action="{$html->url('/areas/saveSection')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<fieldset>
	<input type="hidden" name="data[id]" value="{$section.id|default:''}" />
	{if isset($parent_id)}<input type="hidden" name="data[parent_id]" value="{$parent_id}" />{/if}
</fieldset>
{include file="../pages/form_header.tpl"}
<div class="blockForm" id="errorForm"></div>
{include file="../pages/form_container_properties.tpl"}
{include file="../pages/form_tree.tpl" excludedSubTreeId=$section.id}
{include file="../pages/form_custom_properties.tpl" el=$section}
{include file="../pages/form_permissions.tpl" el=$section recursion=true}
</form>
</div>