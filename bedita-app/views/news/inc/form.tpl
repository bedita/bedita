{*
** document form template
*}

<form action="{$html->url('/news/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
{$beForm->csrf()}
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

	{$view->element('form_title_subtitle')}

	{$view->element('form_previews')}
	
	{assign_associative var="params" comments=true}
	{$view->element('form_properties', $params)}
	
	{$view->element('form_tree')}
	
	{$view->element('form_categories')}
	
	{$view->element('form_textbody')}
	
	{$view->element('form_tags')}
	
	{$view->element('form_links')}
			
	{*$view->element('form_geotag')*}
	
	{$view->element('form_translations')}
	
	{assign_associative var="params" object_type_id=$conf->objectTypes.short_news.id}
	{$view->element('form_assoc_objects', $params)}
	
	{assign_associative var="params" el=$object}
	{$view->element('form_advanced_properties', $params)}
	
	{$view->element('form_custom_properties')}
	
	{assign_associative var="params" el=$object recursion=true}
	{$view->element('form_permissions', $params)}

	{$view->element('form_versions')}

</form>

	{$view->element('form_print')}