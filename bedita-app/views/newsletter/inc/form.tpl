{*
** newsletter form template
*}


{include file="../common_inc/form_common_js.tpl"}


<form action="{$html->url('/newsletter/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

	{include file="inc/form_contents_newsletter.tpl"}

	{include file="inc/form_invoice.tpl"}
		
	{include file="../common_inc/form_advanced_properties.tpl" el=$object}
	
	

</form>
	


