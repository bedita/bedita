{*
** document form template
*}

<form action="{$html->url('/documents/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

	{$view->element('form_title_subtitle')}

	{assign_associative var="params" addshorttext=$conf->addshorttext|default:true height=500}
	{$view->element('form_textbody', $params)}

	{assign_associative var="params" containerId='multimediaContainer' collection="true" relation='attach' title='Multimedia'}
	{$view->element('form_file_list', $params)}
			
	{$view->element('form_previews')}
	
	{assign_associative var="params" comments=true}
	{$view->element('form_properties', $params)}
	
	{$view->element('form_tree')}
	
	{$view->element('form_categories')}
	
	{$view->element('form_tags')}
	
	{$view->element('form_links')}
			
	{$view->element('form_geotag')}
	
	{$view->element('form_translations')}
	
	{assign_associative var="params" object_type_id=$conf->objectTypes.document.id}
	{$view->element('form_assoc_objects', $params)}
	
	{assign_associative var="params" el=$object}
	{$view->element('form_advanced_properties', $params)}
	
	{$view->element('form_custom_properties')}
	
	{assign_associative var="params" el=$object recursion=true}
	{$view->element('form_permissions', $params)}
	
	{$view->element('form_versions')}
	
	{$view->element('form_notes')}
	

</form>

	{$view->element('form_print')}

	
