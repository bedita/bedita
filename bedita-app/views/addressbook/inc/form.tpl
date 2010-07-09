{*
** addressbook form template
*}


<form action="{$html->url('/addressbook/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
	
	{include file="./inc/form_card_details.tpl"}

	{include file="./inc/form_properties.tpl"}
		
	{include file="./inc/form_newsletter_subscription.tpl"}
	
	{$view->element('form_categories')}
	
	{$view->element('form_tree')}
	
	{assign_associative var="params" containerId='multimediaContainer' collection="true" relation='attach' title='Multimedia'}
	{$view->element('form_file_list', $params)}

	{$view->element('form_tags')}
	
	{$view->element('form_geotag')}
	
	{$view->element('form_translations')}
	
	{assign_associative var="params" object_type_id=$conf->objectTypes.card.id}
	{$view->element('form_assoc_objects', $params)}
	
	{include file="./inc/form_advanced_properties.tpl" el=$object}
	
	{$view->element('form_custom_properties')}

	{$view->element('form_history')}

</form>


	{$view->element('form_print')}