{$javascript->link("jquery/jquery.tablesorter.min")}

{assign_associative var="params" itemType="attachments" items=$bedita_items relation=attach}
{$view->element('form_multimedia_assoc', $params)}