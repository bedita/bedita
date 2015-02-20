{*
** newsletter form template
*}

<form action="{$html->url('/newsletter/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
{$beForm->csrf()}
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
	
	
	{include file="inc/form_contents_newsletter.tpl"}

	{include file="inc/form_invoice.tpl"}
		
	{assign_associative var="params" el=$object}
	{$view->element('form_advanced_properties', $params)}

	{$view->element('form_versions')}
</form>

