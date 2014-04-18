{$html->script("libs/jquery/plugins/jquery.tablesorter.min")}

{$view->element('form_multimedia_assoc', [
    'itemType' => 'attachments',
    'items' => $bedita_items,
    'relation' => 'attach'
])}