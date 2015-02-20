{*
** addressbook form template
*}


<form action="{$html->url('/addressbook/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
{$beForm->csrf()}
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
	
	{include file="../inc/form_card_details.tpl"}
	
	{include file="../inc/form_properties.tpl"}

	{assign_associative var="params" object_type_id=$conf->objectTypes.card.id}
	{$view->element('form_assoc_objects', $params)}

	{include file="../inc/form_newsletter_subscription.tpl"}
	
	{$view->element('form_categories')}
	
	{$view->element('form_tree')}

	{$view->element('form_tags')}
	
	{$view->element('form_geotag')}
	
	{$view->element('form_translations')}
	
	{include file="../inc/form_advanced_properties.tpl" el=$object}
	
	{$view->element('form_custom_properties')}

	{$view->element('form_previews')}

	{$view->element('form_versions')}

</form>

	{$view->element('form_print')}