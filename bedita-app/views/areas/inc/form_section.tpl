{include file="inc/form_properties.tpl" fixed=false}

{$view->element('form_categories')}

{$view->element('form_tags', ['object' => $object|default:null])}

{$view->element('form_geotag')}

{$view->element('form_assoc_objects',['object_type_id' => {$conf->objectTypes.section.id}])}

{$view->element('form_translations', ['object' => $object|default:null])}

{$view->element('form_advanced_properties', ['object' => $object|default:null])}

{$view->element('form_custom_properties')}

{$view->element('form_permissions', ['object' => $object|default:null, 'recursion' => true])}

{$view->element('form_versions')}