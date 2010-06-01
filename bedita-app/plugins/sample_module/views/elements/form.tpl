{*
** general form for content object type (i.e. document, short_news, ...)
*}
{assign_concat var="formUrl" 0="/" 1=$currentModule.url 2="/save"}
<form action="{$html->url($formUrl)}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

	{$view->element("form_title_subtitle")}

	{assign_associative var="params" comments=true}
	{$view->element("form_properties", $params)}
	
	{$view->element("form_tree")}
	
	{$view->element('form_textbody')}
	
	{assign_associative var="params" containerId='attachContainer' collection="true" relation='attach' title='Attachments'}
	{$view->element("form_file_list", $params)}

	{$view->element("form_tags")}
	
	{$view->element("form_links")}			
	
	{$view->element("form_translations")}
	
	{assign_associative var="params" object_type_id=$objectTypeId}
	{$view->element("form_assoc_objects", $params)}
	
	{assign_associative var="params" el=$object}
	{$view->element("form_advanced_properties", $params)}
	
	{$view->element("form_custom_properties")}
	
	{assign_associative var="params" el=$object recursion=true}
	{$view->element("form_permissions", $params)}

</form>

	{$view->element("form_print")}
