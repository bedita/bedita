{*
** form form template
*}

<form action="{$html->url('/questionnaires/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
<input type="hidden" name="data[object_type_id]" value="{$conf->objectTypes.questionnaire.id}"/>

	{$view->element('form_title_subtitle')}

	{include file="./inc/form_list_questions.tpl" object_type_id=$conf->objectTypes.questionnaire.id}

	{assign_associative var="params" comments=false}
	{$view->element('form_properties', $params)}
	
	{$view->element('form_categories')}
	
	{$view->element('form_tree')}
	
	{assign_associative var="params" object_type_id=$conf->objectTypes.document.id}
	{$view->element('form_assoc_objects', $params)}	
	
	{$view->element('form_tags')}
		
	{$view->element('form_translations')}

	{assign_associative var="params" el=$object}
	{$view->element('form_advanced_properties', $params)}
	
	{$view->element('form_custom_properties')}
	
	{assign_associative var="params" el=$object recursion=true}
	{$view->element('form_permissions', $params)}

</form>