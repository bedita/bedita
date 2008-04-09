{*
** galleries form template
** @author ChannelWeb srl
*}

<script type="text/javascript">
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
	$("#updateform", false).validate({
		ignore: ".priority"
	});
	// submit delete
	$("#delBEObject").submitConfirm({
		{/literal}
		action: "{$html->url('delete/')}",
		message: "{t}Are you sure that you want to delete the gallery?{/t}"
		{literal}
	});
	$('div.tabsContainer > ul').tabs();
	$('div.tabsContainer > ul > li > a').changeActiveTabs();
});
{/literal}
</script>
{assign var=objIndex value=0}
<div id="containerPage">
<form action="{$html->url('/galleries/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />
{include file="../pages/form_header.tpl"}
<div class="blockForm" id="errorForm"></div>
{include file="../pages/form_properties.tpl" publication=false}
{include file="../pages/form_subtitle_desc.tpl"}
{include file="../pages/form_file_list.tpl" containerId='multimediaContainer' collection="true" relation='attach' title='Multimedia' items=$multimedia}
{include file="../pages/form_custom_properties.tpl" el=$object}
{include file="../pages/form_permissions.tpl" el=$object recursion=true}
</form>
</div>