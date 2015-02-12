{*
** event form template
*}

<form action="{$html->url('/events/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
{$beForm->csrf()}
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

	{$view->element('form_title_subtitle')}

	{$view->element('form_previews')}
	
	{$view->element('form_calendar_dates')}
	
	{assign_associative var="params" title="Event Locations"}
	{$view->element('form_geotag', $params)}

	{include file="../inc/form_promoter.tpl" comments=true}
	
	{include file="../inc/form_properties.tpl" comments=true}

	{$view->element('form_tree')}
	
	{$view->element('form_categories')}
	
	{assign_associative var="params" height=400}
	{$view->element('form_textbody', $params)}

	{$view->element('form_tags')}

	{$view->element('form_links')}
	
	{$view->element('form_translations')}
	
	{assign_associative var="params" object_type_id=$conf->objectTypes.event.id}
	{$view->element('form_assoc_objects', $params)}
	
	{assign_associative var="params" el=$object}
	{$view->element('form_advanced_properties', $params)}
	
	{$view->element('form_custom_properties')}

	{assign_associative var="params" el=$object recursion=true}
	{$view->element('form_permissions', $params)}

	{$view->element('form_versions')}
	
</form>

	{$view->element('form_print')}