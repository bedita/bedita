
{$view->element('modulesmenu')}

{include file = './inc/menuleft.tpl'}

{include file = './inc/menucommands.tpl'}

{$view->element('toolbar')}

<div class="mainfull">

    {$view->element('filters', [
        'options' => [
            'type' => true
        ]
    ])}

    {$view->element('list_objects', [
        'bulk_tree' => false,
        'bulk_hide_delete' => true
    ])}
    
</div>